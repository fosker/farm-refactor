<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;

/** @var $model \common\models\pharmbonus\Callback */

$this->title = 'Send a request for a demo account ';

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
    <div class="back"><?=Html::a(Html::img('/img/back.png'), '/')?></div>
    <div class="container">
        <div class="row">
            <div class="col-lg-5 col-md-6">
                <h2 class="text-center"><?= Html::encode($this->title); ?></h2>

                <?= Yii::$app->session->hasFlash('success') ?  '<div class="alert alert-info alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'. Yii::$app->session->getFlash('success') . '</div>' : '' ?>

                <div class="form">

                    <?php $form = ActiveForm::begin(); ?>

                    <?= $form->field($model, 'name')->label('Name')->textInput(); ?>
                    <?= $form->field($model, 'company')->label('Company')->textInput(); ?>
                    <?= $form->field($model, 'email')->textInput(); ?>
                    <?= $form->field($model, 'phone')->label('Contact Number')->textInput(); ?>

                    <div class="form-group">
                        <?= Html::submitButton('Send request', ['class' => 'btn btn-success btn-block']); ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
            <div class="col-lg-7 col-md-6 visible-md-block visible-lg-block">
                <div class="description">
                    <p>Still unsure whether to place informatio n about your company and its products in a mobile application <b>PharmBonus</b>?</p>
                    <p><b>We suggest you to install mobile app and see all the tools in action!</b></p>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="callback-copy">
    &copy; PharmBonus, 2016
</div>