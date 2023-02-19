<?php

namespace Knytify\Controller\Admin\Rest;

use Symfony\Component\HttpFoundation\Request;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Configuration;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;


class BaseController extends FrameworkBundleAdminController
{

    protected ?string $api_key = null;

    public function __construct()
    {
        $this->api_key = Configuration::get('KNYTIFY_API_KEY', null);
    }


    protected function knytify_request(string $method, Request $request)
    {
        if (empty($this->api_key)) {
            return new Response('Missing Api Key', 401);
        }

        $service = $this->get('Knytify\Service\Admin\KnytifyClient');
        $service->setApiKey($this->api_key);

        $success = $service->{$method}($request);

        if ($success) {
            $resp = $service->getResponse();
            return new JsonResponse($resp);
        } else {
            return new Response($service->getError(), 500);
        }
    }
}
