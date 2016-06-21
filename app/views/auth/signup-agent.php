<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use kartik\widgets\DepDrop;
use yii\helpers\Url;

$this->title = 'Регистрация представителя';
?>
<div class="site-signup">
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <h2 class="text-center"><?= Html::encode($this->title); ?></h2>

            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'name')->textInput(); ?>
            <?= $form->field($model, 'email')->textInput(); ?>
            <?= $form->field($model, 'phone')->textInput(); ?>
            <?= $form->field($model, 'region_agent')->textInput(); ?>
            <?= $form->field($model, 'firm_agent')->textInput(); ?>

            <?= Html::submitButton('Регистрация', ['class' => 'btn btn-primary']); ?>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
