<?php

use totaldev\yii\rbac\RbacAsset;
use yii\helpers\Json;

RbacAsset::register($this);

/* @var $this yii\web\View */
/* @var $model \totaldev\yii\rbac\models\AssignmentModel */
/* @var $usernameField string */

$userName = $model->user->{$usernameField};
$this->title = Yii::t('yii2mod.rbac', 'Assignment : {0}', $userName);
$this->params['breadcrumbs'][] = ['label' => Yii::t('yii2mod.rbac', 'Assignments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $userName;
?>
<div class="box">
    <div class="box-body">

        <?= $this->render('../_dualListBox', [
            'opts' => Json::htmlEncode([
                'items' => $model->getItems(),
            ]),
            'assignUrl' => ['assign', 'id' => $model->userId],
            'removeUrl' => ['remove', 'id' => $model->userId],
        ]); ?>

    </div>
</div>
