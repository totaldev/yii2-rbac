<?php

namespace totaldev\yii\rbac\backendControllers;

use totaldev\yii\rbac\models\AssignmentModel;
use totaldev\yii\rbac\models\search\AssignmentSearch;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class AssignmentController
 *
 * @package totaldev\yii\rbac\controllers
 */
class AssignmentController extends Controller
{
    /**
     * @var array assignments GridView columns
     */
    public $gridViewColumns = [];
    /**
     * @var string id column name
     */
    public $idField = 'id';
    /**
     * @var string search class name for assignments search
     */
    public $searchClass = [
        'class' => AssignmentSearch::class,
    ];
    /**
     * @var \yii\web\IdentityInterface the class name of the [[identity]] object
     */
    public $userIdentityClass;
    /**
     * @var string username column name
     */
    public $usernameField = 'username';

    /**
     * Assign items
     *
     * @param int $id
     *
     * @return array
     */
    public function actionAssign(int $id)
    {
        $items = Yii::$app->getRequest()->post('items', []);
        $assignmentModel = $this->findModel($id);
        $assignmentModel->assign($items);

        return $assignmentModel->getItems();
    }

    /**
     * List of all assignments
     *
     * @return string
     */
    public function actionIndex()
    {
        /* @var AssignmentSearch */
        $searchModel = Yii::createObject($this->searchClass);

        if ($searchModel instanceof AssignmentSearch) {
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $this->userIdentityClass, $this->idField, $this->usernameField);
        } else {
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        }

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'gridViewColumns' => $this->gridViewColumns,
        ]);
    }

    /**
     * Remove items
     *
     * @param int $id
     *
     * @return array
     */
    public function actionRemove(int $id)
    {
        $items = Yii::$app->getRequest()->post('items', []);
        $assignmentModel = $this->findModel($id);
        $assignmentModel->revoke($items);

        return $assignmentModel->getItems();
    }

    /**
     * Displays a single Assignment model.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function actionView(int $id)
    {
        $model = $this->findModel($id);

        return $this->render('view', [
            'model' => $model,
            'usernameField' => $this->usernameField,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function behaviors(): array
    {
        return [
            'verbs' => [
                'class' => 'yii\filters\VerbFilter',
                'actions' => [
                    'index' => ['get'],
                    'view' => ['get'],
                    'assign' => ['post'],
                    'remove' => ['post'],
                ],
            ],
            'contentNegotiator' => [
                'class' => 'yii\filters\ContentNegotiator',
                'only' => ['assign', 'remove'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if ($this->userIdentityClass === null) {
            $this->userIdentityClass = Yii::$app->user->identityClass;
        }

        if (empty($this->gridViewColumns)) {
            $this->gridViewColumns = [
                $this->idField,
                $this->usernameField,
            ];
        }
    }

    /**
     * Finds the Assignment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param int $id
     *
     * @return AssignmentModel the loaded model
     *
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $id)
    {
        $class = $this->userIdentityClass;

        if (($user = $class::findIdentity($id)) !== null) {
            return new AssignmentModel($user);
        }

        throw new NotFoundHttpException(Yii::t('yii2mod.rbac', 'The requested page does not exist.'));
    }
}
