<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;

use kartik\widgets\DatePicker;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pharm-company-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'admin_id')->widget(Select2::classname(), [
        'data' => $admins,
        'options' => ['placeholder' => 'Выберите администратора ...'],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]) ?>

    <?= $form->field($model, 'type')->dropDownList(['дистрибьютор' => 'дистрибьютор', 'производитель' => 'производитель'], ['prompt' => 'Выберите тип']) ?>

    <?= $form->field($model, 'location')->dropDownList(['зарубежный' => 'зарубежный', 'отечественный' => 'отечественный'], ['prompt' => 'Выберите размещение']) ?>

    <?= $form->field($model, 'size')->dropDownList(['крупная международная корпорация' => 'крупная международная корпорация', 'некрупная международнавя корпорация' => 'некрупная международнавя корпорация'], ['prompt' => 'Выберите размер']) ?>

    <?= $form->field($model, 'rx_otc')->dropDownList(['RX' => 'RX', 'OTC' => 'OTC', 'RX/OTC' => 'RX/OTC'], ['prompt' => 'Выберите RX/OTC']) ?>

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

    <div class="form-group">
        <?= Html::submitButton('Поиск', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Сброс', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div> 