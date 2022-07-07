<?php

include 'graphene.php';

$apiContainer = new Graphene\Containers\ApiContainer(
    '/graphene/api/:appType/:version/:module/:model/:method'
);

$apiContainer->handle(function ($context, $request) {

    $controllersContainer = new Graphene\Containers\ControllersContainer(
        ['context' => $context, 'request' => $request]
    );

    return $controllersContainer->handle();
});
