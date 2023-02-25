<?php
/**
* Knytify Fraud Protection Plugin for Prestashop
* Copyright (C) 2023  Knytify SARL-s
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*
* @author       Knytify SARL <inquiry@knytify.com>
* @copyright    2022-2023 Knytify SARL-s
* @license      GPL-3.0-or-later (https://www.gnu.org/licenses/gpl-3.0.html)
**/

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

        $psAccounts = $this->loadPsAccounts();
        $this->loadPsBilling();


        $params = [
            'knytify' => [
                // _PS_BASE_URL_ does not always show the correct HTTP/HTTPS protocol.
                // Another way would be to do like in the getShopUrl method in ps_accounts module
                "base_url" => rtrim(Context::getContext()->shop->getBaseURL(true), "/"),
                "links" =>  $this->getLinks(),
                "page" => $this->page
            ],

            // psAccountsvue
            'urlAccountsCdn' => $psAccounts->getAccountsCdn(),

            // Vue app params
            'pathApp' => $this->module->getPathUri() . "views/js/vue/js/app.js",
            'chunkVendor' => $this->module->getPathUri() . "views/js/vue/js/chunk-vendors.js",

        ];

        return $this->render(
            '@Modules/knytify/views/templates/admin/app.html.twig',
            $params
        );
    }

    private function getLinks()
    {

        /**
         * The REST routes used by the app
         */
        $links = [
            /**
             * Routes to ensure the API key of Knytify is correct.
             */
            'account' => $this->router->generate('ps_knytify_user'),
            'account_login' => $this->router->generate('ps_knytify_user_login'),
            'account_setup' => $this->router->generate('ps_knytify_user_setup'),

            /**
             * Link for the configuration page. (=app default page)
             */
            'knytify_configuration' => $this->router->generate('knytify_app'),

            /**
             * Routes to configure the plugin
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
            'stats' => $this->router->generate('knytify_stats'),
            'stats_recap' => $this->router->generate('ps_knytify_stats_recap'),
            'stats_advanced' => $this->router->generate('ps_knytify_stats_advanced'),
        ];
        return $links;
    }

    private function loadPsBilling()
    {
        /**
         * PS Billing
         *
         * Sets window.psBillingContext
         *
         * If subscribed, billingContext.user.email holds the e-mail.
         *
         * https://docs.cloud.prestashop.com/4-account-and-billing/#backend
         */
        $billingFacade = $this->module->getService('ps_billings.facade');

        Media::addJsDef($billingFacade->present([
            'logo' => $this->module->getLocalPath() . 'logo.png',
            'tosLink' => 'https://www.knytify.com/terms.html',
            'privacyLink' => 'https://www.knytify.com/privacy.html',
            'emailSupport' => 'inquiry@knytify.com',
        ]));
    }

    private function loadPsAccounts()
    {
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
                ->present($this->module->name),
        ]);
        return $accountsService;
    }
}
