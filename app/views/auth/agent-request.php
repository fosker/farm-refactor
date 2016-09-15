<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;

$this->title = 'Заявка представителя';
?>
<div class="agent-request">
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <h2 class="text-center"><?= Html::encode($this->title); ?></h2>

            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'name')->textInput(); ?>
            <?= $form->field($model, 'factory')->textInput(); ?>
            <?= $form->field($model, 'email')->textInput(); ?>
            <?= $form->field($model, 'phone')->textInput(); ?>

            <?= Html::submitButton('Отправить заявку', ['class' => 'btn btn-primary']); ?>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
