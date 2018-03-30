<?php

namespace totaldev\yii\rbac\backendControllers;

use totaldev\yii\rbac\base\ItemController;
use yii\rbac\Item;

/**
 * Class PermissionController
 *
 * @package totaldev\yii\rbac\controllers
 */
class PermissionController extends ItemController
{
    /**
     * @var array
     */
    protected $labels = [
        'Item' => 'Permission',
        'Items' => 'Permissions',
    ];
    /**
     * @var int
     */
    protected $type = Item::TYPE_PERMISSION;
}
