<?php

$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php')
);

$config = [
    'id' => 'app',
    'language'=>'ru',
    'controllerNamespace' => 'app\controllers',
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
        'user' => [
            'class' => 'yii\web\User',
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'loginUrl'=>['/login'],
            'idParam'=>'__userId',
            'identityCookie'=>[
                'name' => '_user',
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
                'agent-request' => 'auth/agent-request',
                'logout' => 'auth/logout',
                '/' => 'main/index',
                'terms' => 'main/terms'
            ],
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    /*$config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];*/

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
