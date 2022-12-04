<?php



namespace Knytify\Controller\Admin;


use Symfony\Component\HttpFoundation\Request;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;

// use Knytify\Service\Admin\LoginForm;

use Knytify\Entity\Admin\LoginEntity;



class LoginController extends FrameworkBundleAdminController
{
    public function indexAction(Request $request)
    {
        // $form = new LoginForm($this);

        // creates a logindata and gives it some dummy data for this example
        $login = new LoginEntity();

        $form = $this->createFormBuilder($login)
            ->add('username', TextType::class)
            ->add('password', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Log In'])
            ->getForm();

        return $this->render(
            '@Modules/knytify/views/templates/admin/login.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }
}