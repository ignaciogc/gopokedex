<?php

namespace GoPokedex\Controllers;

use Http\Request;
use Http\Response;

class Admin
{
    private $request;
    private $response;
    private $database;

    public function __construct(Request $request, Response $response, $database, $auth)
    {
        $this->request = $request;
        $this->response = $response;
        $this->database = $database;
        if (!$auth->hasRole(\Delight\Auth\Role::ADMIN)) {
            $this->response->setStatusCode(403, 'You must be an admin to perform this operation');
            var_dump("403: You're not an admin");
            exit;
        }
    }

    public function showDashboard()
    {
        var_dump('show dashboard');
    }

    public function updatePokemon()
    {
        $str = file_get_contents(ROOT_PATH.'/Migrations/pokedex.json');
        $data = json_decode($str, true);
        $text = '';

        foreach ($data as $pokemon) {
            $this->database->exec(
                'INSERT INTO pokemon (dex, name, atk, def, hp, maxcp, typea,
                    typeb, available, oor, shiny, legendary, region, regional,
                    forms, shinyforms)
                    VALUES (?, ? , ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE name = ?, atk = ?, def = ?,
                    hp = ?, maxcp = ?, typea = ?, typeb = ?, available = ?,
                    oor = ?, shiny = ?, legendary = ?, region = ?, regional = ?,
                    forms = ?, shinyforms = ?',
                [
                    $pokemon['dex'],
                    $pokemon['name'],
                    $pokemon['atk'],
                    $pokemon['def'],
                    $pokemon['hp'],
                    $pokemon['maxcp'],
                    $pokemon['typea'],
                    $pokemon['typeb'],
                    $pokemon['available'],
                    $pokemon['oor'],
                    $pokemon['shiny'],
                    $pokemon['legendary'],
                    $pokemon['region'],
                    $pokemon['regional'],
                    json_encode($pokemon['forms']),
                    json_encode($pokemon['shinyforms']),
                    $pokemon['name'],
                    $pokemon['atk'],
                    $pokemon['def'],
                    $pokemon['hp'],
                    $pokemon['maxcp'],
                    $pokemon['typea'],
                    $pokemon['typeb'],
                    $pokemon['available'],
                    $pokemon['oor'],
                    $pokemon['shiny'],
                    $pokemon['legendary'],
                    $pokemon['region'],
                    $pokemon['regional'],
                    json_encode($pokemon['forms']),
                    json_encode($pokemon['shinyforms'])
                ]
            );



            $text.= sprintf(
                'Inserted #%s %s<br>',
                $pokemon['dex'],
                $pokemon['name']
            );
        }

        $this->database->insert(
            'news',
            [
                'type' => 'pokedex_update',
                'text' => 'import by admin'
            ]
        );

        $this->response->SetContent($text);
    }
}
