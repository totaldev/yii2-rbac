<?php
return [
    'label' => Yii::t('yii2mod.rbac', 'RBAC'),
    'url' => ['/rbac'],
    'items' => [
        [
            'label' => Yii::t('yii2mod.rbac', 'Assignments'),
            'url' => ['/rbac/assignment'],
        ],
        [
            'label' => Yii::t('yii2mod.rbac', 'Roles'),
            'url' => ['/rbac/role'],
        ],
        [
            'label' => Yii::t('yii2mod.rbac', 'Permissions'),
            'url' => ['/rbac/permission'],
        ],
        [
            'label' => Yii::t('yii2mod.rbac', 'Routes'),
            'url' => ['/rbac/route'],
        ],
        [
            'label' => Yii::t('yii2mod.rbac', 'Rules'),
            'url' => ['/rbac/rule'],
        ],
    ],
];