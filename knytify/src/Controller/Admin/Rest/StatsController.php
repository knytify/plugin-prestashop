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
use Configuration;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class StatsController extends BaseController
{
    public function recap(Request $request)
    {
        /*
         * Rest proxy to knytify stats/graphs
         */
        return $this->proxy('getStatsRecap', $request);
    }

    public function advanced(Request $request)
    {
        /*
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
