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
