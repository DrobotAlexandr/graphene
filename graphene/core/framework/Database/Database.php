<?php

namespace Graphene\Database;

use PDO;

class Database
{
    static private $PDOInstance;

    public function init($params = [])
    {

        if (self::$PDOInstance) {
            return self::$PDOInstance;
        }

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
            self::$PDOInstance = new PDO($dsn, $settings['user'], $settings['password'], $opt);
            self::$PDOInstance->exec("set names utf8");
        } catch (\PDOException $e) {
            echo 'Error connecting to database';
            exit();
        }

        return self::$PDOInstance;
    }

}