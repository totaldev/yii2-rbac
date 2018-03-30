<?php

use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider \yii\data\ArrayDataProvider */
/* @var $searchModel \totaldev\yii\rbac\models\search\AuthItemSearch */

$labels = $this->context->getLabels();
$this->title = Yii::t('yii2mod.rbac', $labels['Items']);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box">
    <div class="box-body">
        <p>
            <?= Html::a(Yii::t('yii2mod.rbac', 'Create ' . $labels['Item']), ['create'], ['class' => 'btn btn-success']); ?>
        </p>
        <?php Pjax::begin(['timeout' => 5000, 'enablePushState' => false]); ?>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                [
                    'class' => 'yii\grid\ActionColumn',
                    'headerOptions' => [
                        'width' => 70,
                    ],
                ],
                [
                    'attribute' => 'name',
                    'label' => Yii::t('yii2mod.rbac', 'Name'),
                ],
                [
                    'attribute' => 'ruleName',
                    'label' => Yii::t('yii2mod.rbac', 'Rule Name'),
                    'filter' => ArrayHelper::map(Yii::$app->getAuthManager()->getRules(), 'name', 'name'),
                    'filterInputOptions' => ['class' => 'form-control', 'prompt' => Yii::t('yii2mod.rbac', 'Select Rule')],
                ],
                [
                    'attribute' => 'description',
                    'format' => 'ntext',
                    'label' => Yii::t('yii2mod.rbac', 'Description'),
                ],
            ],
        ]); ?>

        <?php Pjax::end(); ?>
    </div>
</div>