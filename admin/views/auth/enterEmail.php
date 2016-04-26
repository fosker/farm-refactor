<?php

use yii\helpers\Html;
use yii\bootstrap\NavBar;
use backend\assets\AppAsset;

$this->title .= 'Восстановление пароля';

AppAsset::register($this);

$this->beginPage() ?>
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
                'brandLabel' => 'Фарма',
                'brandUrl' => Yii::$app->homeUrl,
                'options' => [
                    'class' => 'navbar-inverse navbar-fixed-top',
                ],
            ]);
            NavBar::end();
        ?>

        <div class="container">
            <div class="row">
                <div class="col-md-6 col-md-offset-3 enter-email">
                    <h2 class="text-center"><?= Html::encode($this->title); ?></h2>

                    <?= Html::beginForm([''], 'post') ?>
                        <div class="form-group <?= $error ? 'has-error' : '';?>">
                            <label class="control-label" for="email">Введите почтовый ящик</label>
                                <div class="input-group"><span class="input-group-addon"><b>@</b></span><?= Html::input('email', 'email', '', ['class'=>'form-control', 'id'=>'email']); ?></div>
                            <div class="help-block"><?=$error ?></div>
                        </div>

                        <?= Html::submitButton('Отправить', ['class' => 'btn btn-primary']); ?>

                    <?php Html::endForm(); ?>
                </div>
            </div>
        </div>
    </div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>