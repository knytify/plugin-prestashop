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
            'configuration_link' => $router->generate('ps_controller_configuration')
        ];

        $service = $this->get('Knytify\Service\Admin\KnytifyClient');
        $service->setApiKey($api_key);
        $success = $service->getStats();



        if ($success) {
            $resp = $service->getResponse();

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

            $params['stats'] = $stats;

        } else {
            $params['error'] = $service->getError();
        }

        return $this->render(
            '@Modules/knytify/views/templates/admin/stats.html.twig',
            $params
        );
    }
}