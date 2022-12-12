<?php

namespace Knytify\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Knytify\Entity\Admin\ApiKeyEntity;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use Configuration;

class ApiKeyController extends FrameworkBundleAdminController
{
    public function indexAction(Request $request)
    {
        $router = SymfonyContainer::getInstance()->get('router');

        $entity = new ApiKeyEntity();

        $form = $this->createFormBuilder($entity)
            ->add('api_key', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Associate account'])
            ->getForm();

        $params = [
            'form' => $form->createView(),
            'getting_started_link' => $router->generate('ps_controller_getting_started'),
            'configuration_link' => $router->generate('ps_controller_configuration')
        ];

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $entity = $form->getData();
            $service = $this->get('Knytify\Service\Admin\KnytifyClient');
            $service->setApiKey($entity->getApiKey());
            $success = $service->getUser();
            if ($success) {
                Configuration::updateValue('KNYTIFY_API_KEY', $entity->getApiKey());
                Configuration::updateValue('KNYTIFY_ENABLED', true);
            } else {
                $params['error'] = $service->getError();
            }
            $params['success'] = $success;
        }

        return $this->render(
            '@Modules/knytify/views/templates/admin/api_key.html.twig',
            $params
        );
    }
}