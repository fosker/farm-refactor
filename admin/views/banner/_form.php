<?php

use kartik\file\FileInput;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use common\models\location\Region;
use common\models\agency\Firm;
use backend\components\CheckWidget;
use yii\bootstrap\Modal;
use kartik\widgets\ActiveForm;

$this->registerJsFile('js/checkWidget.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$url = Url::to(['/banner/link-list']);
?>

<div class="banner-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?php
        $regions = Region::find()->asArray()->all();
        $firms = Firm::find()->asArray()->all();

        Modal::begin([
            'header' => '<h2>Выберите города</h2>',
            'toggleButton' => ['label' => 'Для городов', 'class' => 'btn btn-primary'],
        ]);

        echo $form->field($banner_cities, 'cities')->widget(CheckWidget::className(), [
            'parent_title' => 'regions',
            'parent' => $regions,
            'update' => $old_cities,

            'child_title' => 'cities',
            'child' => $cities,
            'relation' => 'region_id'
        ]);
        Modal::end();


        Modal::begin([
            'header' => '<h2>Выберите аптеки</h2>',
            'toggleButton' => ['label' => 'Для аптек', 'class' => 'btn btn-primary'],
        ]);
            echo $form->field($banner_pharmacies, 'pharmacies')->widget(CheckWidget::className(), [
                'firms' => true,
                'color' => 'green',
                'parent_title' => 'firms',
                'parent' => $firms,
                'update' => $old_pharmacies,

                'child_title' => 'pharmacies',
                'child' => $pharmacies,
                'relation' => 'firm_id'

            ]);
        Modal::end();

        Modal::begin([
            'header' => '<h2>Выберите образования</h2>',
            'toggleButton' => ['label' => 'Для образований', 'class' => 'btn btn-primary'],
        ]);

        echo $form->field($banner_education, 'education')->widget(CheckWidget::className(), [
            'parent_title' => 'education',
            'parent' => $education,
            'update' => $old_education,
            'height' => '10px'
        ]);
        Modal::end();
    ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

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
