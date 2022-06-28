<?php

namespace Graphene\System\Builders;

class ModelsBuilder
{
    public static function createModel(string $name, string $code, string $module, string $template = 'default'): bool
    {
        $moduleDir = $_SERVER['DOCUMENT_ROOT'] . '/graphene/app/modules/' . $module . '/';

        $moduleClassPath = $moduleDir . $module . 'Module.php';
        $modelClassPath = $moduleDir . 'models/' . $code . '.php';

        $serviceProviderClassPath = $moduleDir . 'Providers/' . $code . 'ServiceProvider' . '.php';
        $controllerClassPath = $moduleDir . 'Http/Controllers/' . $code . 'Controller' . '.php';

        if (!file_exists($moduleClassPath)) {
            return false;
        }

        if (file_exists($modelClassPath)) {
            return false;
        }

        $table = self::getTableName($module, $code);

        $class = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/graphene/core/signatures/GenericModel_' . $template . '.php');

        $class = strtr($class,
            [
                '#NAME#' => $name,
                '#CODE#' => $code,
                '#MODULE#' => $module,
                '#TABLE#' => $table,
            ]
        );

        file_put_contents($modelClassPath, $class);

        $class = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/graphene/core/signatures/GenericServiceProvider.php');

        $class = strtr($class,
            [
                '#CODE#' => $code . 'ServiceProvider',
                '#MODULE#' => $module,
            ]
        );

        file_put_contents($serviceProviderClassPath, $class);

        $class = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/graphene/core/signatures/GenericController.php');

        $class = strtr($class,
            [
                '#CODE#' => $code . 'Controller',
                '#MODULE#' => $module,
            ]
        );

        file_put_contents($controllerClassPath, $class);

        return true;
    }

    private static function getTableName($module, $code): string
    {

        if ($module != $code) {
            $code = $module . '_' . $code;
        }

        $code = strtr($code, ['__' => '_']);
        $code = strtr($code, ['_' => '']);

        return self::tableNameNormalize($code);
    }

    private static function tableNameNormalize($input): string
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return implode('_', $ret);
    }
}