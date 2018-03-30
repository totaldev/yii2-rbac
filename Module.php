<?php

namespace totaldev\yii\rbac;

use totaldev\yii\rbac\console\MigrateController;
use totaldev\yii\rbac\models\RouteModel;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\console\Application as ConsoleApplication;
use yii\console\Exception as ConsoleException;
use yii\web\Application as WebApplication;

/**
 * GUI manager for RBAC.
 *
 * Use [[\yii\base\Module::$controllerMap]] to change property of controller.
 *
 * ```php
 * 'controllerMap' => [
 *     'assignment' => [
 *         'class' => 'totaldev\yii\rbac\controllers\AssignmentController',
 *         'userIdentityClass' => 'app\models\User',
 *         'searchClass' => [
 *              'class' => 'totaldev\yii\rbac\models\search\AssignmentSearch',
 *              'pageSize' => 10,
 *         ],
 *         'idField' => 'id',
 *         'usernameField' => 'username'
 *         'gridViewColumns' => [
 *              'id',
 *              'username',
 *              'email'
 *         ],
 *     ],
 * ],
 * ```php
 */
class Module extends \totaldev\yii\usefull\base\Module
{
    const NAME = 'rbac';
    /** @var string the namespace that controller classes are in */
    public $controllerNamespace = null;
    /** @var string the default route of this module. Defaults to 'default' */
    public $defaultRoute = 'assignment';
    /**
     * dependency inversion
     * @see \totaldev\yii\rbac\models\RouteModel::$dispatchApplications
     */
    public $dispatchApplications = [];


    /**
     * @param \yii\base\Application $app
     * @throws Exception
     */
    public function bootstrap($app)
    {
        if (empty($this->dispatchApplications)) {
            throw new InvalidConfigException('You must set $dispatchApplications');
        }
        RouteModel::$dispatchApplications = $this->dispatchApplications;

        if ($app->i18n) {
            if (!empty($app->i18n->translations['yii2mod.rbac'])) {
                throw new Exception('Translation with this category already exists');
            }
            $app->i18n->translations['yii2mod.rbac'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => $this->getBasePath() . '/messages',
                'sourceLanguage' => 'en-US',
                'fileMap' => [
                    'yii2mod.rbac' => 'yii2mod.rbac.php',
                ],
            ];
        }

        if ($app instanceof WebApplication) {
            $this->webBootstrap($app);
        } elseif ($app instanceof ConsoleApplication) {
            $this->consoleBootstrap($app);
        }
    }

    public function init()
    {
        parent::init();
        $this->setViewPath('@vendor/totaldev/yii2-rbac/views');
    }

    /**
     * @param ConsoleApplication $app
     * @throws ConsoleException
     */
    protected function consoleBootstrap(ConsoleApplication $app)
    {
        parent::consoleBootstrap($app);
        $this->controllerMap['migrate'] = [
            'class' => MigrateController::class,
            'migrationTable' => '{{%auth_migration}}',
            'migrationPath' => '@common/migrations/rbac',
        ];
    }
}
