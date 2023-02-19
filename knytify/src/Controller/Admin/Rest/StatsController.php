<?php

namespace Knytify\Controller\Admin\Rest;

use Symfony\Component\HttpFoundation\Request;
use Configuration;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;


class StatsController extends BaseController
{
    public function recap(Request $request)
    {
        /**
         * Rest proxy to knytify stats/graphs
         */
        return $this->proxy('getStatsRecap', $request);
    }

    public function advanced(Request $request)
    {
        /**
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
