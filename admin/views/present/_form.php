<?php

use kartik\widgets\Select2;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\widgets\FileInput;
use backend\components\Editor;
use backend\components\CheckWidget;
use yii\bootstrap\Modal;
use common\models\agency\Firm;
use common\models\location\Region;
$this->registerJsFile('backend/web/js/checkWidget.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>

<div class="item-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data',]]); ?>

    <?php
    $regions = Region::find()->asArray()->all();
    $firms = Firm::find()->asArray()->all();

    Modal::begin([
        'header' => '<h2>Выберите города</h2>',
        'toggleButton' => ['label' => 'Для городов', 'class' => 'btn btn-primary'],
    ]);

    echo $form->field($item_cities, 'cities')->widget(CheckWidget::className(), [
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
    echo $form->field($item_pharmacies, 'pharmacies')->widget(CheckWidget::className(), [
        'parent_title' => 'firms',
        'parent' => $firms,
        'update' => $old_pharmacies,

        'child_title' => 'pharmacies',
        'child' => $pharmacies,
        'relation' => 'firm_id'

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
