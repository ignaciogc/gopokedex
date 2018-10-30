<?php

namespace GoPokedex\Controllers;

use Http\Request;
use GoPokedex\System\Template;
use Delight\Cookie\Session;

class Homepage
{
    private $request;
    private $template;
    private $database;
    private $auth;

    public function __construct(Request $request, Template $template, $database, $auth)
    {
        $this->request = $request;
        $this->template = $template;
        $this->database = $database;
        $this->auth = $auth;
    }

    public function show()
    {
        $this->template->output('homepage', [
            'auth' => $this->auth,
            'action' => Session::take('action'),
            'error' => Session::take('error'),
            'code' => Session::take('code')
        ]);
    }
}
