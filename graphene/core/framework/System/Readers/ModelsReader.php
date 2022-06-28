<?php

namespace Graphene\System\Readers;

class ModelsReader
{

    public static function getModels(array $params = []): bool|array
    {
        $models = [];

        foreach (scandir($_SERVER['DOCUMENT_ROOT'] . '/graphene/app/modules/') as $moduleCode) {

            if ($params['module']) {
                if ($moduleCode != $params['module']) {
                    continue;
                }
            }

            if (!str_contains($moduleCode, '.')) {

                $modelsDir = $_SERVER['DOCUMENT_ROOT'] . '/graphene/app/modules/' . $moduleCode . '/Models';

                if (!file_exists($modelsDir)) {
                    return false;
                }

                foreach (scandir($modelsDir) as $modelCode) {


                    if (str_contains($modelCode, '.php')) {

                        $modelCode = explode('.', $modelCode)[0];

                        $modelClass = "Modules\\$moduleCode\Models\\$modelCode";

                        $model = new  $modelClass();

                        $data = [
                            'name' => $model->name,
                            'code' => $modelCode,
                            'module' => $moduleCode,
                            'table' => $model->table,
                            'sorting' => $model->name,
                            'namespace' => 'App\Modules\\' . $moduleCode . '\Models',
                            'className' => $modelCode,
                            'instance' => $model,
                        ];

                        if ($params['get'] == 'left-menu') {
                            if (!$model->options['panel']['showInMenu']) {
                                continue;
                            }
                        }

                        $models[$modelCode] = $data;

                        if ($params['model'] and $modelCode == $params['model']) {
                            return $data;
                        }
                    }

                }
            }
        }


        return $models;
    }

    public static function getModel(string $module, string $model)
    {
        $model = self::getModels(['module' => $module, 'model' => $model]);

        return $model;
    }
}