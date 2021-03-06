<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => 'ФармПроизводители',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => [
            Yii::$app->user->isGuest ?
                ['label' => 'Войти', 'url' => ['/login']] :
                [
                    'label' =>  Yii::$app->user->identity->login,
                    'items' => [
                        [
                            'label' => 'Мой профиль',
                            'url' => ['/profile']
                        ],
                        [
                            'label' => 'Выйти',
                            'url' => ['/logout'],
                            'linkOptions' => ['data-method' => 'post']
                        ],
                    ],
                ],
            [
                'label' => 'Новости',
                'url' => ['/news'],
                'visible' => Yii::$app->user->isGuest ? false : true
            ],
            [
                'label' => 'Оповещения',
                'items' => [
                    [
                        'label' => 'Все оповещения',
                        'url' => ['/push/all'],
                        'visible' => Yii::$app->user->isGuest ? false : true
                    ],
                    [
                        'label' => 'Оповещения группам',
                        'url' => ['/push/push-groups'],
                        'visible' => Yii::$app->user->isGuest ? false : true
                    ],
                    [
                        'label' => 'Оповещения пользователям',
                        'url' => ['/push/push-users'],
                        'visible' => Yii::$app->user->isGuest ? false : true
                    ],
                ],
                'visible' => Yii::$app->user->isGuest ? false : true
            ],
        ]
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-right">Футер</p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
