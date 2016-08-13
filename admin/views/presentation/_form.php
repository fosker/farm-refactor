<?php

use kartik\widgets\Select2;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\widgets\FileInput;
use backend\components\Editor;
use backend\components\CheckWidget;
use common\models\location\Region;
use common\models\profile\Education;
use common\models\profile\Type;
use common\models\Company;
use yii\bootstrap\Modal;
$this->registerJsFile('js/checkWidget.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>

<div class="presentation-form">

    <input type="checkbox" class="btn btn-info all-groups">Выбрать все</input>
    </br>
    </br>

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'id'=>'presentation-form']]); ?>

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

    Modal::begin([
        'header' => '<h2>Выберите типы пользователей</h2>',
        'toggleButton' => ['label' => 'Для типов пользователей', 'class' => 'btn btn-primary type'],
        'id' => 'types'
    ]);

    echo $form->field(new Type, '_')->widget(CheckWidget::className(), [
        'parent_title' => 'types',
        'parent' => $types,
        'parent_label' => 'name',

        'update' => $old_types,
        'height' => '10px'
    ]);
    Modal::end();


    Modal::begin([
        'header' => '<h2>Выберите образования</h2>',
        'toggleButton' => ['label' => 'Для образований', 'class' => 'btn btn-primary education'],
        'id' => 'education'
    ]);

    echo $form->field(new Education, '_')->widget(CheckWidget::className(), [
        'parent_title' => 'education',
        'parent' => $education,
        'parent_label' => 'name',

        'update' => $old_education,
        'height' => '10px'
    ]);
    Modal::end();
    ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'factory_id')->widget(Select2::classname(), [
        'data' => $factories,
        'options' => ['placeholder' => 'Выберите фабрику ...'],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); ?>

    <?= $form->field($model, 'points')->textInput() ?>

    <?= $form->field($model, 'description')->widget(Editor::className(), [
        'options' => ['rows' => 6],
        'preset' => 'click'
    ]); ?>

    <?= $form->field($model, 'home_priority')->textInput() ?>

    <?= $form->field($model, 'views_limit')->textInput() ?>

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

    <?= $form->field($model, 'grayList')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
