<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use kartik\widgets\DepDrop;
use yii\helpers\Url;

$this->title = 'Редактирование профиля';
?>
<div class="profile-update">
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <h2 class="text-center"><?= Html::encode($this->title); ?></h2>

            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'email')->textInput(); ?>
            <?= $form->field($model, 'name')->textInput(); ?>
            <?= $form->field($model, 'sex')->dropDownList([
                'male' => 'мужчина',
                'female' => 'женщина',
            ]); ?>
            <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'mail_address')->textInput(['maxlength' => true]) ?>

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
                    'url'=>Url::to(['/list/city-list'])
                ]
            ]); ?>

            <?= $form->field($model, 'firm_id')->widget(DepDrop::classname(), [
                'type' => 2,
                'options'=>['id'=>'firm-id'],
                'pluginOptions'=>[
                    'depends'=>['city-id'],
                    'placeholder'=>'Выберите фирму...',
                    'url'=>Url::to(['/list/firm-list'])
                ]
            ]); ?>

            <?= $form->field($model, 'pharmacy_id')->widget(DepDrop::classname(), [
                'data' => $pharmacies,
                'type' => 2,
                'options'=>['id'=>'pharmacy-id'],
                'pluginOptions'=>[
                    'depends'=>['firm-id', 'city-id'],
                    'placeholder'=>'Выберите аптеку...',
                    'url'=>Url::to(['/list/pharmacy-list'])
                ]
            ]); ?>

            <?= $form->field($model, 'education_id')->widget(Select2::classname(), [
                'data' => $education,
                'options' => ['placeholder' => 'Выберите образование ...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>

            <?= $form->field($model, 'position_id')->widget(Select2::classname(), [
                'data' => $positions,
                'options' => ['placeholder' => 'Выберите должность ...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>

            <?= $form->field($model, 'details')->textArea(['rows' => '6']) ?>

            <?= Html::submitButton('Изменить', ['class' => 'btn btn-primary']); ?>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
