<?php

namespace Graphene\System\Builders;

class ModulesBuilder
{
    public static function createModule(string $name, string $code): bool
    {

        if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/graphene/app/')) {
            mkdir($_SERVER['DOCUMENT_ROOT'] . '/graphene/app/');
        }

        if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/graphene/app/modules/')) {
            mkdir($_SERVER['DOCUMENT_ROOT'] . '/graphene/app/modules/');
        }

        $moduleDir = $_SERVER['DOCUMENT_ROOT'] . '/graphene/app/modules/' . $code . '/';

        $moduleClassPath = $moduleDir . $code . 'Module.php';

        if (file_exists($moduleClassPath)) {
            return false;
        }

        mkdir($moduleDir);
        mkdir($moduleDir . '/Http/');
        mkdir($moduleDir . '/Http/Controllers/');
        mkdir($moduleDir . '/Models/');
        mkdir($moduleDir . '/Providers/');

        $class = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/graphene/core/signatures/GenericModule.php');

        $class = strtr($class,
            [
                '#NAME#' => $name,
                '#CODE#' => $code,
            ]
        );

        file_put_contents($moduleClassPath, $class);

        return true;
    }
}