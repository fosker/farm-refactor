<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;

$this->title = 'Вход';
?>
<div class="site-login">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <h2 class="text-center"><?= Html::encode($this->title); ?></h2>

                <?php $form = ActiveForm::begin(); ?>

                <?= $form->field($model, 'login',['addon'=>['prepend'=>['content'=>'<i class="glyphicon glyphicon-user"></i>']]]); ?>
                <?= $form->field($model, '_password',['addon'=>['prepend'=>['content'=>'<i class="glyphicon glyphicon-lock"></i>']]])->passwordInput(); ?>
                <?= $form->field($model, 'rememberMe')->checkbox(); ?>

                <?= Html::submitButton('Войти', ['class' => 'btn btn-primary']); ?>
                <?= Html::a('Забыли пароль?',['/auth/reset-password'], ['class' => 'btn btn-warning']); ?>

                <?php ActiveForm::end(); ?>
                <div>
                </div>
            </div>
        </div>
</div>
