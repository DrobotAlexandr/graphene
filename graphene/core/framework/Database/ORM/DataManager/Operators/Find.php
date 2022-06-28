<?php

namespace Graphene\Database\ORM\DataManager\Operators;

Class Find
{

    public static function handle($findKey)
    {
        if(!$findKey)
        {
            return false;
        }

        $arWhere = [];

        if (is_array($findKey)) {

            if ($findKey['sql']) {
                $arWhere['sql'] = $findKey['sql'];
            }

            if (!$arWhere) {
                $arWhere = $findKey;
            }

        } else {

            if (is_int($findKey)) {
                $arWhere['id'] = $findKey;
            }

            if (strstr($findKey, 'tkn.') AND strstr($findKey, '$')) {
                $arWhere['token'] = $findKey;
            }

            if (!$arWhere) {
                $arWhere['code'] = $findKey;
            }

        }

        return $arWhere;
    }

}