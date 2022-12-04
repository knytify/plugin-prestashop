<?php



namespace Knytify\Controller\Admin;


use Symfony\Component\HttpFoundation\Request;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


use Knytify\Entity\Admin\RegistrationEntity;

class RegistrationController extends FrameworkBundleAdminController
{
    public function indexAction(Request $request)
    {
        $entity = new RegistrationEntity();

        $form = $this->createFormBuilder($entity)
            ->add('username', TextType::class)
            ->add('password', PasswordType::class)
            ->add('passwordCheck', PasswordType::class)
            ->add('save', SubmitType::class, ['label' => 'Register account'])
            ->getForm();

        return $this->render(
            '@Modules/knytify/views/templates/admin/registration.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }
}