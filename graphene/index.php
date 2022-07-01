<?php

include 'graphene.php';

$container = new \Graphene\Containers\ApiContainer(
    '/graphene/api/:appType/:version/:module/:model/:method'
);

$container->handle(function ($context, $request) {
    return [];
});
