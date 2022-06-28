<?php

namespace Graphene\Database\ORM\DataManager\RunTime;

Class RunTime
{

    private $startTime = 0;
    private $returnData = [];
    private $sqlQuery = '';

    public function __construct()
    {
        $this->returnData = [];
    }

    public function start()
    {
        $this->returnData = [];
        $this->startTime = microtime();
    }

    public function setReturn($key, $value)
    {
        $this->returnData[$key] = $value;
    }

    public function setSqlQuery($sql)
    {
        $this->sqlQuery = trim($sql);
    }

    public function end()
    {

        $arReturn = [];

        $arReturn['status'] = 'ok';

        if ($this->returnData) {
            foreach ($this->returnData as $key => $value) {
                $arReturn[$key] = $value;
            }
        }

        $arReturn['debug'] = $this->end__debug();

        return (object)$arReturn;

    }

    private function end__debug()
    {
        $res['time'] = round(microtime() - $this->startTime, 3);

        if ($this->returnData['items']) {
            $res['countItems'] = count($this->returnData['items']);
        } else {
            $res['countItems'] = 0;
        }

        $res['sql'] = $this->sqlQuery;

        return $res;
    }

}