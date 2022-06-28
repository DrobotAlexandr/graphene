<?php

namespace Graphene\Database\ORM\DataManager;

use Graphene\Database\Database;
use Graphene\Database\ORM\DataManager\Operators\Limit;
use Graphene\Database\ORM\DataManager\Operators\OrderBy;
use Graphene\Database\ORM\DataManager\RunTime\RunTime;
use  Graphene\Database\ORM\DataManager\Operators\Find;


class DataManager
{

    private string $table = '';
    private array $params = [];
    private array $arEx = [];
    private array $tableConfig = [];
    private string $sqlQuery = '';
    private int $countItemsInPage = 0;
    private $runTime = [];
    private $dataBase = [];

    public function __construct($data)
    {
        $database = new Database;

        $this->dataBase = $database->init();

        $this->table = $data['entity'];

        if ($data['params']) {
            foreach (array_keys($data['params']) as $key) {
                $this->setParams($key, $data['params'][$key]);
            }
        }

        $this->runTime = new RunTime;

    }

    private function setParams($key, $value)
    {
        $this->params[$key] = $value;
    }

    private function getParamsValue($key)
    {
        return $this->params[$key];
    }

    public function set($data)
    {
        $this->setParams('saveData', $data);

        return $this;
    }

    /**
     * Метод формирует sql условие для поиска записей
     * @param int $findKey
     * @return mixed
     */

    public function find($findKey = 0)
    {

        $this->setParams('findKey',
            Find::handle($findKey)
        );

        return $this;
    }

    /**
     * Метод формирует sql условие WHERE для запроса
     * @param $sqlQuery
     * @return mixed
     */

    private function getWhereSql($sqlQuery)
    {
        $this->arEx = [];

        if ($this->params['findKey']) {

            if ($this->params['findKey']['sql']) {

                $sqlQuery .= $this->params['findKey']['sql'];

            } else {

                $sqlQuery .= 'WHERE item.is_active=1 ';

                foreach ($this->params['findKey'] as $column => $value) {
                    $this->arEx[$column] = $value;
                    $sqlQuery .= "AND item.$column=:$column ";
                }

            }

        } else {

            $sqlQuery .= 'WHERE item.is_active=1 ';
        }

        return " $sqlQuery ";
    }

    /**
     * Метод формирует sql условие ORDER BY для запроса
     * @param $sqlQuery
     * @return mixed
     */

    private function getOrderBySql($sqlQuery)
    {
        $sqlQuery .= 'ORDER BY ';

        if (!$this->params['orderBy']) {

            $orderBy = 'created_at.desc';

            $this->params['orderBy'] = [];

            $this->params['orderBy'][] = strtr($orderBy, ['.' => ' ']);

        }

        foreach ($this->params['orderBy'] as $order) {
            $sqlQuery .= " item.$order,";
        }

        $sqlQuery = rtrim($sqlQuery, ',');

        return " $sqlQuery ";
    }

    /**
     * Метод формирует sql условие LIMIT для запроса
     * @param $sqlQuery
     * @return mixed
     */

    private function getLimitSql($sqlQuery)
    {
        $sqlQuery .= 'LIMIT ';


        if ($this->params['pagination']['currentPage']) {
            return $sqlQuery . self::getLimitSql__getPaginationLimit();
        }

        if (!$this->params['limit']) {

            $limit = $this->tableConfig['use_settings']['select']['limit'];

            if (!$limit) {
                $limit = 10;
            }

        } else {

            $limit = $this->params['limit'];

        }


        $sqlQuery .= $limit;

        return " $sqlQuery ";
    }

    private function getLimitSql__getPaginationLimit()
    {

        if (!$this->params['pagination']['currentPage']) {
            $this->params['pagination']['currentPage'] = 1;
        }

        if (!$this->params['pagination']['countItemsInPage']) {
            $this->params['pagination']['countItemsInPage'] = 10;
        }

        $this->countItemsInPage = $this->params['pagination']['countItemsInPage'];

        $limit = ($this->params['pagination']['currentPage'] - 1) * $this->params['pagination']['countItemsInPage'] . ',' . $this->params['pagination']['countItemsInPage'];

        return " $limit ";

    }

