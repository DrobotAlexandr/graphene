<?php

namespace Graphene\Database;

class TablesManager
{

    public static function createTable($table): bool
    {
        $table = trim($table);

        if (!$table) {
            return false;
        }

        $database = new Database;

        $pdo = $database->init();

        try {

            $stmt = $pdo->prepare("CREATE TABLE `$table` ( id INT PRIMARY KEY AUTO_INCREMENT )");

            $stmt->execute();

            return true;

        } catch (\Throwable  $t) {

            return false;

        }
    }

    public static function addColumn($params): bool
    {

        if ($params['column'] == 'id') {
            return false;
        }

        $database = new Database;

        $pdo = $database->init();

        $table = $params['tableName'];
        $column = $params['column'];
        $type = $params['type'];
        $comment = $params['comment'];

        try {

            $pdo->query("ALTER TABLE  $table ADD  $column $type NOT NULL COMMENT '$comment'");

            return true;

        } catch (\Throwable  $t) {

            return false;

        }

    }

    public static function getColumns($table): array
    {

        if (!$table) {
            return [];
        }

        $database = new Database;

        $pdo = $database->init();

        $stmt = $pdo->prepare("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$table'");

        $stmt->execute();

        $data = $stmt->fetchAll();

        $arData = [];

        if ($data) {
            foreach ($data as $value) {
                $arData[$value['COLUMN_NAME']] = $value['COLUMN_NAME'];
            }
        }

        return $arData;

    }

    public static function removeColumn($table, $column): bool
    {
        if (!$table or !$column) {
            return false;
        }

        $database = new Database;

        $pdo = $database->init();

        try {

            $pdo->query("ALTER TABLE  $table DROP  $column");

            return true;

        } catch (\Throwable  $t) {

            return false;

        }

    }

    public static function moveColumn($table, $column, $type, $position): bool
    {


        if (!$table or !$column or !$type) {
            return false;
        }

        $position = strtr($position, ['.' => ' ']);

        $database = new Database;

        $pdo = $database->init();

        try {

            $preSql = '';

            if ($column == 'id') {
                $preSql = 'AUTO_INCREMENT';
            }

            $sql = "ALTER TABLE $table CHANGE `$column` `$column` $type $preSql $position";

            $pdo->query($sql);

            return true;

        } catch (\Throwable  $t) {

            return false;

        }

    }

}