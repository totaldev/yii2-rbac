<?php

namespace totaldev\yii\rbac\tests\models;

use totaldev\yii\rbac\models\BizRuleModel;
use totaldev\yii\rbac\rules\GuestRule;
use totaldev\yii\rbac\tests\TestCase;
use Yii;
use yii\rbac\Rule;

/**
 * Class BizRuleTest
 *
 * @package totaldev\yii\rbac\tests\models
 */
class BizRuleTest extends TestCase
{
    public function testCreateRule()
    {
        $model = new BizRuleModel();
        $model->name = 'guest';
        $model->className = GuestRule::class;

        $this->assertTrue($model->save());

        $rule = Yii::$app->authManager->getRule($model->name);
        $this->assertInstanceOf(Rule::class, $rule);

        return $rule;
    }

    /**
     * @depends testCreateRule
     *
     * @param $rule
     */
    public function testRemoveRule($rule)
    {
        $this->assertTrue(Yii::$app->authManager->remove($rule));
    }

    public function testTryToCreateRuleWithInvalidClassName()
    {
        $model = new BizRuleModel();
        $model->name = 'guest';
        $model->className = 'invalid className';

        $this->assertFalse($model->save());
        $this->assertArrayHasKey('className', $model->getErrors());
    }
}
