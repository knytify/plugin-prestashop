<?php



namespace Knytify\Controller\Admin;


use Symfony\Component\HttpFoundation\Request;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

// use Knytify\Service\Admin\LoginForm;

use Knytify\Entity\Admin\LoginEntity;

class LoginController extends FrameworkBundleAdminController
{
    public function indexAction(Request $request)
    {
        $login = new LoginEntity();

        $form = $this->createFormBuilder($login)
            ->add('username', TextType::class)
            ->add('password', PasswordType::class)
            ->add('save', SubmitType::class, ['label' => 'Associate account'])
            ->getForm();

        return $this->render(
            '@Modules/knytify/views/templates/admin/login.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }
}