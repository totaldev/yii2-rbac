<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider \yii\data\ArrayDataProvider */
/* @var $searchModel totaldev\yii\rbac\models\search\BizRuleSearch */

$this->title = Yii::t('yii2mod.rbac', 'Rules');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="box">
    <div class="box-body">

        <p>
            <?= Html::a(Yii::t('yii2mod.rbac', 'Create Rule'), ['create'], ['class' => 'btn btn-success']); ?>
        </p>

        <?php Pjax::begin(['timeout' => 5000]); ?>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'header' => Yii::t('yii2mod.rbac', 'Action'),
                    'class' => 'yii\grid\ActionColumn',
                    'headerOptions' => [
                        'width' => 70,
                    ],
                ],
                [
                    'attribute' => 'name',
                    'label' => Yii::t('yii2mod.rbac', 'Name'),
                ],
            ],
        ]);
        ?>

        <?php Pjax::end(); ?>
    </div>
</div>
