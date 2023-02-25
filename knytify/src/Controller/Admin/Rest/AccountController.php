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


namespace Knytify\Controller\Admin\Rest;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Configuration;

class AccountController extends BaseController
{
    protected $ps_billing_service = null;

    public function __construct()
    {
        parent::__construct();

        $this->ps_billing_service = $this->knytify_module->getService('ps_billings.service');
        $this->subscription_email = $this->getSubscriptionEmail();
    }

    public function user(Request $request)
    {
        if (empty($this->subscription_email)) {
            return new Response("missing_subscription_email", 400);
        }

        $incoming_api_key = $request->query->get('api_key', null);

        if (!empty($incoming_api_key)) {
            $this->knytify_client->setApiKey($incoming_api_key);
        }

        if ($this->knytify_client->getUser()) {

            $response = $this->knytify_client->getResponse();

            if (!empty($incoming_api_key)) {
                if ($this->subscription_email != $response["email"]) {
                    return new Response("email_mismatch", 400);
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
        /**
         * If login succeeds, store the api key.
         */

        if (empty($this->subscription_email)) {
            return new Response("missing_subscription_email", 400);
        }

        $input_data = json_decode($request->getContent(), true);

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

        $input_data = json_decode($request->getContent(), true);

        if (empty($this->subscription_email)) {
            return new Response("missing_subscription_email", 400);
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

        $subscription_customer = $this->ps_billing_service->getCurrentCustomer();

        if ($subscription_customer['httpStatus'] !== 200 || empty($subscription_customer["body"]["email"])) {
            return null;
        }

        return $subscription_customer["body"]["email"];
    }
}
