<?php

namespace Graphene\Http;

class Request
{
    /**
     * @param string $paramName
     * @return array|string
     */
    public static function get(string $paramName = ''): array|string
    {

        $data = $_GET;

        if ($paramName) {
            return trim($data[$paramName]);
        } else {
            return $data;
        }

    }

    /**
     * @param string $paramName
     * @return array|string
     */
    public static function post(string $paramName = ''): array|string
    {

        $data = $_POST;

        if ($paramName) {
            return trim($data[$paramName]);
        } else {
            return $data;
        }

    }
}