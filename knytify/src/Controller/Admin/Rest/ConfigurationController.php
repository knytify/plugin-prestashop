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

use Symfony\Component\HttpFoundation\Request;
use Configuration;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;


class ConfigurationController extends BaseController
{

    public function getConfig(Request $request)
    {
        /**
         * Gets the general plugin configuration
         */
        return new JsonResponse([
            "enabled" => Configuration::get('KNYTIFY_ENABLED', false)
        ]);
    }

    public function setConfig(Request $request)
    {
        /**
         * Sets the general plugin configuration
         */
        $data = json_decode($request->getContent(), true);

        if (isset($data['enabled'])) {
            $enabled = !empty($data['enabled']) ? "1" : "0";
        } else {
            $enabled = null;
        }

        if ($enabled !== NULL) {
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
