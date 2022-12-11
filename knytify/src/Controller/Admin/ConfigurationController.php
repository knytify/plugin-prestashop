<?php
namespace Knytify\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use Configuration;

class ConfigurationController extends FrameworkBundleAdminController
{
    public function indexAction(Request $request)
    {

        $router = SymfonyContainer::getInstance()->get('router');
        $params = [
            'api_key' => Configuration::get('KNYTIFY_API_KEY'),
            'getting_started_link' => $router->generate('ps_controller_getting_started'),
        ];
        return $this->render(
            '@Modules/knytify/views/templates/admin/configuration.html.twig',
            $params
        );
    }
}