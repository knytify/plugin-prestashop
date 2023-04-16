<?php
/**
 * Knytify Fraud Protection Plugin for Prestashop
 * Copyright (C) 2023 Knytify SARL-s
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @author       Knytify SARL <inquiry@knytify.com>
 * @copyright    2022-2023 Knytify SARL
 * @license      MIT License (https://opensource.org/licenses/MIT)
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
