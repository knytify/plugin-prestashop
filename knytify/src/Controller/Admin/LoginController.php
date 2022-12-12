<?php

namespace Knytify\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use Knytify\Entity\Admin\LoginEntity;
use Configuration;

class LoginController extends FrameworkBundleAdminController
{
    public function indexAction(Request $request)
    {
        $router = SymfonyContainer::getInstance()->get('router');

        $login = new LoginEntity();

        $form = $this->createFormBuilder($login)
            ->add('username', TextType::class, ["label" => "E-mail"])
            ->add('password', PasswordType::class)
            ->add('save', SubmitType::class, ['label' => 'Associate account'])
            ->getForm();

        $params = [
            'form' => $form->createView(),
            'getting_started_link' => $router->generate('ps_controller_getting_started'),
            'configuration_link' => $router->generate('ps_controller_configuration')
        ];

        $form->handleRequest($request);

        if ($form->isSubmitted()) { //  && $form->isValid()
            $entity = $form->getData();
            $service = $this->get('Knytify\Service\Admin\KnytifyClient');
            $success = $service->login($entity);
            if ($success) {
                $api_key = $service->getResponse()['api_key'];
                if (!empty($api_key)) {
                    Configuration::updateValue('KNYTIFY_API_KEY', $api_key);
                    Configuration::updateValue('KNYTIFY_ENABLED', true);
                } else {
                    $params['error'] = "An error happened retrieving the api key";
                    $success = false;
                }
            } else {
                $params['error'] = $service->getError();
            }
            $params['success'] = $success;
        }

        return $this->render(
            '@Modules/knytify/views/templates/admin/login.html.twig',
            $params
        );
    }
}