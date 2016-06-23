<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\presentation\Question */
?>

<div class="presentation-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'id'=>'presentation-form']]); ?>

    <?= $form->field($model, 'question')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'right_answers')->textInput(['maxlength' => true]) ?>

    <?php if(!$model->options) {
        echo $form->field($model, 'validAnswer')->textInput(['maxlength' => true]);
    };
    ?>

    <?= $form->field($model, 'order_index') ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
