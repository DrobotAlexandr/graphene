<?php

namespace Graphene\Arrays;

Class Finder
{
    public static function findElement($array, $where, $mode = 'list')
    {
        if (!$array) {
            return [];
        }

        $arData = [];

        foreach ($array as $field => $item) {

            foreach ($where as $key => $value) {
                if ($item[$key] == $value) {
                    if (is_string($field)) {
                        $arData[$field] = $item;
                    } else {
                        $arData[] = $item;
                    }
                }
            }

        }

        if ($mode == 'list') {
            return $arData;
        } else if ($mode == 'one') {
            return $arData[0];
        }
    }
}