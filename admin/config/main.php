<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php')
);

$config =  [
    'id' => 'admin',
    'language'=>'ru',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'layout'=>'admin',
    'defaultRoute' => 'main/index',
    'components' => [
        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'locale' => 'ru_RU',
            'datetimeFormat' => 'php: d.m.Y, H:i:s',
            'defaultTimeZone' => 'Europe/Minsk'
        ],
        'admin' => [
            'class' => 'yii\web\User',
            'identityClass' => 'backend\models\Admin',
            'enableAutoLogin' => true,
            'loginUrl'=>['auth/login'],
            'idParam'=>'__adminId',
            'identityCookie'=>[
                'name' => '_admin',
            ],
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
    ],
    'params' => $params,
];

if (!YII_ENV_TEST) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;