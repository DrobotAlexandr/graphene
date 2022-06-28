<?php

namespace Graphene\Arrays;

class TreeBuilder
{

    private array $params = [];

    public function __construct($params)
    {
        $this->params = $params;
    }

    public function buildTree()
    {

        $data = $this->getItems(0);

        return $data;

    }

    private function getItems($parentId): array
    {
        $arData = [];
        if ($this->params['items']) {
            foreach ($this->params['items'] as $item) {
                if ($item[$this->params['parentSourceField']] == $parentId) {
                    $item[$this->params['childsResultField']] = $this->getItems($item['id']);
                    $arData[] = $item;
                }
            }
        }
        return $arData;
    }

}