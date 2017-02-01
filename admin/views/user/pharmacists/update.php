<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
use kartik\widgets\DepDrop;
use yii\helpers\Url;

use kartik\widgets\FileInput;
use kartik\date\DatePicker;

$this->title = 'Редактирование данных: ' . ' ' . $model->name;
?>

<div class="pharmacist-form">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($model, 'image')->widget(FileInput::classname(),[
        'pluginOptions' => [
            'initialPreview'=> $model->avatar ? Html::img($model->avatarPath, ['class'=>'file-preview-image', 'alt'=>'image', 'title'=>'Image']) : '',
            'showUpload' => false,
            'showRemove' => false,
        ]
    ]);
    ?>

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

    <?= $form->field($type, 'date_birth')->widget(DatePicker::classname(), [
        'options' => ['placeholder' => 'Выберите дату рождения ...'],
        'pluginOptions' => [
            'autoclose' => true,
            'format' => 'yyyy-mm-dd',
        ]
    ]) ?>

    <?= $form->field($model, 'comment')->textInput() ?>

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

    <label class="control-label">Регион</label>
    <?= Select2::widget([
        'name' => 'Type[region_id]',
        'value' => $type->city->region_id,
        'data' => $regions,
        'options' => [
            'id' => 'region-id',
            'placeholder' => 'Выберите регион ...',
        ],
    ]); ?>

    <label class="control-label">Город</label>
    <?= Select2::widget([
        'name' => 'Type[city_id]',
        'value' => $type->city->id,
        'data' => $cities,
        'options' => [
            'id' => 'city-id',
            'placeholder' => 'Выберите город ...',
        ],
    ]); ?>

    <label class="control-label">Организация</label>
    <?= Select2::widget([
        'name' => 'Type[company_id]',
        'value' => $type->company->id,
        'data' => $companies,
        'options' => [
            'id' => 'company-id',
            'placeholder' => 'Выберите организацию ...',
        ],
    ]); ?>

    <?= $form->field($type, 'pharmacy_id')->widget(DepDrop::classname(), [
        'data' => $pharmacies,
        'type' => DepDrop::TYPE_SELECT2,
        'options' => ['id' => 'pharmacy-id'],
        'pluginOptions' => [
            'depends' => ['company-id', 'city-id'],
            'placeholder' => 'Выберите аптеку...',
            'url' => Url::to(['pharmacy-list'])
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