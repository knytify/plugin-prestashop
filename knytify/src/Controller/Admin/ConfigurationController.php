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
        $router = SymfonyContainer::getInstance()->get('router');
        $module = Module::getInstanceByName('knytify');

        /**
         * PS Account
         *
         * Sets window.contextPsAccounts
         *
         * If associated, window.contextPsAccounts.user holds the e-mail instead of null,
         * and $accountsService->isAccountLinked() returns true.
         *
         * https://docs.cloud.prestashop.com/4-account-and-billing/#backend
         */
        $accountsFacade = $module->getService('ps_accounts.facade');
        $accountsService = $accountsFacade->getPsAccountsService();
        Media::addJsDef([
            'contextPsAccounts' => $accountsFacade->getPsAccountsPresenter()
                ->present($module->name),
        ]);

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

        $params = [
            // "knytify" is passed to the window, to be used on the Vue app.
            'knytify' => [
                'base_url' => rtrim(Context::getContext()->shop->getBaseURL(true), '/'), // _PS_BASE_URL_ fails to present https:// sometimes.
                // Another way: see getShopUrl in ps_accounts module
                'links' =>  [
                    'configuration_set' => $router->generate('ps_knytify_configuration_set'),
                    'configuration_get' => $router->generate('ps_knytify_configuration_get'),
                    'configuration_script_set' => $router->generate('ps_knytify_configuration_script_set'),
                    'configuration_script_get' => $router->generate('ps_knytify_configuration_script_get'),
                    'stats' => $router->generate('ps_knytify_stats'),
                ]
            ],

            // Vue app params
            'pathApp' => $module->getPathUri() . 'views/js/vue/js/app.js',
            'chunkVendor' => $module->getPathUri() . 'views/js/vue/js/chunk-vendors.js',

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
        /*
         * Gets the general plugin configuration
         */
        return new JsonResponse([
            'enabled' => Configuration::get('KNYTIFY_ENABLED', false)
        ]);
    }

    public function setConfig(Request $request)
    {
        /**
         * Sets the general plugin configuration
         */
        $data = json_decode($request->getContent(), true);

        if (isset($data['enabled'])) {
            $enabled = !empty($data['enabled']) ? '1' : '0';
        } else {
            $enabled = null;
        }

        if ($enabled !== null) {
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
