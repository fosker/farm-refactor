<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

?>

<div class="rate-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'employee_id')->widget(Select2::classname(), [
        'data' => $employees,
        'value' => $model->employee_id,
        'disabled' => true
    ]); ?>

    <?= $form->field($model, 'criterion_id')->widget(Select2::classname(), [
        'data' => $criteria,
        'value' => $model->criterion_id,
        'disabled' => true
    ]); ?>

    <?php
        $values = range($model->criterion->min, $model->criterion->max, $model->criterion->step);
        $buttons = array_combine(array_values($values), array_values($values));
    ?>
    <?= $form->field($model, 'rate')->label($model->criterion->customName)->dropDownList($buttons) ?>

    <?= $form->field($model, 'comment')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton('Изменить', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
