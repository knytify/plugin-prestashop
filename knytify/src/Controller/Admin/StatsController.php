<?php

namespace Knytify\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use Configuration;
use Knytify\Entity\Admin\ConfigurationEntity;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

use Module;

class StatsController extends FrameworkBundleAdminController
{
    public function indexAction(Request $request)
    {
        $module = Module::getInstanceByName('knytify');
        $router = SymfonyContainer::getInstance()->get('router');

        $params = [
            // "knytify" is passed to the browser window, to be used in the Vue app.
            'knytify' => [
                "base_url" => _PS_BASE_URL_,
                "links" =>  [
                    // Plugin page
                    'configuration' => $router->generate('ps_knytify_configuration'),
                    // Curl Proxies
                    'stats_recap' => $router->generate('ps_knytify_stats_recap'),
                ]
            ],
            // Vue scripts to be injected to the page
            'pathApp' => $module->getPathUri() . "views/js/vue/js/app.js",
            'chunkVendor' => $module->getPathUri() . "views/js/vue/js/chunk-vendors.js"
        ];

        return $this->render(
            '@Modules/knytify/views/templates/admin/app.html.twig',
            $params
        );
    }

    public function recap(Request $request)
    {
        /**
         * Rest proxy to knytify stats/graphs
         */
        return $this->proxy('getStatsRecap', $request);
    }

    public function advanced(Request $request)
    {
        /**
         * Rest proxy to knytify stats/getStatsAdvanced
         */
        return $this->proxy('getStatsAdvanced', $request);
    }

    private function proxy(string $method, Request $request)
    {
        /**
         * Use the curl-based client for knytify
         */
        $api_key = Configuration::get('KNYTIFY_API_KEY', false);
        if (empty($api_key)) {
            return new Response('Missing Api Key', 401);
        }

        $service = $this->get('Knytify\Service\Admin\KnytifyClient');
        $service->setApiKey($api_key);

        $success = $service->{$method}($request);

        if ($success) {
            $resp = $service->getResponse();
            return new JsonResponse($resp);
        } else {
            return new Response($service->getError(), 500);
        }
    }
}
