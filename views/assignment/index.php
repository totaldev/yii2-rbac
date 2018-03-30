<?php

use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;

/* @var $this \yii\web\View */
/* @var $gridViewColumns array */
/* @var $dataProvider \yii\data\ArrayDataProvider */
/* @var $searchModel \totaldev\yii\rbac\models\search\AssignmentSearch */

$this->title = Yii::t('yii2mod.rbac', 'Assignments');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box">
    <div class="box-body">

        <?php Pjax::begin(['timeout' => 5000]); ?>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => ArrayHelper::merge([
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{view}',
                    'headerOptions' => [
                        'width' => 70,
                    ],
                ]
            ], $gridViewColumns),
        ]); ?>

        <?php Pjax::end(); ?>
    </div>
</div>
