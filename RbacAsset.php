<?php

namespace totaldev\yii\rbac;

use yii\web\AssetBundle;

/**
 * Class RbacAsset
 *
 * @package totaldev\yii\rbac
 */
class RbacAsset extends AssetBundle
{
    public $css = [
        'css/rbac.css',
    ];
    /**
     * @var array
     */
    public $depends = [
        'yii\web\YiiAsset',
    ];
    /**
     * @var array
     */
    public $js = [
        'js/rbac.js',
    ];
    /**
     * @var string
     */
    public $sourcePath = '@vendor/totaldev/yii2-rbac/assets';
}
