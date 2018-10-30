<?php

namespace GoPokedex\System;

class Pokedexer
{
    private $database;
    private $auth;

    public function __construct($database, $auth)
    {
        $this->database = $database;
        $this->auth = $auth;
    }

    public function updateEntry($data)
    {
        try {
            $this->database->exec(
                'INSERT INTO pokedex (user_id, dex, has) VALUES (?, ?, ?)
                    ON DUPLICATE KEY UPDATE has = ?',
                [
                    $this->auth->getUserId(),
                    $data['pokemon'],
                    $data['has'],
                    $data['has']
                ]
            );
        } catch (Exception $e) {
            var_dump($e);
            exit;
        }
    }

    public function getPokedex()
    {
        $list = null;
        if ($this->auth->check()) {
            $list = $this->userList();
        } else {
            $list = $this->guestList();
        }

        array_walk($list, function (&$item, $key) {
            $item['available'] = ($item['available'])?true:false;
            $item['oor'] = ($item['oor'])?true:false;
            $item['shiny'] = ($item['shiny'])?true:false;
            $item['legendary'] = ($item['legendary'])?true:false;
            $item['forms'] = json_decode($item['forms']);
            $item['user'] = $this->decodeUserHas($item['user']);
        });

        return $list;
    }

    private function decodeUserHas($data = null)
    {
        $results = [
            'hasSeen'       => false,
            'hasCaught'     => false,
            'hasShiny'      => false,
            'hasPerfect'    => false,
            'hasImperfect'  => false,
            'hasLucky'      => false,
            'hasForms'      => [],
            'hasShinyForms' => []
        ];

        if ($data != null) {
            $user = json_decode($data, true);
            foreach ($user as $key => $val) {
                $results[$key] = ($val === "true") ? true : false;
            }
            if ($results['hasForms'] === false) {
                $results['hasForms'] = $user['hasForms'];
            }
            if ($results['hasShinyForms'] === false) {
                $results['hasShinyForms'] = $user['hasShinyForms'];
            }
        }

        return $results;
    }

    private function userList()
    {
        return $this->database->select(
            'SELECT p.*, pk.has user
                FROM pokemon p
                LEFT JOIN pokedex pk ON p.dex = pk.dex AND pk.user_id = ?
                ORDER BY p.dex ASC',
            [ $this->auth->getUserId() ]
        );
    }

    private function guestList()
    {
        return $this->database->select('SELECT p.*, null user FROM pokemon p ORDER by dex ASC');
    }
}
