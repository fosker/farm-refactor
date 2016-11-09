<?php

return [
    'id' => 'test',
    'controllerNamespace' => 'test\controllers',
    'basePath' => dirname(__DIR__),
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'request' => [
            'enableCookieValidation' => false,
        ],
    ],
    'bootstrap' => ['gii'],
    'modules' => [
        'gii' => ['class' => 'yii\gii\Module'],
    ],
];