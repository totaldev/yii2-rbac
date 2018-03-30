<?php

namespace totaldev\yii\rbac\backendControllers;

use totaldev\yii\rbac\base\ItemController;
use yii\rbac\Item;

/**
 * Class RoleController
 *
 * @package totaldev\yii\rbac\controllers
 */
class RoleController extends ItemController
{
    /**
     * @var array
     */
    protected $labels = [
        'Item' => 'Role',
        'Items' => 'Roles',
    ];
    /**
     * @var int
     */
    protected $type = Item::TYPE_ROLE;
}
