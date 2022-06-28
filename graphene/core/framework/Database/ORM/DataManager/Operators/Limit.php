<?php

namespace Graphene\Database\ORM\DataManager\Operators;

Class Limit
{

    public static function handle($limit)
    {
        if (!$limit) {
            return false;
        }

        if (is_int($limit)) {
            return $limit;
        }

        return $limit;

    }

}