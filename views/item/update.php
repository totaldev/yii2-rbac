<?php

/* @var $this yii\web\View */
/* @var $model \totaldev\yii\rbac\models\AuthItemModel */

$context = $this->context;
$labels = $this->context->getLabels();
$this->title = Yii::t('yii2mod.rbac', 'Update ' . $labels['Item'] . ' : {0}', $model->name);
$this->params['breadcrumbs'][] = ['label' => Yii::t('yii2mod.rbac', $labels['Items']), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->name]];
$this->params['breadcrumbs'][] = Yii::t('yii2mod.rbac', 'Update');
?>
<div class="box">
    <div class="box-body">
        <?= $this->render('_form', [
            'model' => $model,
        ]); ?>
    </div>
</div>