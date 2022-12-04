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
        $this->bootstrap = true; // compliant

        parent::__construct();

        $this->displayName = $this->l('Knytify - Fraud Protection');
        $this->description = $this->l('Advanced traffic quality evaluation, fraud detection & prevention');
        $this->confirmUninstall = $this->l('Uninstalling this plugin will stop prevention against low quality traffic. Do you wish to proceed uninstalling it?');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        Configuration::updateValue('KNYTIFY_ENABLED', false);
        return parent::install() &&
            $this->registerHook('header') && $this->registerHook('displayBackOfficeHeader');
    }

    public function uninstall()
    {
        Configuration::deleteByName('KNYTIFY_ENABLED');
        Configuration::deleteByName('KNYTIFY_API_KEY');
        return parent::uninstall();
    }

    public function getContent()
    {

        $api_key = Configuration::get('api_key');
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
        // $this->context->controller->addCSS($this->_path . '/views/css/front.css');
    }
}