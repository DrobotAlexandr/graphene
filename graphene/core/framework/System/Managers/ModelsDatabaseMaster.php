<?php

namespace Graphene\System\Managers;

use Graphene\Arrays\Finder;
use Graphene\Database\TablesManager;
use Graphene\Filesystem\Directory;
use Graphene\System\Readers\ModelsReader;

class ModelsDatabaseMaster
{

    public static function run(): void
    {
        $models = self::getAllModels();

        if ($models) {

            foreach ($models as $model) {

                $model = ModelsReader::getModel($model['module'], $model['model']);

                self::handleInstance($model['instance']);

            }

        }
    }

    private static function getAllModels(): array
    {

        $arModels = [];

        foreach (Directory::scan('/graphene/app/Modules/', 'folders') as $module) {

            foreach (Directory::scan($module['path'] . '/Models/', 'files') as $model) {
                $arModels[] = [
                    'module' => $module['name'],
                    'model' => strtr($model['name'], ['.php' => '']),
                ];
            }

        }

        return $arModels;

    }

    private static function handleInstance($instance): void
    {

        TablesManager::createTable($instance->table);

        foreach ($instance->columns as $column) {

            TablesManager::addColumn(
                [
                    'tableName' => $instance->table,
                    'column' => $column['column'],
                    'type' => $column['type'],
                    'comment' => $column['comment'],
                ]
            );

        }

        foreach (TablesManager::getColumns($instance->table) as $dbColumn) {

            if (!Finder::findElement($instance->columns, ['column' => $dbColumn], 'one')) {

                TablesManager::removeColumn($instance->table, $dbColumn);

            }

        }

        foreach ($instance->columns as $k => $column) {

            if (!$k) {
                $position = 'FIRST';
            } else {
                $position = 'AFTER.' . $instance->columns[$k - 1]['column'];
            }

            TablesManager::moveColumn($instance->table, $column['column'], $column['type'], $position);

        }

    }

}