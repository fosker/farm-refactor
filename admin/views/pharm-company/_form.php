<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

use kartik\widgets\DatePicker;

/* @var $this yii\web\View */
/* @var $model common\models\PharmCompany */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pharm-company-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'type')->dropDownList(['дистрибьютор' => 'дистрибьютор', 'производитель' => 'производитель']) ?>

    <?= $form->field($model, 'location')->dropDownList(['зарубежный' => 'зарубежный', 'отечественный' => 'отечественный']) ?>

    <?= $form->field($model, 'size')->dropDownList(['крупная международная корпорация' => 'крупная международная корпорация', 'некрупная международнавя корпорация' => 'некрупная международнавя корпорация']) ?>

    <?= $form->field($model, 'rx_otc')->dropDownList(['RX' => 'RX', 'OTC' => 'OTC', 'RX/OTC' => 'RX/OTC']) ?>

    <?= $form->field($model, 'first_visit')->widget(DatePicker::classname(), [
        'options' => ['placeholder' => 'Выберите дату первого визита ...'],
        'pluginOptions' => [
            'autoclose'=>true,
            'format' => 'yyyy-mm-dd',
        ]
    ]) ?>

    <?= $form->field($model, 'planned_visit')->widget(DatePicker::classname(), [
        'options' => ['placeholder' => 'Выберите дату запланированного визита ...'],
        'pluginOptions' => [
            'autoclose'=>true,
            'format' => 'yyyy-mm-dd',
        ]
    ]) ?>

    <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'contact_person')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Изменить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div> 