<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
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
                <div class="col-md-6 col-md-offset-3 reset-password">
                    <h2 class="text-center"><?= Html::encode($this->title); ?></h2>

                    <?php $form = ActiveForm::begin(); ?>
                        <?= $form->field($model, 'password')->passwordInput(); ?>
                        <?= $form->field($model, 're_password')->passwordInput(); ?>

                        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']); ?>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>