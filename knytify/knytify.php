<?php

/**
 *  @author    Jean-François Kener
 *  @copyright 2022-2023 Knytify SARL
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
        Configuration::updateValue('KNYTIFY_ENABLED', true);
        return parent::install() &&
            $this->installTab() &&
            $this->registerHook('header') &&
            $this->registerHook('displayBackOfficeHeader') &&
            $this->registerHook('displayBeforeBodyClosingTag');
    }

    public function uninstall()
    {
        return parent::uninstall()
            && $this->uninstallTab();
    }


    public function enable($force_all = false)
    {
        Configuration::updateValue('KNYTIFY_ENABLED', true);
        return parent::enable($force_all)
            && $this->installTab();
    }

    public function disable($force_all = false)
    {
        Configuration::updateValue('KNYTIFY_ENABLED', false);
        return parent::disable($force_all)
            && $this->uninstallTab();
    }

    private function installTab()
    {
        // https://devdocs.prestashop-project.org/1.7/modules/concepts/controllers/admin-controllers/tabs/#manual-tab-insertion
        $tabId = (int) Tab::getIdFromClassName('KnytifyStats');
        if (!$tabId) {
            $tabId = null;
        }
        $tab = new Tab($tabId);
        $tab->active = 1;
        $tab->class_name = 'KnytifyStats';
        $tab->name = array();
        foreach (Language::getLanguages() as $lang) {
            $tab->name[$lang['id_lang']] = "Knytify Stats";
        }
        $tab->id_parent = (int) Tab::getIdFromClassName('AdminStats');
        $tab->module = $this->name;
        return $tab->save();
    }

    private function uninstallTab()
    {
        $tabId = (int) Tab::getIdFromClassName('KnytifyStats');
        if (!$tabId) {
            return true;
        }
        $tab = new Tab($tabId);
        return $tab->delete();
    }


    public function getContent()
    {

        if (!function_exists('curl_version')) {
            return $this->render(
                '@Modules/knytify/views/templates/errors/curl_not_supported.html.twig',
            );
        } else if (!Configuration::get('PS_SSL_ENABLED', false)) {
            return $this->render(
                '@Modules/knytify/views/templates/errors/no_ssl.html.twig',
            );
        }

        $api_key = Configuration::get('KNYTIFY_API_KEY');

        if (empty($api_key)) {
            Tools::redirectAdmin(
                $this->context->link->getAdminLink('KnytifyGettingStarted')
            );
        } else {
            Tools::redirectAdmin(
                $this->context->link->getAdminLink('KnytifyConfiguration')
            );
        }
    }

    public function hookDisplayBackOfficeHeader($params)
    {
        if (
            Tools::getIsset('controller') &&
            Tools::getValue('controller') == 'KnytifyStats'
        ) {
            $this->context->controller->addJS($this->_path . 'views/js/vendor/chartjs-4.1.1.min.js');
            // $this->context->controller->addJS($this->_path . 'views/js/vendor/d3-3.5.17.min.js');
            // $this->context->controller->addJS($this->_path . 'views/js/vendor/nv.d3-1.8.6.min.js');
            // $this->context->controller->addCSS($this->_path . 'views/css/vendor/nv.d3.min.css',);
        }

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
