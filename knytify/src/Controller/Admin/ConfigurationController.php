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

class ConfigurationController extends FrameworkBundleAdminController
{
    public function indexAction(Request $request)
    {
        $module = Module::getInstanceByName('knytify');
        $router = SymfonyContainer::getInstance()->get('router');

        $params = [
            // "knytify" is passed to the window, to be used on the Vue app.
            'knytify' => [
                "base_url" => _PS_BASE_URL_,
                "links" =>  [
                    'getting_started_' => $router->generate('ps_knytify_getting_started'),
                    'configuration_set' => $router->generate('ps_knytify_configuration_set'),
                    'configuration_get' => $router->generate('ps_knytify_configuration_get'),
                    'stats' => $router->generate('ps_knytify_stats'),
                ]
            ],
            // Vue app params
            'pathApp' => $module->getPathUri() . "views/js/vue/js/app.js",
            'chunkVendor' => $module->getPathUri() . "views/js/vue/js/chunk-vendors.js"
        ];

        return $this->render(
            '@Modules/knytify/views/templates/admin/app.html.twig',
            $params
        );
    }

    public function getConfig(Request $request)
    {
        return new JsonResponse([
            "enabled" => Configuration::get('KNYTIFY_ENABLED', false)
        ]);
    }

    public function setConfig(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['enabled'])) {
            $enabled = !empty($data['enabled']) ? "1" : "0";
        } else {
            $enabled = null;
        }

        if ($enabled !== NULL) {
            Configuration::updateValue('KNYTIFY_ENABLED', $enabled);
        }

        return new Response();
    }
}
