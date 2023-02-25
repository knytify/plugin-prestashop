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
