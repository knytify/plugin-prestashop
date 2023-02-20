<?php

namespace Knytify\Controller\Admin\Rest;

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Knytify\Service\Admin\KnytifyClient;

use Configuration;
use Module;


class BaseController extends FrameworkBundleAdminController
{
    protected $knytify_module = null;
    protected ?KnytifyClient $knytify_client = null;

    public function __construct()
    {
        $this->knytify_module = Module::getInstanceByName('knytify');
        $this->knytify_client = $this->knytify_module->get('Knytify\Service\Admin\KnytifyClient');

        $api_key = Configuration::get('KNYTIFY_API_KEY', null);

        if (!empty($api_key)) {
            $this->knytify_client->setApiKey($api_key);
        }

    }
}
