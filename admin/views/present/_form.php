<?php

use kartik\widgets\Select2;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\widgets\FileInput;
use backend\components\Editor;
use backend\components\CheckWidget;
use yii\bootstrap\Modal;
use common\models\location\Region;
use common\models\Company;

$this->registerJsFile('admin/js/checkWidget.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>

<div class="item-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data',]]); ?>

    <?php
        Modal::begin([
            'header' => '<h2>Выберите города</h2>',
            'toggleButton' => ['label' => 'Для городов', 'class' => 'btn btn-primary city'],
            'id' => 'cities'
        ]);

        echo $form->field(new Region(), '_')->widget(CheckWidget::className(), [
            'parent_title' => 'regions',
            'parent' => $regions,
            'parent_label' => 'name',
            'update' => $old_cities,

            'child_title' => 'cities',
            'child' => $cities,
            'relation' => 'region_id'
        ]);
        Modal::end();

        Modal::begin([
            'header' => '<h2>Выберите компании</h2>',
            'toggleButton' => ['label' => 'Для компаний', 'class' => 'btn btn-primary company'],
            'id' => 'companies'
        ]);

        echo $form->field(new Company(), '_')->widget(CheckWidget::className(), [
            'parent_title' => 'companies',
            'parent' => $companies,
            'parent_label' => 'title',
            'update' => $old_companies,

            'height' => '1px',
        ]);
        Modal::end();


        Modal::begin([
            'header' => '<h2>Выберите аптеки</h2>',
            'toggleButton' => ['label' => 'Для аптек', 'class' => 'btn btn-primary pharmacy'],
            'id' => 'pharmacies'
        ]);
        Modal::end();
    ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'vendor_id')->widget(Select2::classname(), [
        'data' => $vendors,
        'options' => ['placeholder' => 'Выберите поставшика ...'],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); ?>


    <?= $form->field($model, 'description')->widget(Editor::className(), [
        'options' => ['rows' => 6],
        'preset' => 'click'
    ]); ?>

    <?= $form->field($model, 'imageFile')->widget(FileInput::classname(),[
        'pluginOptions' => [
            'initialPreview'=> $model->image ? Html::img($model->imagePath, ['class'=>'file-preview-image', 'alt'=>'image', 'title'=>'Image']) : '',
            'showUpload' => false,
            'showRemove' => false,
        ]
    ]); ?>

    <?= $form->field($model, 'thumbFile')->widget(FileInput::classname(),[
        'pluginOptions' => [
            'initialPreview'=> $model->thumbnail ? Html::img($model->thumbPath, ['class'=>'file-preview-image', 'alt'=>'thumb', 'title'=>'thumb']) : '',
            'showUpload' => false,
            'showRemove' => false,
        ]
    ]); ?>

    <?= $form->field($model, 'points')->textInput() ?>

    <?= $form->field($model, 'priority')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
