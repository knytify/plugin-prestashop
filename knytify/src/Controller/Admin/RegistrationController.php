<?php



namespace Knytify\Controller\Admin;


use Symfony\Component\HttpFoundation\Request;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Knytify\Entity\Admin\RegistrationEntity;

use Configuration;

class RegistrationController extends FrameworkBundleAdminController
{
    public function indexAction(Request $request)
    {
        $api_key = Configuration::get('KNYTIFY_API_KEY', null);

        if (!empty($api_key)) {
            return $this->redirectToRoute('ps_controller_configuration');
        }

        $entity = new RegistrationEntity();

        $form = $this->createFormBuilder($entity)
            ->add('username', TextType::class)
            ->add('password', PasswordType::class)
            ->add('passwordCheck', PasswordType::class)
            ->add('save', SubmitType::class, ['label' => 'Register account'])
            ->getForm();

        $params = [
            'form' => $form->createView(),
        ];

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entity = $form->getData();
            $service = $this->get('Knytify\Service\Admin\KnytifyClient');
            $success = $service->register($entity);
            $params['success'] = $success;
            if ($success) {
                $api_key = $service->getResponse()['api_key'];
                Configuration::updateValue('KNYTIFY_API_KEY', $api_key);
            } else {
                $params['error'] = $service->getError();
            }
        }

        return $this->render(
            '@Modules/knytify/views/templates/admin/registration.html.twig',
            $params
        );
    }
}