<?php
namespace Knytify\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use Configuration;
use Knytify\Entity\Admin\ConfigurationEntity;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ConfigurationController extends FrameworkBundleAdminController
{
    public function indexAction(Request $request)
    {
        $router = SymfonyContainer::getInstance()->get('router');

        $config = new ConfigurationEntity();

        if (!$request->isMethod('post')) {
            $config->setEnabled(Configuration::get('KNYTIFY_ENABLED', false));
        }

        $form = $this->createFormBuilder($config)
            ->add('enabled', SwitchType::class)
            ->add('save', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            Configuration::updateValue('KNYTIFY_ENABLED', $config->getEnabled());
        }

        $params = [
            'form' => $form->createView(),
            'getting_started_link' => $router->generate('ps_controller_getting_started'),
        ];

        return $this->render(
            '@Modules/knytify/views/templates/admin/configuration.html.twig',
            $params
        );
    }
}