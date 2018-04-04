<?php

namespace totaldev\yii\rbac\filters;

use Yii;
use yii\base\Action;
use yii\base\Module;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * Class AccessControl
 *
 * @package totaldev\yii\rbac\filters
 */
class AccessControl extends \yii\filters\AccessControl
{
    /**
     * @var array list of actions that not need to check access
     */
    public $allowActions = [];
    /**
     * @var array
     */
    public $params = [];

    /**
     * @inheritdoc
     */
    public function beforeAction($action): bool
    {
        $controller = $action->controller;
        $params = ArrayHelper::getValue($this->params, $action->id, []);
        $applicationId = Yii::$app->id;

        // full access
        if (Yii::$app->user->can("*", $params)) {
            return true;
        }

        if (Yii::$app->user->can("#{$applicationId}/" . $action->getUniqueId(), $params)) {
            return true;
        }

        do {
            if (Yii::$app->user->can("#{$applicationId}/" . ltrim($controller->getUniqueId() . '/*', '/'))) {
                return true;
            }
            $controller = $controller->module;
        } while ($controller !== null);

        return parent::beforeAction($action);
    }

    /**
     * @inheritdoc
     */
    protected function isActive($action): bool
    {
        if ($this->isErrorPage($action) || $this->isLoginPage($action)) {
            return false;
        }

        return parent::isActive($action);
    }

    /**
     * Returns a value indicating whether a current url equals `errorAction` property of the ErrorHandler component
     *
     * @param Action $action
     *
     * @return bool
     */
    private function isErrorPage(Action $action): bool
    {
        if ($action->getUniqueId() === Yii::$app->getErrorHandler()->errorAction) {
            return true;
        }

        return false;
    }

    /**
     * Returns a value indicating whether a current url equals `loginUrl` property of the User component
     *
     * @param Action $action
     *
     * @return bool
     */
    private function isLoginPage(Action $action): bool
    {
        $loginUrl = trim(Url::to(Yii::$app->user->loginUrl), '/');

        if (Yii::$app->user->isGuest && $action->getUniqueId() === $loginUrl) {
            return true;
        }

        return false;
    }
}
