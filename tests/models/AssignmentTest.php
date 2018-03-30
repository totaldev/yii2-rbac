<?php

namespace totaldev\yii\rbac\tests\models;

use totaldev\yii\rbac\models\AssignmentModel;
use totaldev\yii\rbac\models\AuthItemModel;
use totaldev\yii\rbac\tests\data\User;
use totaldev\yii\rbac\tests\TestCase;
use Yii;
use yii\base\Exception;
use yii\rbac\Item;

/**
 * Class AssignmentTest
 *
 * @package totaldev\yii\rbac\tests\models
 */
class AssignmentTest extends TestCase
{
    /**
     * @var string
     */
    private $_permissionName = 'viewArticles';
    /**
     * @var string
     */
    private $_roleName = 'admin';

    // Tests :

    public function testAssignPermission()
    {
        $this->createPermission();

        $user = User::find()->one();
        $model = new AssignmentModel($user);

        $this->assertTrue($model->assign([$this->_permissionName]));
        $this->assertArrayHasKey($this->_permissionName, Yii::$app->authManager->getAssignments($user->id));

        return $model;
    }

    public function testAssignRole()
    {
        $this->createRole();

        $user = User::find()->one();
        $model = new AssignmentModel($user);

        $this->assertTrue($model->assign([$this->_roleName]));
        $this->assertArrayHasKey($this->_roleName, Yii::$app->authManager->getAssignments($user->id));

        return $model;
    }

    /**
     * @depends testAssignRole
     * @depends testAssignPermission
     *
     * @param AssignmentModel $role
     * @param AssignmentModel $permission
     */
    public function testGetItems(AssignmentModel $role, AssignmentModel $permission)
    {
        $this->assertArrayHasKey($this->_roleName, $role->getItems()['assigned']);
        $this->assertArrayHasKey($this->_permissionName, $permission->getItems()['assigned']);
    }

    /**
     * @depends testAssignPermission
     *
     * @param AssignmentModel $model
     */
    public function testRevokePermission(AssignmentModel $model)
    {
        $this->assertTrue($model->revoke([$this->_permissionName]));
    }

    /**
     * @depends testAssignRole
     *
     * @param AssignmentModel $model
     */
    public function testRevokeRole(AssignmentModel $model)
    {
        $this->assertTrue($model->revoke([$this->_roleName]));
    }

    /**
     * Create permission for testing purposes
     *
     * @throws Exception
     */
    private function createPermission()
    {
        $model = new AuthItemModel();
        $model->type = Item::TYPE_ROLE;
        $model->name = $this->_permissionName;

        if (!$model->save()) {
            throw new Exception("A Permission '{$this->_permissionName}' has not been created.");
        }
    }

    /**
     * Create role for testing purposes
     *
     * @throws Exception
     */
    private function createRole()
    {
        $model = new AuthItemModel();
        $model->type = Item::TYPE_ROLE;
        $model->name = $this->_roleName;

        if (!$model->save()) {
            throw new Exception("A Role '{$this->_roleName}' has not been created.");
        }
    }
}
