<?php

namespace Knytify\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use Configuration;

class ErrorController extends FrameworkBundleAdminController
{
    public function indexAction(Request $request)
    {
        $router = SymfonyContainer::getInstance()->get('router');

        $error = $request->query->get('error');

        return $this->render(
            '@Modules/knytify/views/templates/admin/error.html.twig',
            [
                'error' => $error,
                'getting_started_link' => $router->generate('ps_controller_getting_started')
            ]
        );
    }
}
