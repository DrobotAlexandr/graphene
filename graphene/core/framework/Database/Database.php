<?php

namespace Graphene\Database;

use PDO;

class Database
{

    public function init($params = [])
    {

        $settings = include $_SERVER['DOCUMENT_ROOT'] . '/graphene/config/database.php';

        if (isset($params['connection'])) {
            $settings = $settings['connections'][$params['connection']];
        } else {
            $settings = $settings['connections']['main'];
        }

        $dsn = 'mysql:host=' . $settings['host'] . ';dbname=' . $settings['database'] . ';charset=utf8';

        $opt = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            $pdo = new PDO($dsn, $settings['user'], $settings['password'], $opt);
            $pdo->exec("set names utf8");
        } catch (\PDOException $e) {
            echo 'Error connecting to database';
            exit();
        }

        return $pdo;
    }

}