<?php

namespace totaldev\yii\rbac\backendControllers;

use kartik\growl\Growl;
use totaldev\yii\rbac\models\BizRuleModel;
use totaldev\yii\rbac\models\search\BizRuleSearch;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Class RuleController
 *
 * @package totaldev\yii\rbac\controllers
 */
class RuleController extends Controller
{
    /**
     * @var string search class name for rules search
     */
    public $searchClass = [
        'class' => BizRuleSearch::class,
    ];

    /**
     * Creates a new Rule item.
     *
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new BizRuleModel();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->addFlash('success', [
                'type' => Growl::TYPE_SUCCESS,
                'message' => Yii::t('yii2mod.rbac', 'Rule has been saved.'),
            ]);

            return $this->redirect(['view', 'id' => $model->name]);
        }

        return $this->render('create', ['model' => $model]);
    }

    /**
     * Deletes an existing Rule item.
     *
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function actionDelete(string $id)
    {
        $model = $this->findModel($id);
        Yii::$app->authManager->remove($model->item);
        Yii::$app->session->addFlash('success', [
            'type' => Growl::TYPE_SUCCESS,
            'message' => Yii::t('yii2mod.rbac', 'Rule has been deleted.'),
        ]);

        return $this->redirect(['index']);
    }

    /**
     * List of all rules
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = Yii::createObject($this->searchClass);
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Updates an existing Rule item.
     *
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function actionUpdate(string $id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->addFlash('success', [
                'type' => Growl::TYPE_SUCCESS,
                'message' => Yii::t('yii2mod.rbac', 'Rule has been saved.'),
            ]);

            return $this->redirect(['view', 'id' => $model->name]);
        }

        return $this->render('update', ['model' => $model]);
    }

    /**
     * Displays a single Rule item.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function actionView(string $id)
    {
        $model = $this->findModel($id);

        return $this->render('view', ['model' => $model]);
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
                    'index' => ['get'],
                    'view' => ['get'],
                    'create' => ['get', 'post'],
                    'update' => ['get', 'post'],
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Finds the BizRuleModel based on its primary key value.
     *
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param string $id
     *
     * @return BizRuleModel the loaded model
     *
     * @throws \yii\web\NotFoundHttpException
     */
    protected function findModel(string $id)
    {
        $item = Yii::$app->authManager->getRule($id);

        if (!empty($item)) {
            return new BizRuleModel($item);
        }

        throw new NotFoundHttpException(Yii::t('yii2mod.rbac', 'The requested page does not exist.'));
    }
}
