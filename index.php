<?php

include 'graphene/graphene.php';

use \Graphene\System\Builders\ModulesBuilder;
use \Graphene\System\Builders\ModelsBuilder;

ModulesBuilder::createModule('Пользователи', 'Users');
ModulesBuilder::createModule('Каталог', 'Catalog');


ModelsBuilder::createModel('Пользователи', 'Users', 'Users');
ModelsBuilder::createModel('Категории', 'Categories', 'Catalog');
ModelsBuilder::createModel('Товары', 'Products', 'Catalog', 'cataloged');

\Graphene\System\Managers\ModelsDatabaseMaster::run();