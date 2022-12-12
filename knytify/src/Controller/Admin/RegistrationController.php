<?php

namespace Knytify\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use Knytify\Entity\Admin\RegistrationEntity;
use Configuration;

class RegistrationController extends FrameworkBundleAdminController
{
    public function indexAction(Request $request)
    {
        $router = SymfonyContainer::getInstance()->get('router');

        $api_key = Configuration::get('KNYTIFY_API_KEY', null);
        if (!empty($api_key)) {
            // Do not allow to make a new account if you already had one.
            return $this->redirectToRoute('ps_controller_getting_started');
        }

        $entity = new RegistrationEntity();

        $form = $this->createFormBuilder($entity)
            ->add('username', TextType::class, ['label' => "E-mail address"])
            ->add('password', PasswordType::class)
            ->add('passwordCheck', PasswordType::class)
            ->add('save', SubmitType::class, ['label' => 'Register account'])
            ->getForm();

        $params = [
            'form' => $form->createView(),
            'getting_started_link' => $router->generate('ps_controller_getting_started'),
            'configuration_link' => $router->generate('ps_controller_configuration')
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
                Configuration::updateValue('KNYTIFY_ENABLED', true);
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