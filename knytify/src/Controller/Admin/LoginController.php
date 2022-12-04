<?php

namespace Knytify\Controller\Admin;

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;

class LoginController extends FrameworkBundleAdminController
{
    public function indexAction()
    {
        return $this->render(
            '@Modules/knytify/views/templates/admin/login.html.twig',
        );
    }
}