<?php

namespace Modules\Catalog;


class CatalogModule
{

    public string $code = 'Catalog';
    public string $name = 'Каталог';

    public array $options = [
        'showInDashboard' => true,
        'sorting' => 999,
    ];

}