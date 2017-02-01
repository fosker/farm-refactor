<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
use kartik\date\DatePicker;

?>

<div class="user-search">

    <?php $form = ActiveForm::begin([
        'action' => ['agents'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'user.login')->widget(Select2::classname(), [
        'data' => $logins,
        'options' => ['placeholder' => 'Выберите логин ...'],
        'pluginOptions' => [
            'allowClear' => true,
        ],
    ]); ?>

    <?= $form->field($model, 'user.name')->widget(Select2::classname(), [
        'data' => $names,
        'options' => ['placeholder' => 'Выберите имя ...'],
        'pluginOptions' => [
            'allowClear' => true,
        ],
    ]); ?>

    <?= $form->field($model, 'user.email')->widget(Select2::classname(), [
        'data' => $emails,
        'options' => ['placeholder' => 'Выберите email ...'],
        'pluginOptions' => [
            'allowClear' => true,
        ],
    ]); ?>

    <?= $form->field($model, 'user.comment')->textInput() ?>

    <?= $form->field($model, 'user.phone')->textInput() ?>

    <?= $form->field($model, 'factory_id')->widget(Select2::classname(), [
        'data' => $factories,
        'options' => ['placeholder' => 'Выберите компанию ...'],
        'pluginOptions' => [
            'allowClear' => true,
        ],
    ]); ?>

    <?= $form->field($model, 'date_birth_from')->widget(DatePicker::classname(), [
        'options' => ['placeholder' => 'Выберите дату рождения (от) ...'],
        'pluginOptions' => [
            'autoclose' => true,
            'format' => 'yyyy-mm-dd',
        ]
    ])->label('Дата рождения (от)'); ?>

    <?= $form->field($model, 'date_birth_to')->widget(DatePicker::classname(), [
        'options' => ['placeholder' => 'Выберите дату рождения (до) ...'],
        'pluginOptions' => [
            'autoclose' => true,
            'format' => 'yyyy-mm-dd',
        ]
    ])->label('Дата рождения (до)'); ?>

    <?= $form->field($model, 'date_reg_from')->widget(DatePicker::classname(), [
        'options' => ['placeholder' => 'Выберите дату регистрации (от) ...'],
        'pluginOptions' => [
            'autoclose' => true,
            'format' => 'yyyy-mm-dd',
        ]
    ])->label('Дата регистрации (от)'); ?>

    <?= $form->field($model, 'date_reg_to')->widget(DatePicker::classname(), [
        'options' => ['placeholder' => 'Выберите дату регистрации (до) ...'],
        'pluginOptions' => [
            'autoclose' => true,
            'format' => 'yyyy-mm-dd',
        ]
    ])->label('Дата регистрации (до)'); ?>

    <?= $form->field($model, 'points_from')->textInput()->label('Количество бонусов (от)')?>

    <?= $form->field($model, 'points_to')->textInput()->label('Количество бонусов (до)')?>

    <?= $form->field($model, 'user.status')->dropDownList([1 => 'активен', 0 => 'ожидает', 2 => 'не прошёл верификацию'],
        ['prompt' => 'Выберите статус']
    ); ?>

    <?= $form->field($model, 'user.inList')->dropDownList([0 => 'в нейтральном', 1 => 'в черном', 2 => 'в белом', 3 => 'в сером'],
        ['prompt' => 'Выберите список']); ?>


    <div class="form-group">
        <?= Html::submitButton('Поиск', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Сброс', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div> 