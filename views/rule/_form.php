<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \totaldev\yii\rbac\models\BizRuleModel */
/* @var $form ActiveForm */
?>

<div class="rule-item-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 64]); ?>

    <?= $form->field($model, 'className')->textInput(); ?>

    <div class="form-group">
        <?= Html::submitButton(
            $model->getIsNewRecord() ? Yii::t('yii2mod.rbac', 'Create') : Yii::t('yii2mod.rbac', 'Update'),
            [
                'class' => $model->getIsNewRecord() ? 'btn btn-success' : 'btn btn-primary',
            ]
        ); ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
