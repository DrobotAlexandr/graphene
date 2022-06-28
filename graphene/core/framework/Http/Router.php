<?php

namespace Graphene\Http;

class Router
{
    /**
     * @param int $part
     * @return string
     */
    public static function url(int $part = 0): string
    {
        $url = explode('?', $_SERVER['REQUEST_URI'])[0];

        if (!$part) {
            return $url;
        } else {
            $part = explode('/', $url)[$part];
            if ($part) {
                return $part;
            } else {
                return false;
            }
        }
    }

    /**
     * @param string $url
     * @param int $code
     * @return void
     */
    public static function redirect(string $url, int $code = 301): void
    {
        header("Location: $url", true, $code);
        exit();
    }
}