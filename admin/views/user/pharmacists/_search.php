<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;

?>

<div class="user-search">

    <?php $form = ActiveForm::begin([
        'action' => ['pharmacists'],
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

    <?= $form->field($model, 'position_id')->widget(Select2::classname(), [
        'data' => $positions,
        'options' => ['placeholder' => 'Выберите должность ...'],
        'pluginOptions' => [
            'allowClear' => true,
        ],
    ]); ?>

    <?= $form->field($model, 'pharmacy_id')->widget(Select2::classname(), [
        'data' => $pharmacies,
        'options' => ['placeholder' => 'Выберите аптеку ...'],
        'pluginOptions' => [
            'allowClear' => true,
        ],
    ]); ?>

    <?= $form->field($model, 'pharmacy.company.id')->widget(Select2::classname(), [
        'data' => $companies,
        'options' => ['placeholder' => 'Выберите компанию ...'],
        'pluginOptions' => [
            'allowClear' => true,
        ],
    ])->label('Компания'); ?>

    <?= $form->field($model, 'pharmacy.city.id')->widget(Select2::classname(), [
        'data' => $cities,
        'options' => ['placeholder' => 'Выберите город ...'],
        'pluginOptions' => [
            'allowClear' => true,
        ],
    ])->label('Город'); ?>

    <?= $form->field($model, 'points_from')->textInput()->label('Количество бонусов (от)')?>

    <?= $form->field($model, 'points_to')->textInput()->label('Количество бонусов (до)')?>

    <?= $form->field($model, 'user.status')->dropDownList([1 => 'активен', 0 => 'ожидает', 2 => 'не прошёл верификацию'],
        ['prompt' => 'Выберите статус']
    ); ?>

    <?= $form->field($model, 'user.inList')->dropDownList([0 => 'нет', 1 => 'в сером', 2 => 'в белом'],
        ['prompt' => 'Выберите список']); ?>

    <div class="form-group">
        <?= Html::submitButton('Поиск', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Сброс', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div> 