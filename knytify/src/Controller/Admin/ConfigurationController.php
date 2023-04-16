<?php
/**
 * Knytify Fraud Protection Plugin for Prestashop
 * Copyright (C) 2023 Knytify SARL-s
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @author       Knytify SARL <inquiry@knytify.com>
 * @copyright    2022-2023 Knytify SARL
 * @license      MIT License (https://opensource.org/licenses/MIT)
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
