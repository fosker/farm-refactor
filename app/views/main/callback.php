<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;

/** @var $model \common\models\pharmbonus\Callback */

$this->title = 'Отправить заявку на получение демо-аккаунта';

?>

<style>
    html, body {
        height: 100%;
    }

    .wrap {
        height: 100%;
    }
</style>

<div class="callback-bg"></div>

<div class="callback">
    <div class="back"><?=Html::a(Html::img('/app/img/back.png'), '/')?></div>
    <div class="container">
        <div class="row">
            <div class="col-lg-5 col-md-6">
                <h2 class="text-center"><?= Html::encode($this->title); ?></h2>

                <div class="form">

                    <?php $form = ActiveForm::begin(); ?>

                    <?= $form->field($model, 'name')->textInput(); ?>
                    <?= $form->field($model, 'company')->textInput(); ?>
                    <?= $form->field($model, 'email')->textInput(); ?>
                    <?= $form->field($model, 'phone')->textInput(); ?>

                    <div class="form-group">
                        <?= Html::submitButton('Отправить заявку', ['class' => 'btn btn-success btn-block']); ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
            <div class="col-lg-7 col-md-6 visible-md-block visible-lg-block">
                <div class="description">
                    <p>Все еще сомневаетесь, стоит ли размещать информацию о себе и своей продукции в мобильном приложении <b>Фармбонус</b>?</p>
                    <p><b>Предлагаем Вам установить мобильное приложение и увидеть все инструменты в действии!</b></p>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="callback-copy">
    &copy; PharmBonus, 2016
</div>