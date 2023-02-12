<?php

namespace Knytify\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use Configuration;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

use Module;
use Media;
use Context;

class ConfigurationController extends FrameworkBundleAdminController
{
    public function indexAction(Request $request)
    {
        $module = Module::getInstanceByName('knytify');
        $router = SymfonyContainer::getInstance()->get('router');


        /**
         * PS Account
         *
         * This component will set information about the linked account in window.contextPsAccounts
         *
         * To check whethert he account is associated, window.contextPsAccounts.user has the e-mail or null.
         * To do the same in the backend, $accountsService->isAccountLinked();
         */
        $accountsFacade = $module->getService('ps_accounts.facade');
        $accountsService = $accountsFacade->getPsAccountsService();
        Media::addJsDef([
            'contextPsAccounts' => $accountsFacade->getPsAccountsPresenter()
                ->present($module->name),
        ]);


        /**
         * PS Billing
         * https://docs.cloud.prestashop.com/4-account-and-billing/#backend
         */
        $billingFacade = $module->getService('ps_billings.facade');
        $partnerLogo = $module->getPathUri() . "logo.png";

        // Billing
        Media::addJsDef($billingFacade->present([
            'logo' => $partnerLogo,
            'tosLink' => 'https://www.knytify.com/terms.html',
            'privacyLink' => 'https://www.knytify.com/privacy.html',
            'emailSupport' => 'inquiry@knytify.com',
        ]));


        $params = [
            // "knytify" is passed to the window, to be used on the Vue app.
            'knytify' => [
                "base_url" => rtrim(Context::getContext()->shop->getBaseURL(true), "/"), // _PS_BASE_URL_ fails to present https:// sometimes.
                // Another way: see getShopUrl in ps_accounts module
                "links" =>  [
                    'configuration_set' => $router->generate('ps_knytify_configuration_set'),
                    'configuration_get' => $router->generate('ps_knytify_configuration_get'),
                    'configuration_script_set' => $router->generate('ps_knytify_configuration_script_set'),
                    'configuration_script_get' => $router->generate('ps_knytify_configuration_script_get'),
                    'stats' => $router->generate('ps_knytify_stats'),
                ]
            ],
            // Vue app params
            'pathApp' => $module->getPathUri() . "views/js/vue/js/app.js",
            'chunkVendor' => $module->getPathUri() . "views/js/vue/js/chunk-vendors.js",
            // PS Accounts
            'urlAccountsCdn' => $accountsService->getAccountsCdn(),
        ];

        return $this->render(
            '@Modules/knytify/views/templates/admin/app.html.twig',
            $params
        );
    }

    public function getConfig(Request $request)
    {
        /**
         * Gets the general plugin configuration
         */
        return new JsonResponse([
            "enabled" => Configuration::get('KNYTIFY_ENABLED', false)
        ]);
    }

    public function setConfig(Request $request)
    {
        /**
         * Sets the general plugin configuration
         */
        $data = json_decode($request->getContent(), true);

        if (isset($data['enabled'])) {
            $enabled = !empty($data['enabled']) ? "1" : "0";
        } else {
            $enabled = null;
        }

        if ($enabled !== NULL) {
            Configuration::updateValue('KNYTIFY_ENABLED', $enabled);
        }

        return new Response('Updated', 201);
    }

    public function getScriptConfig(Request $request)
    {
        /**
         * Gets the Knytify JS Tag configuration
         */
        $config = Configuration::get('KNYTIFY_SCRIPT_CONFIG', null);
        $config = empty($config) ? [] : json_decode($config, true);
        return new JsonResponse($config);
    }

    public function setScriptConfig(Request $request)
    {
        /**
         * Updates the Knytify JS Tag configuration
         */
        $data = $request->getContent();
        Configuration::updateValue('KNYTIFY_SCRIPT_CONFIG', $data);
        return new Response('Updated', 201);
    }
}
