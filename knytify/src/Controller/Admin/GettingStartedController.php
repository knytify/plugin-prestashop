<?php

namespace Knytify\Controller\Admin;


// use Doctrine\Common\Cache\CacheProvider;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;

use PrestaShop\PrestaShop\Adapter\SymfonyContainer;


class GettingStartedController extends FrameworkBundleAdminController
{
    public function indexAction()
    {
        $router = SymfonyContainer::getInstance()->get('router');

        return $this->render(
            '@Modules/knytify/views/templates/admin/getting_started.html.twig',
            array(
                'layoutTitle' => 'Getting Started with Knytify',
                'login_link' => $router->generate('ps_controller_login'),
                'registration_link' => $router->generate('ps_controller_registration'),
            )
        );
    }
}