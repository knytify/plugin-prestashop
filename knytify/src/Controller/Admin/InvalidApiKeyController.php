<?php
namespace Knytify\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use Configuration;

class InvalidApiKey extends FrameworkBundleAdminController
{
    public function indexAction(Request $request)
    {
        Configuration::updateValue('KNYTIFY_ENABLED', false);

        $router = SymfonyContainer::getInstance()->get('router');

        return $this->render(
            '@Modules/knytify/views/templates/error/invalid_api_key.html.twig',
            [
                'getting_started_link' => $router->generate('ps_controller_getting_started')
            ]
        );
    }
}