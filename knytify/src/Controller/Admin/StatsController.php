<?php

namespace Knytify\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use Knytify\Entity\Admin\RegistrationEntity;
use Configuration;

class StatsController extends FrameworkBundleAdminController
{
    public function indexAction(Request $request)
    {
        $api_key = Configuration::get('KNYTIFY_API_KEY', false);
        if ($api_key == false) {
            return $this->redirectToRoute('ps_controller_getting_started');
        }

        $router = SymfonyContainer::getInstance()->get('router');


        $params = [
            'error' => null,
            'configuration_link' => $router->generate('ps_controller_configuration')
        ];

        $service = $this->get('Knytify\Service\Admin\KnytifyClient');
        $service->setApiKey($api_key);

        $success = $service->getStats();

        if ($success) {
            $resp = $service->getResponse();
            $params['stats'] = $resp;
            $params['stats_recap'] = $this->parseStatsRecap($resp);
            // $params['stats_evolution'] = $this->parseStatsEvolution($resp);
            // $params['stats_percentage'] = $this->parseStatsPercentage($resp);
        } else {
            $params['error'] = $service->getError();
        }

        $success = $success && $service->getStatsUtm();
        if ($success) {
            $resp = $service->getResponse();
            $params['stats_utm'] = $this->parseStatsUtm($resp);
        } else {
            $params['error'] = $service->getError();
        }

        return $this->render(
            '@Modules/knytify/views/templates/admin/stats.html.twig',
            $params
        );
    }

    private function parseStatsRecap(array $resp)
    {
        $stats = [
            "num_sessions_this_month" => 0,
            "num_fraud_this_month" => 0,
            "num_sessions_prev_month" => 0,
            "num_fraud_prev_month" => 0,
        ];

        foreach ($resp["current"]["y"]["sessions"] as $num) {
            $stats["num_sessions_this_month"] += $num;
        }
        foreach ($resp["previous"]["y"]["sessions"] as $num) {
            $stats["num_sessions_prev_month"] += $num;
        }

        foreach ($resp["current"]["y"]["positive_0.5"] as $num) {
            $stats["num_fraud_this_month"] += $num;
        }
        foreach ($resp["previous"]["y"]["positive_0.5"] as $num) {
            $stats["num_fraud_prev_month"] += $num;
        }
        return $stats;
    }



    private function parseStatsUtm(array $resp)
    {

        $score_by_label = [];
        $num_sessions_by_label = [];

        foreach ($resp as $row) {
            $key = empty($row['utm_source']) ? 'Direct' : $row['utm_source'];

            if (!empty($row['utm_medium'])) {
                $key .= "_" . $row['utm_medium'];
            }
            if (!empty($row['utm_name'])) {
                $key .= "_" . $row['utm_name'];
            } else if (!empty($row['utm_id'])) {
                $key .= "_" . $row['utm_id'];
            }

            if (!array_key_Exists($key, $score_by_label)) {
                $score_by_label[$key] = 0;
                $num_sessions_by_label[$key] = 0;
            }

            $score = !empty($row['avg']) ? $row['avg'] : $row['score'];

            $score_by_label[$key] += $score;
            $num_sessions_by_label[$key] += 1;
        }

        ksort($score_by_label);

        $values = [];
        foreach ($score_by_label as $k => $v) {
            $values[] = $v / $num_sessions_by_label[$k];
        }

        return [
            "labels" => array_keys($score_by_label),
            "values" => $values
        ];
    }
}
