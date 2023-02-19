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
            $this->api_key = "8-1OjTyU-KxUoYP9ZBWJn7P8MSfT7QAEDHfhl5DTa";
            // return new Response('missing_api_key', 401);
        }

        $service = $this->get('Knytify\Service\Admin\KnytifyClient');
        $service->setApiKey($this->api_key);

        $success = $service->{$method}($request);

        if ($success) {
            return new JsonResponse($service->getResponse());
        } else {
            return new Response($service->getError(), $service->getStatusCode());
        }
    }
}
