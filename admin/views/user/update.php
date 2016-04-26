<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
use kartik\widgets\DepDrop;
use yii\helpers\Url;

$this->title = 'Редактирование данных: ' . ' ' . $model->name;
?>

<div class="user-form">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin([]); ?>

    <?= $form->field($model, 'name')->textInput([
        'maxlength' => true,
        ]) ?>

    <?php if ($user->name != $model->name && $user->name) {
        echo "<div class='row'>
                <div class='col-md-4'>
                    <p class='text-success'>Новое имя: $user->name</p>
                </div>
              </div>";
        } ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?php if ($user->email != $model->email && $user->email) {
        echo "<div class='row'>
                <div class='col-md-4'>
                    <p class='text-success'>Новый email: $user->email</p>
                </div>
              </div>";
    } ?>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

    <?php if ($user->phone != $model->phone && $user->phone) {
        echo "<div class='row'>
                <div class='col-md-4'>
                    <p class='text-success'>Новый телефон: $user->phone</p>
                </div>
              </div>";
    } ?>

    <?= $form->field($model, 'mail_address')->textInput(['maxlength' => true]) ?>

    <?php if ($user->mail_address != $model->mail_address && $user->mail_address) {
        echo "<div class='row'>
                <div class='col-md-4'>
                    <p class='text-success'>Новый почтовый адрес: $user->mail_address</p>
                </div>
              </div>";
    } ?>

    <?= $form->field($model, 'education_id')->widget(Select2::classname(), [
        'data' => $education,
        'options' => ['placeholder' => 'Выберите образование ...'],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); ?>

    <?php if ($user->education_id != $model->education_id && $user->education_id) {
        echo "<div class='row'>
                <div class='col-md-4'>
                    <p class='text-success'>Новое образование: ".$user->education->name."</p>
                </div>
              </div>";
    } ?>

    <?= $form->field($model, 'region_id')->widget(Select2::classname(), [
        'data' => $regions,
        'options' => ['placeholder' => 'Выберите регион ...', 'id' => 'region-id'],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); ?>

    <?= $form->field($model, 'city_id')->widget(DepDrop::classname(), [
        'type' => 2,
        'options'=>['id'=>'city-id'],
        'pluginOptions'=>[
            'depends'=>['region-id'],
            'placeholder'=>'Выберите город...',
            'url'=>Url::to(['/user/city-list'])
        ]
    ]); ?>

    <?= $form->field($model, 'firm_id')->widget(DepDrop::classname(), [
        'type' => 2,
        'options'=>['id'=>'firm-id'],
        'pluginOptions'=>[
            'depends'=>['city-id'],
            'placeholder'=>'Выберите фирму...',
            'url'=>Url::to(['/user/firm-list'])
        ]
    ]); ?>

    <?= $form->field($model, 'pharmacy_id')->widget(DepDrop::classname(), [
        'data' => $pharmacies,
        'type' => 2,
        'options'=>['id'=>'pharmacy-id'],
        'pluginOptions'=>[
            'depends'=>['firm-id', 'city-id'],
            'placeholder'=>'Выберите аптеку...',
            'url'=>Url::to(['/user/pharmacy-list'])
        ]
    ]); ?>

    <?php if ($user->pharmacy_id != $model->pharmacy_id && $user->pharmacy_id) {
        echo "<div class='row'>
                <div class='col-md-12'>
                    <p class='text-success'>Новая аптека: ".
            $user->pharmacy->city->region->name.' / '.$user->pharmacy->city->name.' / '.
            $user->pharmacy->firm->name.' / '.$user->pharmacy->name.' ('.$user->pharmacy->address.')'."</p>
                </div>
              </div>";
    } ?>

    <?= $form->field($model, 'sex')->dropDownList([
        'male' => 'мужской',
        'female' => 'женский',
    ]); ?>

    <?php if ($user->sex != $model->sex && $user->sex) {
        $sex = "";
        switch($user->sex) {
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

    <?= $form->field($model, 'position_id')->widget(Select2::classname(), [
        'data' => $positions,
        'options' => ['placeholder' => 'Выберите должность ...'],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); ?>

    <?php if ($user->position_id != $model->position_id && $user->position_id) {
        echo "<div class='row'>
                <div class='col-md-4'>
                    <p class='text-success'>Новая должность: ". $user->position->name."</p>
                </div>
              </div>";
    } ?>

    <?php if ($user->details) {
        echo "<div class='row'>
                <div class='col-md-12'>
                    <p class='text-info'>Детали: ". $user->details."</p>
                </div>
              </div>";
    } ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>