<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
use kartik\date\DatePicker;
use common\models\profile\Device;
?>

<div class="user-search">

    <?php $form = ActiveForm::begin([
        'action' => ['pharmacists'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id')->textInput() ?>

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

    <?= $form->field($model, 'sex')->dropDownList(['female' => 'женщина', 'male' => 'мужчина'], ['prompt' => 'Выберите пол']); ?>

    <?= $form->field($model, 'user.comment')->textInput() ?>

    <?= $form->field($model, 'user.phone')->textInput() ?>

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
    ])->label('Организация'); ?>

    <?= $form->field($model, 'pharmacy.city.id')->widget(Select2::classname(), [
        'data' => $cities,
        'options' => ['placeholder' => 'Выберите город ...'],
        'pluginOptions' => [
            'allowClear' => true,
        ],
    ])->label('Город'); ?>

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

    <hr>
    <?= $form->field($model, 'device.type')->dropDownList([Device::TYPE_ANDROID => 'Android', Device::TYPE_IOS => 'Ios'], ['prompt' => 'Выберите тип устройства'])->label('Тип устройства') ?>

    <?= $form->field($model, 'device.version_from')->textInput()->label('Версия (от)') ?>

    <?= $form->field($model, 'device.version_to')->textInput()->label('Версия (до)') ?>

    <div class="form-group">
        <?= Html::submitButton('Поиск', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Сброс', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div> 