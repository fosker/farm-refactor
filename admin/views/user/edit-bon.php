<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Управление Bon';
?>

<div class="edit-bon-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'amount')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'message')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Применить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
