<?php

namespace totaldev\yii\rbac\components;

use yii;
use yii\base\Component;

/**
 * Class RbacMenu
 * !!! Класс предназначен для хранения общей схемы навигации и схемы доступных пунктов меню ТОЧКА
 * !!! Ответственность ТОЛЬКО за разрешение или запрет доступа к пункту меню
 *
 * Configuration example:
 * ~~~
 * 'components' => [
 *      'menu' => [
 *          'class' => '\common\components\RbacMenu',
 *          'items' => [
 *              [
 *                  'label' => 'Label',
 *                  'permission' => '',
 *                  .....
 *              ]
 *              ......
 *          ]
 *      ]
 * ],
 * ~~~
 *
 * Так же есть возможность регистрировать элементы меню динамически в bootstrap модуля например
 * ~~~
 * Yii::$app->menu['newElement'] = [...];
 * ~~~
 *
 * @package common\components
 */
class RbacMenu extends Component implements \ArrayAccess, \Iterator
{
    /**
     * Набор разрешенных элементов
     * @var MenuElement[]
     */
    protected $_allowed = [];
    /**
     * Весь набор меню
     * @var array
     */
    protected $_items = [];

    /** @inheritdoc */
    public function current()
    {
        return current($this->_allowed);
    }

    /**
     * Получение меню, измененного в соответствии с правилами доступа текущего пользователя
     * @return MenuElement[]
     */
    public function getAllowed()
    {
        if (empty($this->_allowed)) {
            $this->rebuildAllowed();
        }
        return $this->_allowed;
    }

    /**
     * Получение полного меню, без учета правил доступа
     * @return array
     */
    public function getItems()
    {
        return $this->_items;
    }

    /**
     * Инициализация меню набором пунктов. Меню будет модифицировано с учетом наличия
     * прав доступа у текущего пользователя.
     * @param $items
     */
    public function setItems($items)
    {
        $this->_items = $items;
        $this->_allowed = $this->buildAllowedItems($items);
    }

    /** @inheritdoc */
    public function key()
    {
        return key($this->_allowed);
    }

    /** @inheritdoc */
    public function next()
    {
        next($this->_allowed);
    }

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return isset($this->_allowed[$offset]);
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        if ($this->offsetExists($offset)) {
            return $this->_allowed[$offset];
        }

        return null;
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        $allowed = $this->tryAllowedItem($value);
        if (empty($offset)) {
            $this->_items[] = $value;
            $offset = end($this->_items);
            if ($allowed) {
                $this->_allowed[$offset] = $allowed;
            }
        } else {
            $this->_items[$offset] = $value;
            if ($allowed) {
                $this->_allowed[$offset] = $allowed;
            }
        }
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        if (isset($this->_allowed[$offset])) {
            unset($this->_allowed[$offset]);
        }
        if (isset($this->_items[$offset])) {
            unset($this->_items[$offset]);
        }
    }

    public function rebuildAllowed()
    {
        $this->_allowed = $this->buildAllowedItems($this->_items);
    }

    /** @inheritdoc */
    public function rewind()
    {
        reset($this->_allowed);
    }

    /** @inheritdoc */
    public function valid()
    {
        return isset($this->_allowed[$this->key()]);
    }

    /**
     * Модифицирует меню с учетом наличия у текущего пользователя прав доступа
     * @param array $items
     * @return array
     */
    protected function buildAllowedItems($items)
    {
        foreach ($items as $index => &$menuElement) {
            $menuElement = $this->tryAllowedItem($menuElement);
            if (!$menuElement) {
                unset($items[$index]);
            }
        }

        return $items;
    }

    /**
     * @param array $menuElement
     * @return null|MenuElement
     */
    protected function tryAllowedItem($menuElement)
    {
        // $menu['itemName']['property'] = 'someValue';
        // Indirect modification of overloaded element of common\components\RbacMenu has no effect
        // именно поэтому здесь данная конструкция
        $menuElement = new MenuElement($menuElement); // hint for real full array access
        if (!isset($menuElement['options'])) {
            $menuElement['options'] = [];
        }

        if (isset($menuElement['items'])) {
            $menuElement['items'] = $this->buildAllowedItems($menuElement['items']);
        }

        if (
            isset($menuElement['permission']) && !$this->checkPermissions($menuElement['permission'])
            || isset($menuElement['url']) && !$this->checkRoute($menuElement['url'][0])
        ) {
            if (empty($menuElement['items'])) {
                return null;
            } else {
                $menuElement['url'] = ['#'];
            }
        }
        return $menuElement;
    }

    /**
     * Проверяет, доступны ли пользователю роли $permission.
     * @param string|array $permissions Набор ролей, которые должны проверяться
     * @return boolean Результат проверки
     */
    protected function checkPermissions($permissions)
    {
        // Нет ограничений по ролям, доступ разрешен
        if (empty($permissions)) {
            return true;
        }

        $permissions = (array)$permissions;
        foreach ($permissions as $permission) {
            // Проверку осуществляем до первого разрешения (ИЛИ)
            if (Yii::$app->user->can($permission) === true) {
                return true;
            }
        }

        return false;
    }

    protected function checkRoute($route, $params = [])
    {
        $applicationId = Yii::$app->id;
        if (
            Yii::$app->user->can("#{$applicationId}/" . trim($route, '/'), $params)
            || Yii::$app->user->can("#{$applicationId}/" . trim($route, '/') . '/index', $params)
            || Yii::$app->user->can("#{$applicationId}/*", $params)
            || Yii::$app->user->can("*", $params)
        ) {
            return true;
        }
        do {
            $permission = "#{$applicationId}" . rtrim($route, '/') . '/*';
            if (Yii::$app->user->can($permission)) {
                return true;
            }
            $parts = explode('/', $route);
            unset($parts[count($parts) - 1]);
            $route = implode('/', $parts);
        } while (!empty($route));

        return false;
    }
}

