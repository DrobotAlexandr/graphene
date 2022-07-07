<?php

namespace Graphene\Containers;

class ControllersContainer
{
    public function __construct($init)
    {

    }

    public function handle()
    {
        return [
            'items' => 1
        ];
    }
}