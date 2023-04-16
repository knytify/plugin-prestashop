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


namespace Knytify\Controller\Admin\Rest;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Configuration;

class AccountController extends BaseController
{
    protected $ps_billing_service = null;

    private ?string $subscription_email = null;

    public function __construct()
    {
        parent::__construct();

        $this->ps_billing_service = $this->knytify_module->getService('ps_billings.service');
        $this->subscription_email = $this->getSubscriptionEmail();
    }

    public function user(Request $request)
    {
        if (empty($this->subscription_email)) {
            return new Response('missing_subscription_email', 400);
        }

        $incoming_api_key = $request->query->get('api_key', null);

        if (!empty($incoming_api_key)) {
            $this->knytify_client->setApiKey($incoming_api_key);
        }

        if ($this->knytify_client->getUser()) {
            $response = $this->knytify_client->getResponse();

            if (!empty($incoming_api_key)) {
                if ($this->subscription_email != $response['email']) {
                    return new Response('email_mismatch', 400);
                } else {
                    Configuration::updateValue('KNYTIFY_API_KEY', $incoming_api_key);
                }
            }

            return new JsonResponse($response);
        } else {
            return new Response($this->knytify_client->getError(), $this->knytify_client->getStatusCode());
        }
    }

    public function login(Request $request)
    {
        /*
         * If login succeeds, store the api key.
         */

        if (empty($this->subscription_email)) {
            return new Response('missing_subscription_email', 400);
        }

        $input_data = json_decode($request->getContent());

        $result = $this->knytify_client->login($this->subscription_email, $input_data->password);

        if ($result) {
            $data = $this->knytify_client->getResponse();
            $api_key = $data['api_key'];
            Configuration::updateValue('KNYTIFY_API_KEY', $api_key);

            return new Response();
        } else {
            return new Response($this->knytify_client->getError(), $this->knytify_client->getStatusCode());
        }
    }

    public function setup(Request $request)
    {
        /**
         * If setup succeeds, store the api key.
         */
        $input_data = json_decode($request->getContent());

        if (empty($this->subscription_email)) {
            return new Response('missing_subscription_email', 400);
        }

        $result = $this->knytify_client->setupPassword($this->subscription_email, $input_data->password);

        if ($result) {
            $data = $this->knytify_client->getResponse();
            $api_key = $data['api_key'];
            Configuration::updateValue('KNYTIFY_API_KEY', $api_key);

            return new Response();
        } else {
            return new Response($this->knytify_client->getError(), $this->knytify_client->getStatusCode());
        }
    }

    private function getSubscriptionEmail()
    {
        // https://docs.cloud.prestashop.com/5-prestashop-billing/#edit-the-module-name-php-file

        $subscription = $this->ps_billing_service->getCurrentSubscription();
        if($subscription['httpStatus'] == 404) {
            // Subscription is non existant, therefore no e-mail too
            return null;
        }

        $subscription_customer = $this->ps_billing_service->getCurrentCustomer();

        if ($subscription_customer['httpStatus'] !== 200 || empty($subscription_customer['body']['email'])) {
            return null;
        }

        return $subscription_customer['body']['email'];
    }
}
