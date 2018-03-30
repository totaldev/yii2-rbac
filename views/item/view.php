<?php

use totaldev\yii\rbac\RbacAsset;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\DetailView;

RbacAsset::register($this);

/* @var $this yii\web\View */
/* @var $model \totaldev\yii\rbac\models\AuthItemModel */

$labels = $this->context->getLabels();
$this->title = Yii::t('yii2mod.rbac', $labels['Item'] . ' : {0}', $model->name);
$this->params['breadcrumbs'][] = ['label' => Yii::t('yii2mod.rbac', $labels['Items']), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->name;

?>
<div class="box">
    <div class="box-body">
        <p>
            <?= Html::a(Yii::t('yii2mod.rbac', 'Update'), ['update', 'id' => $model->name], ['class' => 'btn btn-primary']); ?>
            <?= Html::a(Yii::t('yii2mod.rbac', 'Delete'), ['delete', 'id' => $model->name], [
                'class' => 'btn btn-danger',
                'data-confirm' => Yii::t('yii2mod.rbac', 'Are you sure to delete this item?'),
                'data-method' => 'post',
            ]); ?>
            <?= Html::a(Yii::t('yii2mod.rbac', 'Create'), ['create'], ['class' => 'btn btn-success']); ?>
        </p>
        <div class="row">
            <div class="col-sm-12">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'name',
                        'description:ntext',
                        'ruleName',
                        'data:ntext',
                    ],
                ]); ?>
            </div>
        </div>
        <?= $this->render('../_dualListBox', [
            'opts' => Json::htmlEncode([
                'items' => $model->getItems(),
            ]),
            'assignUrl' => ['assign', 'id' => $model->name],
            'removeUrl' => ['remove', 'id' => $model->name],
        ]); ?>
    </div>
</div>
