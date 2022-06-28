<?php

namespace Graphene\Database\ORM;

use Graphene\Framework\Database\ORM\DataManager\DataManager;
use  Graphene\Framework\Database\ORM\DataManager\Operators\Find;


Class Model
{

    /**
     * Таблица связанной сущности
     * @return string
     */
    public static function table()
    {
        $object = static::class;

        $object = new $object;

        return (string)$object->table;
    }

    public static function find($findKey = 0)
    {
        return new DataManager(
            [
                'entity' => self::table(),
                'params' => [
                    'findKey' => Find::handle($findKey)
                ]
            ]
        );
    }

    public static function set($data)
    {
        return new DataManager(
            [
                'entity' => self::table(),
                'params' => [
                    'findKey' => ['id' => 0],
                    'saveData' => $data
                ]
            ]
        );
    }

    public static function blockEl()
    {
        return self::find(1);
    }


}