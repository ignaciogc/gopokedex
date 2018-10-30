<?php

namespace GoPokedex\Controllers;

use Http\Request;
use Http\Response;
use GoPokedex\System\Pokedexer;

class Pokedex
{
    private $request;
    private $response;
    private $pokedexer;
    private $auth;

    public function __construct(Request $request, Response $response, Pokedexer $pokedexer, $auth)
    {
        $this->request = $request;
        $this->response = $response;
        $this->pokedexer = $pokedexer;
        $this->auth = $auth;
    }

    public function getPokedex()
    {
        $this->response->setContent(
            json_encode($this->pokedexer->getPokedex())
        );
    }

    public function doUpdate()
    {
        if (!$this->auth->check()) {
            $this->response->setStatusCode(403, 'You must be logged in to perform this operation');
            exit;
        }

        $this->pokedexer->updateEntry([
            'pokemon' => $this->request->getParameter('pokemon'),
            'has' => json_encode($this->request->getParameter('user'))
        ]);
    }
}
