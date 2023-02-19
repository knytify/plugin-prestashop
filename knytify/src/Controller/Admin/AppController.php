<?php

namespace Knytify\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;

use Module;
use Media;
use Context;

class AppController extends FrameworkBundleAdminController
{
    protected ?string $page = "configuration";

    public function indexAction(Request $request)
    {
        $this->router = SymfonyContainer::getInstance()->get('router');
        $this->module = Module::getInstanceByName('knytify');

        $ps_accounts = $this->loadPsAccounts();
        $ps_billing = $this->loadPsBilling();


        $params = [
            'knytify' => [
                // _PS_BASE_URL_ does not always show the correct HTTP/HTTPS protocol.
                // Another way would be to do like in the getShopUrl method in ps_accounts module
                "base_url" => rtrim(Context::getContext()->shop->getBaseURL(true), "/"),
                "links" =>  $this->getLinks(),
                "page" => $this->page
            ],

            // psAccountsvue
            'urlAccountsCdn' => $accountsService->getAccountsCdn(),

            // Vue app params
            'pathApp' => $module->getPathUri() . "views/js/vue/js/app.js",
            'chunkVendor' => $module->getPathUri() . "views/js/vue/js/chunk-vendors.js",

        ];

        return $this->render(
            '@Modules/knytify/views/templates/admin/app.html.twig',
            $params
        );
    }

    private function getLinks() {

        /**
         * The REST routes used by the app
         */
        $links = [
            /**
             * Links to ensure the API key of Knytify is correct.
             */
            'account' => $this->router->generate('ps_knytify_configuration_script_get'),
            'account_login' => $this->router->generate('ps_knytify_configuration_script_get'),
            'account_setup' => $this->router->generate('ps_knytify_configuration_script_set'),

            /**
             * Links to configure the plugin
             */
            'configuration_set' => $this->router->generate('ps_knytify_configuration_set'),
            'configuration_get' => $this->router->generate('ps_knytify_configuration_get'),

            /**
             * Routes to configure the Knytify javascript tag
             */
            'configuration_script_set' => $this->router->generate('ps_knytify_configuration_script_set'),
            'configuration_script_get' => $this->router->generate('ps_knytify_configuration_script_get'),

            /**
             * Link for the stats page.
             */
            'stats' => $this->router->generate('ps_knytify_stats'),
        ];
        return $links;
    }

    private function loadPsBilling() {
        /**
         * PS Billing
         *
         * Sets window.psBillingContext
         *
         * If subscribed, billingContext.user.email holds the e-mail.
         *
         * https://docs.cloud.prestashop.com/4-account-and-billing/#backend
         */
        $billingFacade = $module->getService('ps_billings.facade');
        Media::addJsDef($billingFacade->present([
            'logo' => $module->getLocalPath() . 'logo.png',
            'tosLink' => 'https://www.knytify.com/terms.html',
            'privacyLink' => 'https://www.knytify.com/privacy.html',
            'emailSupport' => 'inquiry@knytify.com',
        ]));

        return $billingFacade
    }

    private function loadPsAccounts() {
        /**
         * PS Account
         *
         * Sets window.contextPsAccounts
         *
         * If associated, window.contextPsAccounts.user holds the e-mail instead of null,
         * and window.psaccountsVue.isOnboardingCompleted() also is useful for this,
         * and $accountsService->isAccountLinked() returns true,
         *
         * https://docs.cloud.prestashop.com/4-account-and-billing/#backend
         */
        $accountsFacade = $this->module->getService('ps_accounts.facade');
        $accountsService = $accountsFacade->getPsAccountsService();
        Media::addJsDef([
            'contextPsAccounts' => $accountsFacade->getPsAccountsPresenter()
                ->present($module->name),
        ]);

        return [
            "facade" => $accountsFacade,
            "service" => $accountsService
        ]
    }
}
