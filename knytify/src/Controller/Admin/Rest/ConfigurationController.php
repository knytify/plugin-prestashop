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

use Symfony\Component\HttpFoundation\Request;
use Configuration;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ConfigurationController extends BaseController
{
    public function getConfig(Request $request)
    {
        /*
         * Gets the general plugin configuration
         */
        return new JsonResponse([
            'enabled' => Configuration::get('KNYTIFY_ENABLED', false)
        ]);
    }

    public function setConfig(Request $request)
    {
        /**
         * Sets the general plugin configuration
         */
        $data = json_decode($request->getContent(), true);

        if (isset($data['enabled'])) {
            $enabled = !empty($data['enabled']) ? '1' : '0';
        } else {
            $enabled = null;
        }

        if ($enabled !== null) {
            Configuration::updateValue('KNYTIFY_ENABLED', $enabled);
        }

        return new Response('Updated', 201);
    }

    public function getScriptConfig(Request $request)
    {
        /**
         * Gets the Knytify JS Tag configuration
         */
        $config = Configuration::get('KNYTIFY_SCRIPT_CONFIG', null);
        $config = empty($config) ? [] : json_decode($config, true);

        return new JsonResponse($config);
    }

    public function setScriptConfig(Request $request)
    {
        /**
         * Updates the Knytify JS Tag configuration
         */
        $data = $request->getContent();
        Configuration::updateValue('KNYTIFY_SCRIPT_CONFIG', $data);

        return new Response('Updated', 201);
    }
}
