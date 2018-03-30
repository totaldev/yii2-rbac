<?php

namespace totaldev\yii\rbac\tests\models;

use totaldev\yii\rbac\models\AuthItemModel;
use totaldev\yii\rbac\tests\TestCase;
use Yii;
use yii\rbac\Item;
use yii\rbac\Role;

/**
 * Class RoleTest
 *
 * @package totaldev\yii\rbac\tests\models
 */
class RoleTest extends TestCase
{
    public function testCreateRole()
    {
        $model = new AuthItemModel();
        $model->type = Item::TYPE_ROLE;
        $model->name = 'admin';
        $model->description = 'admin role';

        $this->assertTrue($model->save());
        $this->assertInstanceOf(Role::class, Yii::$app->authManager->getRole('admin'));

        return Yii::$app->authManager->getRole('admin');
    }

    /**
     * @depends testCreateRole
     *
     * @param $role
     */
    public function testRemoveRole($role)
    {
        $this->assertTrue(Yii::$app->authManager->remove($role));
    }
}
