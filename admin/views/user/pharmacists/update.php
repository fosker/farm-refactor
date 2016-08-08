<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
use kartik\widgets\DepDrop;
use yii\helpers\Url;

$this->title = 'Редактирование данных: ' . ' ' . $model->name;
?>

<div class="pharmacist-form">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin([]); ?>

    <?= $form->field($model, 'name')->textInput([
        'maxlength' => true,
        ]) ?>

    <?php if ($update->name != $model->name && $update->name) {
        echo "<div class='row'>
                <div class='col-md-4'>
                    <p class='text-success'>Новое имя: $update->name</p>
                </div>
              </div>";
        } ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?php if ($update->email != $model->email && $update->email) {
        echo "<div class='row'>
                <div class='col-md-4'>
                    <p class='text-success'>Новый email: $update->email</p>
                </div>
              </div>";
    } ?>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

    <?php if ($update->phone != $model->phone && $update->phone) {
        echo "<div class='row'>
                <div class='col-md-4'>
                    <p class='text-success'>Новый телефон: $update->phone</p>
                </div>
              </div>";
    } ?>

    <?= $form->field($type, 'mail_address')->textInput(['maxlength' => true]) ?>

    <?php if ($update->mail_address != $type->mail_address && $update->mail_address) {
        echo "<div class='row'>
                <div class='col-md-4'>
                    <p class='text-success'>Новый почтовый адрес: $update->mail_address</p>
                </div>
              </div>";
    } ?>

    <?= $form->field($type, 'education_id')->widget(Select2::classname(), [
        'data' => $education,
        'options' => ['placeholder' => 'Выберите образование ...'],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); ?>

    <?php if ($update->education_id != $type->education_id && $update->education_id) {
        echo "<div class='row'>
                <div class='col-md-4'>
                    <p class='text-success'>Новое образование: ".$update->education->name."</p>
                </div>
              </div>";
    } ?>

    <?= $form->field($type, 'region_id')->widget(Select2::classname(), [
        'data' => $regions,
        'options' => ['placeholder' => 'Выберите регион ...', 'id' => 'region-id'],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); ?>

    <?= $form->field($type, 'city_id')->widget(DepDrop::classname(), [
        'type' => 2,
        'options'=>['id'=>'city-id'],
        'pluginOptions'=>[
            'depends'=>['region-id'],
            'placeholder'=>'Выберите город...',
            'url'=>Url::to(['/user/city-list'])
        ]
    ]); ?>

    <?= $form->field($type, 'company_id')->widget(Select2::classname(), [
        'data' => $companies,
        'options' => ['placeholder' => 'Выберите компанию ...', 'id' => 'company-id'],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); ?>


    <?= $form->field($type, 'pharmacy_id')->widget(DepDrop::classname(), [
        'data' => $pharmacies,
        'type' => 2,
        'options'=>['id'=>'pharmacy-id'],
        'pluginOptions'=>[
            'depends'=>['company-id', 'city-id'],
            'placeholder'=>'Выберите аптеку...',
            'url'=>Url::to(['/user/pharmacy-list'])
        ]
    ]); ?>

    <?php if ($update->pharmacy_id != $type->pharmacy_id && $update->pharmacy_id) {
        echo "<div class='row'>
                <div class='col-md-12'>
                    <p class='text-success'>Новая аптека: ".
            $update->region->name.' / '.$update->pharmacy->city->name.' / '.
            $update->pharmacy->company->title.' / '.$update->pharmacy->name.' ('.$update->pharmacy->address.')'."</p>
                </div>
              </div>";
    } ?>

    <?= $form->field($type, 'sex')->dropDownList([
        'male' => 'мужской',
        'female' => 'женский',
    ]); ?>

    <?php if ($update->sex != $type->sex && $update->sex) {
        $sex = "";
        switch($update->sex) {
            case 'female' : $sex = 'женский';
                break;
            case 'male' : $sex = 'мужской';
                break;
        }
        echo "<div class='row'>
                <div class='col-md-4'>
                    <p class='text-success'>Новый пол: ". $sex."</p>
                </div>
              </div>";
    } ?>

    <?= $form->field($type, 'position_id')->widget(Select2::classname(), [
        'data' => $positions,
        'options' => ['placeholder' => 'Выберите должность ...'],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); ?>

    <?php if ($update->position_id != $type->position_id && $update->position_id) {
        echo "<div class='row'>
                <div class='col-md-4'>
                    <p class='text-success'>Новая должность: ". $update->position->name."</p>
                </div>
              </div>";
    } ?>

    <?php if ($update->details) {
        echo "<div class='row'>
                <div class='col-md-12'>
                    <p class='text-info'>Детали: ". $update->details."</p>
                </div>
              </div>";
    } ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>