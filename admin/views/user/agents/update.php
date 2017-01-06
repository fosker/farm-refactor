<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
use common\models\Factory;

$this->title = 'Редактирование данных: ' . ' ' . $model->name;
?>

<div class="agent-form">

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

    <?= $form->field($type, 'factory_id')->widget(Select2::classname(), [
        'data' => $factories,
        'options' => ['placeholder' => 'Выберите компанию ...'],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); ?>

    <?php if (!Factory::find()->where(['id' => $type->factory_id])->exists()) {
        echo "<div class='row'>
                <div class='col-md-4'>
                    <p class='text-success'>Новая компания: ".$type->factory_id."</p>
                </div>
              </div>";
    } ?>


    <?php if ($update->factory_id != $type->factory_id && $update->factory_id) {
        echo "<div class='row'>
                <div class='col-md-4'>
                    <p class='text-success'>Новая компания: ".$update->factory->title."</p>
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