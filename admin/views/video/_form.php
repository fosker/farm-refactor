<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\widgets\FileInput;
use backend\components\Editor;
?>
<div class="video-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->widget(Editor::className(), [
        'options' => ['rows' => 6],
        'preset' => 'click'
    ]); ?>

    <?= $form->field($model, 'tags')->textInput(['maxlength' => true]); ?>

    <?= $form->field($model, 'link')->textInput(['maxlength' => true]); ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
