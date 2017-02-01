<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\widgets\FileInput;
use backend\components\Editor;

?>

<div class="factory-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'priority')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->widget(Editor::className(), [
        'options' => ['rows' => 6],
        'preset' => 'basic'
    ]); ?>

    <?= $form->field($model, 'is_shown')->dropDownList([1 => 'да', 0 => 'нет']) ?>

    <?= $form->field($model, 'imageFile')->widget(FileInput::classname(),[
        'pluginOptions' => [
            'initialPreview'=> $model->image ? Html::img($model->imagePath, ['class'=>'file-preview-image', 'alt'=>'image', 'title'=>'Image']) : '',
            'showUpload' => false,
            'showRemove' => false,
        ]
    ]); ?>

    <?= $form->field($model, 'logoFile')->widget(FileInput::classname(),[
        'pluginOptions' => [
            'initialPreview'=> $model->logo ? Html::img($model->logoPath, ['class'=>'file-preview-image', 'alt'=>'image', 'title'=>'Logo']) : '',
            'showUpload' => false,
            'showRemove' => false,
        ]
    ]); ?>



    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
