<?php

spl_autoload_register('autoload__graphene_core');
spl_autoload_register('autoload__app_modules');


function autoload__graphene_core($className)
{

    if (!str_contains($className, 'Graphene\\')) {
        return false;
    }

    $classPath = $_SERVER['DOCUMENT_ROOT'] . '/graphene/core/framework/' . strtr($className, ['\\' => '/', 'Graphene\\' => '']) . '.php';

    if (file_exists($classPath) and !class_exists($className)) {
        require_once $classPath;
    }
}

function autoload__app_modules($className)
{

    if (!str_contains($className, 'Modules\\')) {
        return false;
    }

    $classPath = $_SERVER['DOCUMENT_ROOT'] . '/graphene/app/modules/' . strtr($className, ['\\' => '/', 'Modules\\' => '']) . '.php';

    if (file_exists($classPath) and !class_exists($className)) {
        require_once $classPath;
    }
}