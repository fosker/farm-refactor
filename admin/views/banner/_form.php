<?php

use kartik\file\FileInput;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use common\models\location\Region;
use common\models\Company;
use common\models\profile\Type;
use common\models\profile\Education;
use backend\components\CheckWidget;
use yii\bootstrap\Modal;
use kartik\widgets\ActiveForm;

$this->registerJsFile('admin/js/checkWidget.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$url = Url::to(['/banner/link-list']);
?>

<div class="banner-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

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

    <?= $form->field($model, 'position')->dropDownList($model::positions(), ['prompt'=>'']) ?>

    <?= $form->field($model, 'link')->widget(Select2::classname(),
        [
            'initValueText' => $model->linkTitle,
            'pluginOptions' => [
                'allowClear' => false,
                'minimumInputLength' => 0,
                'ajax' => [
                    'url' => $url,
                    'dataType' => 'json',
                    'data' => new JsExpression('function(params) { return {q:params.term}; }'),
                ],
                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                'templateResult' => new JsExpression('function(link) { return link.text; }'),
                'templateSelection' => new JsExpression('function (link) { return link.text; }'),
            ],
        ]
    ); ?>

    <?= $form->field($model, 'imageFile')->widget(FileInput::classname(),[
        'pluginOptions' => [
            'initialPreview'=> $model->image ? Html::img($model->imagePath, ['class'=>'file-preview-image', 'alt'=>'image', 'title'=>'Image']) : '',
            'showUpload' => false,
            'showRemove' => false,
        ]
    ]);
    ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
