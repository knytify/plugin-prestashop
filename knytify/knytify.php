<?php
/**
 * Knytify Fraud Protection Plugin for Prestashop
 * Copyright (C) 2023  Knytify SARL-s
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author       Knytify SARL <inquiry@knytify.com>
 * @copyright    2022-2023 Knytify SARL-s
 * @license      GPL-3.0-or-later (https://www.gnu.org/licenses/gpl-3.0.html)
 **/
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

class Knytify extends Module
{
    /**
     * @var ServiceContainer
     */
    private $container;

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
        $this->ps_versions_compliancy = ['min' => '1.7.5', 'max' => '1.9.9'];

        if ($this->container === null) {
            $this->container = new \PrestaShop\ModuleLibServiceContainer\DependencyInjection\ServiceContainer(
                $this->name,
                $this->getLocalPath()
            );
        }

        $this->module_key = '9c26b12e1d9f1e541b630a777afc77c2';
    }

    public function install()
    {
        Configuration::updateValue('KNYTIFY_ENABLED', true);

        return parent::install() &&
            $this->installTab() &&
            $this->registerHook('header') &&
            $this->registerHook('displayBackOfficeHeader') &&
            $this->registerHook('displayBeforeBodyClosingTag') &&
            $this->getService('ps_accounts.installer')->install();
    }

    public function uninstall()
    {
        Configuration::deleteByName('KNYTIFY_API_KEY');

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
        $tab->name = [];
        foreach (Language::getLanguages() as $lang) {
            $tab->name[$lang['id_lang']] = 'Knytify Stats';
        }
        $tab->id_parent = (int) Tab::getIdFromClassName('AdminStats'); // Prestashop Parent tab.
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
        Tools::redirectAdmin(
            $this->context->link->getAdminLink('KnytifyApp')
        );
    }

    public function hookDisplayBackOfficeHeader($params)
    {
        $current_controller_name = $this->context->controller->controller_name;

        if (
            str_starts_with($current_controller_name, 'Knytify')
        ) {
            $this->context->controller->addJS($this->_path . 'views/js/back.js');
            $this->context->controller->addCSS($this->_path . 'views/css/back.css');

            if (
                $current_controller_name === 'KnytifyStats'
            ) {
                /*
                 * We want to use a custom stats page.
                 * We chose ChartJS so we can control its version, without conflicting with Prestashop nvd3.
                 */
                $this->context->controller->addJS($this->_path . 'views/js/vendor/chartjs-4.1.1.min.js');
            }
        }

        return false;
    }

    public function hookHeader()
    {
        if (Configuration::get('KNYTIFY_ENABLED')) {
            $script_config = Configuration::get('KNYTIFY_SCRIPT_CONFIG', '');

            return '
            <script src="https://live.knytify.com/tag/main.js"></script>
            <script>
              window.knytify.init(' . $script_config . ');
            </script>';
        }

        return false;
    }

    /**
     * Retrieve the service
     *
     * @param string $serviceName
     *
     * @return mixed
     */
    public function getService($serviceName)
    {
        return $this->container->getService($serviceName);
    }
}
