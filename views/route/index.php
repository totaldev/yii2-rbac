<?php

use totaldev\yii\rbac\RbacRouteAsset;
use yii\helpers\Html;
use yii\helpers\Json;

RbacRouteAsset::register($this);

/* @var $this yii\web\View */
/* @var $routes array */

$this->title = Yii::t('yii2mod.rbac', 'Routes');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="box">
    <div class="box-body">
        <?= Html::a(Yii::t('yii2mod.rbac', 'Refresh'), ['refresh'], [
            'class' => 'btn btn-primary',
            'id' => 'btn-refresh',
        ]); ?>
        <?= $this->render('../_dualListBox', [
            'opts' => Json::htmlEncode([
                'items' => $routes,
            ]),
            'assignUrl' => ['assign'],
            'removeUrl' => ['remove'],
        ]); ?>
    </div>
</div>