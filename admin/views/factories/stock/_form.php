<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\widgets\FileInput;
use backend\components\Editor;
use kartik\widgets\Select2;
use backend\components\CheckWidget;
use common\models\agency\Firm;
use common\models\location\Region;
use yii\bootstrap\Modal;

$this->registerJsFile('js/checkWidget.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>

<div class="stock-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?php
    $regions = Region::find()->asArray()->all();
    $firms = Firm::find()->asArray()->all();

    Modal::begin([
        'header' => '<h2>Выберите города</h2>',
        'toggleButton' => ['label' => 'Для городов', 'class' => 'btn btn-primary'],
    ]);

    echo $form->field($stock_cities, 'cities')->widget(CheckWidget::className(), [
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
    echo $form->field($stock_pharmacies, 'pharmacies')->widget(CheckWidget::className(), [
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

    echo $form->field($stock_education, 'education')->widget(CheckWidget::className(), [
        'parent_title' => 'education',
        'parent' => $education,
        'update' => $old_education,
        'height' => '10px'
    ]);
    Modal::end();
    ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->widget(Editor::className(), [
        'options' => ['rows' => 6],
        'preset' => 'click'
    ]); ?>

    <?= $form->field($model, 'factory_id')->widget(Select2::classname(), [
        'data' => $factories,
        'options' => ['placeholder' => 'Выберите фабрику ...'],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); ?>

    <?= $form->field($model, 'imageFile')->widget(FileInput::classname(),[
        'pluginOptions' => [
            'initialPreview'=> $model->image ? Html::img($model->imagePath, ['class'=>'file-preview-image', 'alt'=>'image', 'title'=>'Image']) : '',
            'showUpload' => false,
            'showRemove' => false,
        ]
    ]); ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
