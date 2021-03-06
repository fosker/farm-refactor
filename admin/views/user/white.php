<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;

$this->title = 'Добавить в белый список';
?>

<h1><?= Html::encode($this->title) ?></h1>

<div class="white-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id')->widget(Select2::classname(), [
        'data' => $users,
        'disabled' => true,
        'options' => ['placeholder' => 'Пользователь ...'],
        'pluginOptions' => [
            'allowClear' => true,
        ],
    ])->label('Пользователь'); ?>

    <?= $form->field($model, 'comment')->textarea(); ?>

    <div class="form-group">
        <?= Html::submitButton('Добавить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
