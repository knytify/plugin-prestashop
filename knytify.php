<?php
/**
* 2007-2022 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2022 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class Knytify extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'knytify';
        $this->tab = 'analytics_stats';
        $this->version = '0.0.1';
        $this->author = 'Knytify';
        $this->need_instance = 1;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Knytify - Fraud Detection and Prevention');
        $this->description = $this->l('This is the description');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall knytify');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        Configuration::updateValue('KNYTIFY_LIVE_MODE', false);

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('actionAdminControllerSetMedia') &&
            $this->registerHook('displayBeforeBodyClosingTag');
    }

    public function uninstall()
    {
        Configuration::deleteByName('KNYTIFY_LIVE_MODE');

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');
        $this->context->smarty->assign('module_dir', $this->_path);
        
        if (((bool)Tools::isSubmit('submitAlreadyMember')) == true) {
            $this->alreadyMemberResponse = 'defined';
        }
        if (((bool)Tools::isSubmit('submitLoginForm')) == true) {
            $this->loginFormResponse = 'defined';
        }

        if ($this->loginFormResponse == 'defined') {
            $this->postProcessLoginForm();
        }

        if ($this->credentialsReturn == "ok") {
            $this->urlToParse = parse_url(Context::getContext()->shop->getBaseURL(true));
            $this->urlToAdd = 'www.ajouttesturl.com';//$this->urlToParse['host'].'.com'; // supprimer le point com quand on est pas en dev pour qu'il prenne bien le domain racine
            $this->domainReturn = $this->domainToSet($this->urlToAdd, $this->credentialsResponse);
            if($this->domainReturn == "ok")
            {
                echo "toto";
                die();
            }
            else{
                echo "titi";
                die();
            }
        }

        if ($this->alreadyMemberResponse == 'defined') {
            return $output.$this->loginFormRender($this->registeredValue);
        }

        return $output.$this->alreadyMemberFormRender();
    }

    /**
     * Create the Step 1 of the module
     */
    protected function getAlreadyMemberForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Are you already a Knytify\'s registered member ?'),
                        'name' => 'KNYTIFY_ALREADY_MEMBER',
                        'is_bool' => true,
                        'desc' => $this->l('Are you already registered to knytify ?'),
                        'values' => array(
                            array(
                                'id' => 'registered_yes',
                                'value' => 1,
                                'label' => $this->l('yes')
                            ),
                            array(
                                'id' => 'registered_no',
                                'value' => 0,
                                'label' => $this->l('no')
                            )
                        ),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Next'),
                ),
            ),
        );
    }

    protected function getAlreadyMemberFormValues()
    {
        return array(
            'KNYTIFY_ALREADY_MEMBER' => Configuration::get('KNYTIFY_ALREADY_MEMBER', false),
        );
    }

    protected function alreadyMemberFormRender()
    {
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitAlreadyMember';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getAlreadyMemberFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );
        return $helper->generateForm(array($this->getAlreadyMemberForm()));
    }

    protected function postProcessAlreadyMember()
    {
        $form_values = $this->getAlreadyMemberFormValues();
        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
        $this->registeredValue = Tools::getValue('KNYTIFY_ALREADY_MEMBER');
        return 'defined';
    }

    /**
     * Create the Step 2 of the module
     */
    protected function getloginForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-envelope"></i>',
                        'desc' => $this->l('Enter a valid email address'),
                        'name' => 'KNYTIFY_ACCOUNT_EMAIL',
                        'label' => $this->l('Email'),
                    ),
                    array(
                        'type' => 'password',
                        'name' => 'KNYTIFY_ACCOUNT_PASSWORD',
                        'label' => $this->l('Password'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Next'),
                ),
            ),
        );
    }
    protected function getloginFormValues()
    {
        return array(
            'KNYTIFY_ACCOUNT_EMAIL' => Configuration::get('KNYTIFY_ACCOUNT_EMAIL', 'contact@prestashop.com'),
            'KNYTIFY_ACCOUNT_PASSWORD' => Configuration::get('KNYTIFY_ACCOUNT_PASSWORD', null),
        );
    }
    protected function loginFormRender($registeredValue)
    {
        if (registeredValue == 1) {
            $helper = new HelperForm();

            $helper->show_toolbar = false;
            $helper->table = $this->table;
            $helper->module = $this;
            $helper->default_form_language = $this->context->language->id;
            $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
    
            $helper->identifier = $this->identifier;
            $helper->submit_action = 'submitLoginForm';
            $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
                .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
            $helper->token = Tools::getAdminTokenLite('AdminModules');
    
            $helper->tpl_vars = array(
                'fields_value' => $this->getloginFormValues(), /* Add values for your inputs */
                'languages' => $this->context->controller->getLanguages(),
                'id_language' => $this->context->language->id,
            );
            return $helper->generateForm(array($this->getloginForm()));
        }
        if (registeredValue == 0) {
            $helper = new HelperForm();

            $helper->show_toolbar = false;
            $helper->table = $this->table;
            $helper->module = $this;
            $helper->default_form_language = $this->context->language->id;
            $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
    
            $helper->identifier = $this->identifier;
            $helper->submit_action = 'submitLoginForm';
            $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
                .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
            $helper->token = Tools::getAdminTokenLite('AdminModules');
    
            $helper->tpl_vars = array(
                'fields_value' => $this->getloginFormValues(), /* Add values for your inputs */
                'languages' => $this->context->controller->getLanguages(),
                'id_language' => $this->context->language->id,
            );
            
            return $helper->generateForm(array($this->getloginForm()));
        }
    }

    protected function postProcessLoginForm()
    {
        $form_values = $this->getloginFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }

        $params=array(
            'username'=>Tools::getValue('KNYTIFY_ACCOUNT_EMAIL'),
            'email'=>Tools::getValue('KNYTIFY_ACCOUNT_EMAIL'),
            'password'=>Tools::getValue('KNYTIFY_ACCOUNT_PASSWORD'),
            'keepMeLogged'=>false);
        $payload = json_encode($params);

        $this->credentialsReturn = $this->getCredentials($payload);
    }

    private function getCredentials($payload)
    {
        $ch = curl_init("https://back.knytify.com/auth/login");
        try {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLINFO_HEADER_OUT, true);
            curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/cookies.txt');
            curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/cookies.txt');
            curl_setopt(
                $ch,
                CURLOPT_HTTPHEADER,
                array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($payload)),
            );
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            
            $response = curl_exec($ch);
            
            if (curl_errno($ch)) {
                echo curl_error($ch);
            }
            
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if ($http_code == intval(200)) {
                $this->credentialsResponse = json_decode($response, false);
                return "ok";
            } else {
                return "nok";
            }
        } catch (\Throwable $th) {
            throw $th;
        } finally {
            curl_close($ch);
        }
    }

    private function addDomain($domainToAdd)
    {
        $authorizationPayload = "Authorization: Bearer " . $this->credentialsResponse->access_token;
        $paramsDomain=array(
            'domain' => $domainToAdd
            );
        $payloadDomain = json_encode($paramsDomain);

        $ch = curl_init("https://back.knytify.com/me/domain");
        try {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payloadDomain);
            curl_setopt($ch, CURLINFO_HEADER_OUT, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt(
                $ch,
                CURLOPT_HTTPHEADER,
                array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($payloadDomain),
                $authorizationPayload)
            );
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            
            $response = curl_exec($ch);
            
            if (curl_errno($ch)) {
                echo curl_error($ch);
            }
            
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($http_code == intval(201)) {
                return "ok";
            } else {
                return "nok";
            }
        } catch (\Throwable $th) {
            throw $th;
        } finally {
            curl_close($ch);
        }
    }

    private function domainToSet($domainToSetValue, $authorization)
    {
        $authorizationPayload = "Authorization: Bearer " . $this->credentialsResponse->access_token;
        $ch = curl_init("https://back.knytify.com/me/domain");
        try {
            curl_setopt($ch, CURLINFO_HEADER_OUT, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt(
                $ch,
                CURLOPT_HTTPHEADER,
                array(
                'Content-Type: application/json',
                $authorizationPayload
            )
            );
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            
            $response = curl_exec($ch);
            
            if (curl_errno($ch)) {
                echo curl_error($ch);
            }
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($http_code == intval(200)) {
                $responseArray = json_decode($response);
                foreach ($responseArray as $key => $value) {
                    foreach ($value as $key2 => $value2) {
                        if ($value2 == $domainToSetValue) {
                            $this->domainIsAlreadySet = true;
                        }
                    }
                }
                if (isset($this->domainIsAlreadySet)) {
                    return "ok";
                } else {
                    $domainIsNowSet = $this->addDomain($domainToSetValue);
                    return $domainIsNowSet;
                }
            } else {
                return "nok";
            }
        } catch (\Throwable $th) {
            throw $th;
        } finally {
            curl_close($ch);
        }
    }








    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path.'/views/js/front.js');
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
    }

    public function hookActionAdminControllerSetMedia()
    {
        /* Place your code here. */
    }

    public function hookDisplayBeforeBodyClosingTag(){
        return   '
        <script src="https://live.knytify.com/tag/main.js"></script>
        <script>
          window.knytify.init();
        </script>';
    }
}
