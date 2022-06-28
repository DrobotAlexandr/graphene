<?php

namespace Modules\Users;


class UsersModule
{

    public string $code = 'Users';
    public string $name = 'Пользователи';

    public array $options = [
        'showInDashboard' => true,
        'sorting' => 999,
    ];

}