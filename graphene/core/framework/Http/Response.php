<?php

namespace Graphene\Http;

class Response
{

    public static function output($data)
    {
        if (is_array($data)) {
            header('Content-type: application/json;');
            $json = json_encode($data, JSON_UNESCAPED_UNICODE);
            print $json;
            exit();

        } else {
            self::output__renderHtml($$data);
        }

        exit();
    }

    private static function output__renderHtml($html)
    {

        $arHtml = explode(PHP_EOL, $html);

        $html = '';

        foreach ($arHtml as $string) {
            $html .= trim($string) . ' ';
        }

        echo trim($html);
        exit();
    }

}