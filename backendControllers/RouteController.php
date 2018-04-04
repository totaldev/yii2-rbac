<?php

namespace totaldev\yii\rbac\backendControllers;

use totaldev\yii\rbac\models\RouteModel;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;

/**
 * Class RouteController
 *
 * @package totaldev\yii\rbac\controllers
 */
class RouteController extends Controller
{
    /**
     * @var array route model class
     */
    public $modelClass = [
        'class' => RouteModel::class,
    ];


    public function actionAllowedLinks()
    {
        $model = Yii::createObject($this->modelClass);
        $routes = $model->getAllRoutes();
        $permissions = Yii::$app->getAuthManager()->getPermissions();
        $links = [];
        foreach ($routes as $permission => $route) {
            if (Yii::$app->getUser()->can($permission)) {
                $url = Yii::$app->getUrlManager()->createUrl($route);
                $url = rtrim($url, '*');
                $permissionName = isset($permissions[$permission]) ? $permissions[$permission]->description : '-';
                $links[$url] = "$url: $permissionName";
            }
        }

        $content = '<h1>Ваши доступы</h1>';
        $content .= "<ul>";
        foreach ($links as $url => $label) {
            $content .= "<li><a href='{$url}'>{$label}</a></li>";
        }
        $content .= "</ul>";
        return $content;
    }

    /**
     * Assign routes
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function actionAssign(): array
    {
        $routes = Yii::$app->getRequest()->post('routes', []);
        $model = Yii::createObject($this->modelClass);
        $model->addNew($routes);

        return $model->getAvailableAndAssignedRoutes();
    }

    /**
     * Lists all Route models.
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function actionIndex()
    {
        $model = Yii::createObject($this->modelClass);

        return $this->render('index', ['routes' => $model->getAvailableAndAssignedRoutes()]);
    }

    /**
     * Refresh cache of routes
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function actionRefresh(): array
    {
        $model = Yii::createObject($this->modelClass);
        $model->invalidate();

        return $model->getAvailableAndAssignedRoutes();
    }

    /**
     * Remove routes
     *
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function actionRemove(): array
    {
        $routes = Yii::$app->getRequest()->post('routes', []);
        $model = Yii::createObject($this->modelClass);
        $model->remove($routes);

        return $model->getAvailableAndAssignedRoutes();
    }

    /**
     * Returns a list of behaviors that this component should behave as.
     *
     * @return array
     */
    public function behaviors(): array
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'index' => ['get', 'post'],
                    'create' => ['post'],
                    'assign' => ['post'],
                    'remove' => ['post'],
                    'refresh' => ['post'],
                ],
            ],
            'contentNegotiator' => [
                'class' => 'yii\filters\ContentNegotiator',
                'only' => ['assign', 'remove', 'refresh'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
        ];
    }
}
