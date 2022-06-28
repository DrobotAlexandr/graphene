<?php

namespace Graphene\Database\ORM\DataManager\Operators;

Class OrderBy
{

    public static function handle($orderBy)
    {
        if (!$orderBy) {
            return false;
        }

        if (!is_array($orderBy)) {
            $arOrder = [];
            $arOrder[] = $orderBy;
            $orderBy = $arOrder;
        }

        $arData = [];

        foreach ($orderBy as $order) {
            $arData[] = strtr($order, ['.' => ' ']);
        }

        return $arData;
    }

}