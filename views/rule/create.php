<?php

/* @var $this  yii\web\View */
/* @var $model \totaldev\yii\rbac\models\BizRuleModel */

$this->title = Yii::t('yii2mod.rbac', 'Create Rule');
$this->params['breadcrumbs'][] = ['label' => Yii::t('yii2mod.rbac', 'Rules'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="box">
    <div class="box-body">
        <?= $this->render('_form', [
            'model' => $model,
        ]); ?>
    </div>
</div>