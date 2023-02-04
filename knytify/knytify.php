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
        $this->ps_versions_compliancy = array('min' => '1.7.5', 'max' => '1.9.9');

        $this->local_development = substr_compare(_PS_BASE_URL_, ".local", -strlen(".local")) === 0;


        if ($this->container === null) {
            $this->container = new \PrestaShop\ModuleLibServiceContainer\DependencyInjection\ServiceContainer(
                $this->name,
                $this->getLocalPath()
            );
        }
    }

    public function install()
    {
        Configuration::updateValue('KNYTIFY_ENABLED', true);
        return parent::install() &&
            $this->installTab() &&
            $this->registerHook('header') &&
            $this->registerHook('displayBackOfficeHeader') &&
            $this->registerHook('displayBeforeBodyClosingTag') &&
            $this->container->getService('ps_accounts.installer')->install();
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
        /**
         * Configuration errors (eg. https required)
         */
        $this->handleErrors();

        /**
         * PS Account & Billing
         */

         $accountsService = null;

         try {
             $accountsFacade = $this->container->getService('ps_accounts.facade');
             $accountsService = $accountsFacade->getPsAccountsService();
         } catch (\PrestaShop\PsAccountsInstaller\Installer\Exception\InstallerException $e) {
             $accountsInstaller = $this->getService('ps_accounts.installer');
             $accountsInstaller->install();
             $accountsFacade = $this->container->getService('ps_accounts.facade');
             $accountsService = $accountsFacade->getPsAccountsService();
         }

         try {
             Media::addJsDef([
                 'contextPsAccounts' => $accountsFacade->getPsAccountsPresenter()
                     ->present($this->name),
             ]);

             // Retrieve Account CDN
             $this->context->smarty->assign('urlAccountsCdn', $accountsService->getAccountsCdn());

         } catch (Exception $e) {
            //  $this->context->controller->errors[] = $e->getMessage();
            //  return '';
            // TODO: handle this error too
         }


        /**
         * Knytify configuration
         */
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

    private function handleErrors()
    {
        $error = null;

        if (!Configuration::get('PS_SSL_ENABLED', false) && !$this->local_development) {
            // The Knytify API blocks non-ssl requests.
            $error = "ssl";
        }

        if (!empty($error)) {
            // Combine both ways of passing args with getAdminLink
            // https://devdocs.prestashop-project.org/1.7/modules/core-updates/1.7.5/
            Tools::redirectAdmin(
                $this->context->link->getAdminLink('KnytifyError', true, [], ['error' => $error]) . '&amp;error=' . $error
            );
        }
    }

    public function hookDisplayBackOfficeHeader($params)
    {
        $current_controller_name = $this->context->controller->controller_name;


        if (
            str_starts_with($current_controller_name, "Knytify")
        ) {
            $this->context->controller->addJS($this->_path . 'views/js/back.js');
            $this->context->controller->addCSS($this->_path . 'views/css/back.css');

            if (
                $current_controller_name === 'KnytifyStats'
            ) {
                /**
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

            // A single json-encoded dictionary is used to avoid multiple sql queries.
            $script_config = Configuration::get('KNYTIFY_SCRIPT_CONFIG', '');

            return '
            <script src="https://live.knytify.com/tag/main.js"></script>
            <script>
              window.knytify.init(' . $script_config . ');
            </script>';
        }

        return false;
    }
}