    /**
     * Метод формирует sql условие порядка выборки записей
     * @param string $orderBy
     * @return mixed
     */
    public function orderBy($orderBy = '')
    {

        $this->setParams('orderBy',
            OrderBy::handle($orderBy)
        );

        return $this;
    }

    /**
     * Метод формирует sql условие для пагинации
     * @param string $pagination
     * @return mixed
     */
    public function pagination($pagination = '')
    {

        $this->setParams('pagination',
            $pagination
        );

        return $this;
    }

    /**
     * Метод формирует sql условие лимита выборки записей
     * @param string $limit
     * @return mixed
     */
    public function limit($limit = '')
    {

        $this->setParams('limit',
            Limit::handle($limit)
        );

        return $this;
    }

    /**
     * Метод формирует sql условие выборки определенных ключей
     * @param array $arColumns
     * @return mixed
     */
    public function fl($arColumns = [])
    {

        $this->setParams('fl',
            $arColumns
        );

        return $this;
    }

    /**
     * Метод сохраняет запись в БД
     * @return mixed
     */
    public function save()
    {

        if (!$this->getParamsValue('saveData')) {
            return [
                'status' => 'error',
                'code' => 'noSetData'
            ];
        }

        if (!isset($this->params['saveData']['is_active'])) {
            $this->params['saveData']['is_active'] = 1;
        }

        if (!isset($this->params['saveData']['updated_at'])) {
            $this->params['saveData']['updated_at'] = date('Y-m-d H:i:s');
        }

        $entityId = $this->getOne()->data['id'];

        if (!isset($this->params['saveData']['created_at']) and !$entityId) {
            $this->params['saveData']['created_at'] = date('Y-m-d H:i:s');
        }

        $this->runTime->start();

        $this->runTime->setReturn('res',
            $this->save__handle($entityId)
        );


        return $this->runTime->end();

    }

    private function save__handle($entityId)
    {
        $database = new Database;

        $pdo = $database->init();

        $values = '';

        foreach ($this->params['saveData'] as $key => $value) {
            $values .= $key . '=:' . $key . ',';
        }

        if ($entityId) {

            $stmt = $pdo->prepare('UPDATE ' . $this->table . ' SET ' . rtrim($values, ',') . ' WHERE id=' . $entityId);

        } else {

            $stmt = $pdo->prepare('INSERT INTO ' . $this->table . ' SET ' . rtrim($values, ','));

        }

        $stmt->execute($this->params['saveData']);

        if (!$entityId) {
            $entityId = $pdo->lastInsertId();
        }

        return [
            'entityId' => $entityId
        ];
    }

    /**
     * Метод получает экземпляр записи из БД
     * @param bool $handle
     * @return mixed
     */

    public function getOne($handle = false)
    {
        $this->runTime->start();

        $data = $this->getOne__select();

        if ($handle and $data) {

            $handleResponse = $handle($data);

            if ($handleResponse) {
                $data = $handleResponse;
            }

        }

        $this->runTime->setSqlQuery(
            $this->sqlQuery
        );

        $this->runTime->setReturn('data',
            self::getOne__handle($data)
        );

        return $this->runTime->end();
    }

    public function get($handle = false)
    {
        return $this->getOne($handle);
    }

    private static function getOne__handle($data)
    {
        if (!$data) {
            return false;
        }

        $data = self::getOne__handle__values($data);


        return $data;
    }

    private static function getOne__handle__values($data)
    {

        if (!$data) {
            return false;
        }

        $arData = [];

        foreach (array_keys($data) as $key) {

            $value = $data[$key];

            $arData[$key] = $value;
        }

        return $arData;

    }

