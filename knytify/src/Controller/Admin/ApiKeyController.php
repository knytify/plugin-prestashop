<?php

namespace Knytify\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Knytify\Entity\Admin\ApiKeyEntity;

class ApiKeyController extends FrameworkBundleAdminController
{
    public function indexAction(Request $request)
    {
        $login = new ApiKeyEntity();

        $form = $this->createFormBuilder($login)
            ->add('api_key', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Associate account'])
            ->getForm();

        return $this->render(
            '@Modules/knytify/views/templates/admin/api_key.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }
}