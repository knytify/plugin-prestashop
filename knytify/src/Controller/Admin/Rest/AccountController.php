<?php

namespace Knytify\Controller\Admin\Rest;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AccountController extends BaseController
{

    public function user(Request $request)
    {
        return $this->knytify_request('getUser', $request);
    }

    public function login(Request $request)
    {
        return new Response('OK', 200);
    }


    public function setup(Request $request)
    {
        return new Response('OK', 200);
    }
}