    private function getOne__select()
    {

        if ($this->params['fl']) {
            $fl = implode(',', $this->params['fl']);
            $this->sqlQuery = "SELECT $fl ";
        } else {
            $this->sqlQuery = 'SELECT * ';
        }

        $this->sqlQuery .= 'FROM  ' . $this->table . ' item ';

        $this->sqlQuery = $this->getWhereSql($this->sqlQuery);

        $this->sqlQuery .= 'LIMIT 1';

        $stmt = $this->dataBase->prepare($this->sqlQuery);

        $stmt->execute($this->arEx);

        return $stmt->fetch();
    }

    /**
     * Метод получает коллекцию записей из БД
     * @param bool $handle
     * @return mixed
     */
    public function getCollection($handle = false)
    {
        $this->runTime->start();

        $this->tableConfig = [];

        $data = $this->getCollection__select();

        if ($this->countItemsInPage) {

            $pagination['countAllItems'] = $this->getCollection__select(['mode' => 'getAllCount']);

            if ($pagination['countAllItems']) {
                $pagination['countAllPages'] = ceil($pagination['countAllItems'] / $this->countItemsInPage);
            } else {
                $pagination['countAllPages'] = 0;
            }

        } else {
            $pagination = false;
        }

        $items = false;

        if ($handle and $data) {

            foreach ($data as $item) {

                $handleResponse = $handle($item);

                if ($handleResponse) {
                    $item = $handleResponse;
                }

                $items[] = $item;
            }

        } else {

            $items = $data;

        }

        $this->runTime->setSqlQuery(
            $this->sqlQuery
        );

        $this->runTime->setReturn('items',
            $this->getCollection__handle($items)
        );

        if ($pagination) {

            $this->runTime->setReturn('pagination',
                $pagination
            );

        }

        return $this->runTime->end();

    }

    private function getCollection__select($params = [])
    {

        if ($params['mode'] != 'getAllCount') {

            if ($this->params['fl']) {
                $fl = implode(',', $this->params['fl']);
                $this->sqlQuery = "SELECT $fl ";
            } else {
                $this->sqlQuery = 'SELECT * ';
            }

        } else {
            $this->sqlQuery = 'SELECT COUNT(*) ';
        }


        $this->sqlQuery .= 'FROM  ' . $this->table . ' item ';

        $this->sqlQuery = $this->getWhereSql($this->sqlQuery);

        if ($params['mode'] != 'getAllCount') {

            $this->sqlQuery = $this->getOrderBySql($this->sqlQuery);

            $this->sqlQuery = $this->getLimitSql($this->sqlQuery);

        }

        $stmt = $this->dataBase->prepare($this->sqlQuery);

        $stmt->execute($this->arEx);

        $data = $stmt->fetchAll();

        if ($params['mode'] != 'getAllCount') {
            return $data;
        } else {
            if (!$data) {
                return 0;
            } else {
                return (int)$data[0]['COUNT(*)'];
            }
        }

    }

    private function getCollection__handle($data)
    {
        if (!$data) {
            return false;
        }

        $arData = [];

        foreach ($data as $item) {
            $arData[] = $item;
        }

        return $arData;

    }


    /**
     * Метод удаляет запись из БД
     * @return mixed
     */
    public function remove()
    {
        $entityId = $this->getOne()->data['id'];

        if (!$entityId) {
            return [
                'status' => 'error',
                'code' => 'recordNoExists'
            ];
        }

        $this->runTime->start();

        $this->runTime->setReturn('res',
            $this->remove__handle($entityId)
        );

        return $this->runTime->end();
    }

    private function remove__handle($entityId)
    {
        $database = new Database;

        $pdo = $database->init();

        $sql = 'DELETE FROM ' . $this->table . ' WHERE id=:id LIMIT 1';

        $stmt = $pdo->prepare($sql);

        $stmt->execute(
            [
                'id' => $entityId
            ]
        );

        return [
            'recordRemovedId' => $entityId
        ];
    }

}