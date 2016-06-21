<?php
use kartik\sidenav\SideNav;
use yii\helpers\Url;

echo SideNav::widget([
    'type' => SideNav::TYPE_INFO,
    'encodeLabels' => false,
    'heading' => "<img src = '".Yii::$app->user->identity->avatarPath."' class='img-responsive' style = 'width: 100%'>",
    'items' => [
        ['label' => 'Домой', 'icon' => 'home', 'url' => ['/']],
        ['label' => 'Профиль', 'icon' => 'user', 'items' => [
            ['label' => "Мой профиль (".Yii::$app->user->identity->login.")", 'url' => ['/profile']],
            ['label' => 'Изменить пароль', 'url' => ['/profile/update-password']],
            ['label' => 'Изменить аватар', 'url' => ['/profile/update-avatar']],
        ]],
        ['label' => 'Видео', 'icon' => 'expand', 'url' => ['/videos']],
        ['label' => 'Новости', 'icon' => 'bullhorn', 'url' => ['/news'], 'visible' => Yii::$app->user->identity->isAgent],
    ],
]);