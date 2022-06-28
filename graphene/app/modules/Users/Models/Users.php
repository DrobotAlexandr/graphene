<?php

namespace Modules\Users\Models;

use Graphene\Database\ORM\Model;

class Users extends Model
{
    public string $name = 'Пользователи';
    public string $table = 'users';

    public array $options = [
        'panel' => [
            'showInMenu' => false,
            'showInWidget' => true,
            'oneEntityMode' => false,
            'sorting' => 999,
        ]
    ];

    public array $labels = [
        'ru' => [
            'many' => 'Записи',
            'one' => 'Запись',
            'add' => 'Добавить запись',
            'edit' => 'Редактировать запись',
            'remove' => 'Удалить запись',
        ]
    ];

    public array $columns = [
        [
            'name' => 'Id',
            'column' => 'id',
            'type' => 'int',
            'editor' => false,
            'config' => [],
        ],
        [
            'name' => 'Название',
            'column' => 'name',
            'type' => 'varchar(255)',
            'editor' => 'text-input',
            'config' => [],
        ],
        [
            'name' => 'Дата создания',
            'column' => 'created_at',
            'type' => 'datetime',
            'editor' => false,
            'config' => [],
        ],
        [
            'name' => 'Дата обновления',
            'column' => 'updated_at',
            'type' => 'datetime',
            'editor' => false,
            'config' => [],
        ],
        [
            'name' => 'Активность',
            'column' => 'is_active',
            'type' => 'varchar(1)',
            'editor' => false,
            'config' => [],
        ]
    ];

}