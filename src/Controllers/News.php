<?php

namespace GoPokedex\Controllers;

use Http\Request;
use Http\Response;

class News
{
    private $request;
    private $response;
    private $database;

    public function __construct(Request $request, Response $response, $database, $auth)
    {
        $this->request = $request;
        $this->response = $response;
        $this->database = $database;
    }

    public function getNews()
    {
        $this->response->setContent(
            json_encode($this->database->select('SELECT * FROM news n'))
        );
    }
}
