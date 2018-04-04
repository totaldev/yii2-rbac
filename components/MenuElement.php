<?php

namespace totaldev\yii\rbac\components;

/**
 * данная сущность необходима в таком виде т.к. например ArrayHelper::getValue($item, 'options', 'default')
 * видит что item является объектом а не массивом и соотв. общается с ним как с объектом
 * соотв. в ином случае будет возвращать дефолтное значение
 */
class MenuElement extends \ArrayObject
{

    public function __construct($config = [])
    {
        parent::__construct($config);
    }

    public function __get($key)
    {
        if (key_exists($key, $this)) {
            return $this[$key];
        }
        return null;
    }

    public function __isset($key)
    {
        return key_exists($key, $this);
    }
}