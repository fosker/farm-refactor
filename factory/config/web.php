<?php

$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php')
);

$config = [
    'id' => 'basic',
    'language'=>'ru',
    'controllerNamespace' => 'factory\controllers',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'components' => [
        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'locale' => 'ru_RU',
            'datetimeFormat' => 'php: d.m.Y, H:i:s',
            'defaultTimeZone' => 'Europe/Minsk'
        ],
        'request' => [
            'cookieValidationKey' => 'm13suiePCS1k0k6G3_Nan2NQkOKPGMDr',
        ],
        'apns' => [
            'class' => 'bryglen\apnsgcm\Apns',
            'environment' => \bryglen\apnsgcm\Apns::ENVIRONMENT_PRODUCTION,
            'pemFile' => dirname(__DIR__).'/extensions/Shogunate_Farma_aps.pem',
            // 'retryTimes' => 3,
            'options' => [
                'sendRetryTimes' => 5
            ],
            'enableLogging'=> true,
        ],
        'gcm' => [
            'class' => 'bryglen\apnsgcm\Gcm',
            'apiKey' => 'AIzaSyB76uLdh9i7t-UYoTL3Cqb-n3cszkX7HcA',
            'enableLogging'=> true,
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'class' => 'yii\web\User',
            'identityClass' => 'factory\models\Admin',
            'enableAutoLogin' => true,
            'loginUrl'=>['/login'],
            'idParam'=>'__adminId',
            'identityCookie'=>[
                'name' => '_admin',
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '<controller:\w+>/<id:\d+>' => '<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
                'login' => 'auth/login',
                'logout' => 'auth/logout',
                '/' => 'site/index'
            ],
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
