<?php
/**
 *  @author    Jean-François Kener, Aurélien Savart
 *  @copyright 2007-2023 Knytify SARL
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

class Knytify extends Module
{
    public function __construct()
    {
        $this->name = 'knytify';
        $this->tab = 'analytics_stats';
        $this->version = '1.0.0';
        $this->author = 'Knytify';
        $this->need_instance = 1;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Knytify - Fraud Protection');
        $this->description = $this->l('Advanced traffic quality evaluation, fraud detection & prevention');
        $this->confirmUninstall = $this->l('Uninstalling this plugin will stop prevention against low quality traffic. Do you wish to proceed uninstalling it?');
        $this->ps_versions_compliancy = array('min' => '1.7.5', 'max' => '1.9.9');
    }

    public function install()
    {
        Configuration::updateValue('KNYTIFY_ENABLED', false);
        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('displayBackOfficeHeader') &&
            $this->registerHook('displayBeforeBodyClosingTag');
    }

    public function uninstall()
    {
        Configuration::deleteByName('KNYTIFY_ENABLED');
        Configuration::deleteByName('KNYTIFY_API_KEY');
        return parent::uninstall();
    }

    public function getContent()
    {

        if (!function_exists('curl_version')) {
            return $this->render(
                '@Modules/knytify/views/templates/errors/curl_not_supported.html.twig',
            );
        }

        $api_key = Configuration::get('KNYTIFY_API_KEY');
        if (empty($api_key)) {
            Tools::redirectAdmin(
                $this->context->link->getAdminLink('KnytifyGettingStarted')
            );
        }
    }

    public function hookDisplayBackOfficeHeader()
    {
        $this->context->controller->addJS($this->_path . 'views/js/back.js');
        $this->context->controller->addCSS($this->_path . 'views/css/back.css');
    }

    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path . '/views/js/front.js');
    }


    public function hookDisplayBeforeBodyClosingTag()
    {
        if (Configuration::get('KNYTIFY_ENABLED')) {
            return '
            <script src="https://live.knytify.com/tag/main.js"></script>
            <script>
              window.knytify.init();
            </script>';
        } else {
            return '';
        }
    }

}