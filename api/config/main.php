<?php

$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php')
);

return [
    'id' => 'rest-api',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'modules' => [
        'v1' => [
            'class' => 'rest\versions\v1\RestModule'
        ],
    ],
    'components' => [
        'user' => [
            'identityClass' => 'common\models\User',
            'enableSession' => false,
            'loginUrl' => null,
        ],
        'response' => [
            'format' => yii\web\Response::FORMAT_JSON,
            'charset' => 'UTF-8',
        ],
        'log' => [
            'traceLevel' => 3,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'request' => [
            'class' => '\yii\web\Request',
            'enableCookieValidation' => false,
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                'OPTIONS v1/<route>' => 'v1/auth/options',
                'OPTIONS v1/<route1>/<route2>' => 'v1/auth/options',
                'POST v1/login' => 'v1/auth/login',
                'POST v1/register-device' => 'v1/auth/register-device',
                'POST v1/logout' => 'v1/user/logout',
                'POST v1/join-agent'  => 'v1/auth/join-agent',
                'POST v1/join-pharmacist'  => 'v1/auth/join-pharmacist',
                'PUT v1/reset-token'  => 'v1/auth/send-reset-token',
                'GET v1/reset-token'  => 'v1/auth/check-reset-token',
                'PUT v1/reset-password'  => 'v1/auth/reset-password',

                'GET v1/companies' => 'v1/list/companies',
                'GET v1/types' => 'v1/list/types',
                'GET v1/pharmacies' => 'v1/list/pharmacies',
                'GET v1/education' => 'v1/list/education',
                'GET v1/sex' => 'v1/list/sex',
                'GET v1/positions' => 'v1/list/positions',
                'GET v1/cities' => 'v1/list/cities',
                'GET v1/regions' => 'v1/list/regions',

                'GET v1/user'   => 'v1/user/index',
                'PUT v1/user-agent' => 'v1/user/update-profile-agent',
                'PUT v1/user-pharmacist' => 'v1/user/update-profile-pharmacist',
                'POST v1/user/photo' => 'v1/user/update-photo',
                'POST v1/user/message' => 'v1/user/send-message',
                'PUT v1/user/password' => 'v1/user/update-password',
                'PUT v1/user/notifications' => 'v1/user/notifications',
                'GET v1/user/notifications' => 'v1/user/get-notifications',

                'GET v1/banners' => 'v1/banner/index',
                'GET v1/banner/<id>' => 'v1/banner/view',

                'GET v1/surveys' => 'v1/survey/index',
                'GET v1/survey/<id>' => 'v1/survey/view',
                'POST v1/survey' => 'v1/survey/answer',
                'GET v1/survey/is-answered/<id>' => 'v1/survey/is-survey-answered',

                'GET v1/presentations/home' => 'v1/presentation/home-list',
                'GET v1/presentations/viewed' => 'v1/presentation/viewed-list',
                'GET v1/presentations/not-viewed' => 'v1/presentation/not-viewed-list',
                'GET v1/presentation/<id>' => 'v1/presentation/view',
                'POST v1/presentation' => 'v1/presentation/answer',
                'GET v1/presentation/is-viewed/<id>' => 'v1/presentation/is-presentation-viewed',
                'GET v1/presentation/comments/<presentation_id>' => 'v1/presentation/comments',
                'GET v1/presentation/comment/<id>' => 'v1/presentation/comment',
                'DELETE v1/presentation/comment/<id>' => 'v1/presentation/delete-comment',
                'POST v1/presentation/comment' => 'v1/presentation/add-comment',

                'GET v1/shop' => 'v1/shop/index',
                'GET v1/shop/<id>' => 'v1/shop/view',
                'GET v1/desires' => 'v1/shop/desires',
                'GET v1/presents' => 'v1/shop/presents',
                'GET v1/present/<id>' => 'v1/shop/present',
                'GET v1/desire/<id>' => 'v1/shop/desire',
                'POST v1/present' => 'v1/shop/add-present',
                'POST v1/desire' => 'v1/shop/add-desire',
                'DELETE v1/desire/<id>'=> 'v1/shop/delete-desire',

                'GET v1/promo/<promo>/<token>' => 'v1/promo/use-promo',
                'GET v1/cron/<key>' => 'v1/promo/cron',

                'GET v1/news' => 'v1/news/index',
                'GET v1/news/<id>' => 'v1/news/view',
                'GET v1/news/comments/<news_id>' => 'v1/news/comments',
                'GET v1/news/comment/<id>' => 'v1/news/comment',
                'POST v1/news/comment' => 'v1/news/add-comment',
                'DELETE v1/news/comment/<id>' => 'v1/news/delete-comment',

                'GET v1/seminars' => 'v1/seminar/index',
                'GET v1/seminar/<id>' => 'v1/seminar/view',
                'GET v1/seminar/comments/<seminar_id>' => 'v1/seminar/comments',
                'GET v1/seminar/comment/<id>' => 'v1/seminar/comment',
                'POST v1/seminar/comment' => 'v1/seminar/add-comment',
                'DELETE v1/seminar/comment/<id>' => 'v1/seminar/delete-comment',
                'POST v1/seminar' => 'v1/seminar/entry',

                'GET v1/vacancy' => 'v1/vacancy/index',
                'GET v1/vacancy/<id>' => 'v1/vacancy/view',
                'GET v1/vacancy/comments/<vacancy_id>' => 'v1/vacancy/comments',
                'GET v1/vacancy/comment/<id>' => 'v1/vacancy/comment',
                'POST v1/vacancy/comment' => 'v1/vacancy/add-comment',
                'DELETE v1/vacancy/comment/<id>' => 'v1/vacancy/delete-comment',
                'POST v1/vacancy' => 'v1/vacancy/entry',

                'GET v1/factories' => 'v1/factory/index',
                'GET v1/factories/all' => 'v1/factory/all',
                'GET v1/factory/<id>' => 'v1/factory/view',
                'GET v1/factory/stocks/<factory_id>' => 'v1/factory/stocks',
                'GET v1/factory/stock/<id>' => 'v1/factory/stock',
                'POST v1/factory/stock' => 'v1/factory/reply',
                'GET v1/factory/products/<factory_id>' => 'v1/factory/products',
                'GET v1/factory/product/<id>' => 'v1/factory/product',

                'GET v1/substances' => 'v1/substance/index',
                'GET v1/substance/<id>' => 'v1/substance/view',
            ],
        ],
    ],
    'params' => $params,
];
