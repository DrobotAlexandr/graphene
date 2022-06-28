<?php

namespace Graphene\Filesystem;

class Directory
{
    public static function remove($params)
    {

        if ($params['path'] == $_SERVER['DOCUMENT_ROOT']) {
            $params['path'] = false;
        }

        if (strtr($_SERVER['DOCUMENT_ROOT'], ['/' => '']) == strtr($params['path'], ['/' => ''])) {
            $params['path'] = false;
        }

        if ($params['path']) {
            $params['path'] = trim($params['path']);
        }

        if (!$params['path']) {
            return false;
        }

        if ($content_del_cat = glob($params['path'] . '/*')) {

            foreach ($content_del_cat as $object) {
                if (is_dir($object)) {
                    self::remove(['path' => $object]);
                } else {
                    @chmod($object, 0777);
                    @unlink($object);
                }
            }
        }
        @chmod($object, 0777);
        @rmdir($params['path']);

        return true;
    }

    public static function scan(string $path, string $type = ''): array
    {

        if (!$path) {
            return [];
        }

        $path = strtr($path, [$_SERVER['DOCUMENT_ROOT'] => '']);

        if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $path)) {
            return [];
        }

        $data = scandir($_SERVER['DOCUMENT_ROOT'] . $path);

        $arData = [];

        if ($data) {
            foreach ($data as $value) {

                if ($value == '.' or $value == '..') {
                    continue;
                }

                if (is_dir($_SERVER['DOCUMENT_ROOT'] . $path . '/' . $value)) {
                    $arData['folders'][] = [
                        'name' => $value,
                        'src' => $path . $value . '/',
                        'path' => $_SERVER['DOCUMENT_ROOT'] . $path . $value . '/'
                    ];
                } else {
                    $arData['files'][] = [
                        'name' => $value,
                        'src' => $path . $value,
                        'path' => $_SERVER['DOCUMENT_ROOT'] . $path . $value
                    ];
                }

            }
        }

        if ($type AND $arData) {
            return $arData[$type];
        }

        return $arData;
    }

    public static function getSize($params)
    {
        $params['path'] = self::getSize__getPath($params);

        $data['size'] = self::getSize__dir_size($params['path']);
        $data['sizeFormat'] = self::getSize__format_size($data['size']);

        return $data;


    }

    public static function getSize__dir_size($dirName)
    {
        $size = 0;
        if ($dirStream = @opendir($dirName)) {
            while (false !== ($filename = readdir($dirStream))) {
                if ($filename != "." && $filename != "..") {
                    if (is_file($dirName . "/" . $filename))
                        $size += filesize($dirName . "/" . $filename);

                    if (is_dir($dirName . "/" . $filename))
                        $size += self::getSize__dir_size($dirName . "/" . $filename);
                }
            }
            @closedir($dirStream);
        }
        return $size;
    }

    public static function getSize__format_size($size)
    {
        $metrics[0] = 'байт';
        $metrics[1] = 'Кбайт';
        $metrics[2] = 'Мбайт';
        $metrics[3] = 'Гбайт';
        $metrics[4] = 'Тбайт';
        $metric = 0;
        while (floor($size / 1024) > 0) {
            ++$metric;
            $size /= 1024;
        }
        $ret = round($size, 1) . " " . (isset($metrics[$metric]) ? $metrics[$metric] : '??');
        return $ret;
    }

    private static function getSize__getPath($params)
    {

        $path = $_SERVER['DOCUMENT_ROOT'] . '/' . strtr($params['path'], [$_SERVER['DOCUMENT_ROOT'] => '']);

        $path = strtr($path, ['//' => '/']);

        return $path;

    }
}