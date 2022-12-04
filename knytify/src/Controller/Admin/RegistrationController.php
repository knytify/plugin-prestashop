<?php

namespace Knytify\Controller\Admin;

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;

class RegistrationController extends FrameworkBundleAdminController
{
    public function indexAction()
    {
        return $this->render(
            '@Modules/knytify/views/templates/admin/registration.html.twig',
        );
    }
}