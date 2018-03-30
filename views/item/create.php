<?php

/* @var $this yii\web\View */
/* @var $model \totaldev\yii\rbac\models\AuthItemModel */

$labels = $this->context->getLabels();
$this->title = Yii::t('yii2mod.rbac', 'Create ' . $labels['Item']);
$this->params['breadcrumbs'][] = ['label' => Yii::t('yii2mod.rbac', $labels['Items']), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box">
    <div class="box-body">
        <?= $this->render('_form', [
            'model' => $model,
        ]); ?>
    </div>
</div>