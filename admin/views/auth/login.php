<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\widgets\Growl;
use yii\bootstrap\NavBar;
use backend\assets\AppAsset;

$this->title .= 'Вход';

if(Yii::$app->session->hasFlash('LoginAdminMessage')) :
    echo Growl::widget([
        'type' => Growl::TYPE_SUCCESS,
        'title' => 'Успешно',
        'icon' => 'glyphicon glyphicon-ok-sign',
        'body' => Yii::$app->session->getFlash('LoginAdminMessage'),
        'showSeparator' => true,
        'delay' => 0,
        'pluginOptions' => [
            'placement' => [
                'from' => 'top',
                'align' => 'right',
            ]
        ]
    ]);
endif;

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
                <div class="col-md-6 col-md-offset-3">
                    <h2 class="text-center"><?= Html::encode($this->title); ?></h2>

                    <?php $form = ActiveForm::begin(); ?>

                        <?= $form->field($model, 'email',['addon'=>['prepend'=>['content'=>'<b>@</b>']]]); ?>
                        <?= $form->field($model, 'password',['addon'=>['prepend'=>['content'=>'<i class="glyphicon glyphicon-lock"></i>']]])->passwordInput(); ?>
                        <?= $form->field($model, 'rememberMe')->checkbox(); ?>

                        <?= Html::submitButton('Войти', ['class' => 'btn btn-primary']); ?>
                    <?= Html::a('Забыли пароль?',['/auth/reset-password'], ['class' => 'btn btn-warning']); ?>

                    <?php ActiveForm::end(); ?>
                    <div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>