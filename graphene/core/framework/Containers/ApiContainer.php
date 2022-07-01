<?php

namespace Graphene\Containers;

class ApiContainer
{

    private $urlParams = [];
    private $urlTemplate = '';

    public function __construct($url = '/graphene/api/:appType/:version/:module/:model/:method')
    {
        $this->setUrlTemplate($url);

        $this->setUrlParams();
    }

    private function setUrlTemplate($urlTemplate)
    {
        $this->urlTemplate = $urlTemplate;
    }

    private function getUrlTemplate()
    {
        return $this->urlTemplate;
    }

    private function setUrlParams()
    {
        $params = [];

        $urlData = explode('/', $this->getUrl());

        foreach (explode('/', $this->getUrlTemplate()) as $k => $urlPart) {
            if (strstr($urlPart, ':')) {
                $params[$urlPart] = trim($urlData[$k]);
            }
        }

        $this->urlParams = $params;
    }

    private function getUrlParams()
    {
        $params = $this->urlParams;

        return $params;
    }

    private function inclusionMask()
    {
        $templateUrl = explode(':', $this->getUrlTemplate());
        $url = $this->getUrl();

        if ($this->normalizeUrl($templateUrl[0]) == $this->normalizeUrl($url)) {
            return true;
        } else {
            return false;
        }
    }

    private function normalizeUrl($url)
    {
        $url = rtrim($url, '/');

        return $url;
    }

    public function handle($callback)
    {
        if (!$this->inclusionMask()) {
            return false;
        }

        $executeTime = [
            'start' => 0,
            'end' => 0,
        ];

        $executeTime['start'] = microtime();

        $res = $callback();

        $executeTime['end'] = microtime();

        $this->response($res);
    }

    public function getUrl()
    {
        return explode('?', $_SERVER['REQUEST_URI'])[0];
    }

    private function response($data)
    {
        if (!$data['status']) {
            $data = 'ok';
        }
        header('Content-type: application/json;');
        exit(json_encode($data, JSON_UNESCAPED_UNICODE));
    }


}
