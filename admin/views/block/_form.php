<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\widgets\FileInput;
use backend\components\Editor;
use yii\helpers\ArrayHelper;
use dosamigos\ckeditor\CKEditor;


$this->registerJs("CKEDITOR.plugins.addExternal('dropler','".Yii::getAlias('@web')."/js/ckeditor/plugins/dropler/');");
?>

<div class="block-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->widget(Editor::className(), [
        'options' => [
            'rows' => 6,
        ],
        'clientOptions' => [
            'extraPlugins' => 'dropler',
            'droplerConfig' => [
                'backend' => 'basic',
                'settings' => [
                    'uploadUrl' => 'http://farm.loc/backend/components/upload.php'
                ]
            ]
        ],
        'preset' => 'basic',
    ]); ?>

    <?= $form->field($model, 'imageFile')->widget(FileInput::classname(),[
        'pluginOptions' => [
            'initialPreview'=> $model->image ? Html::img($model->imagePath, ['class'=>'file-preview-image', 'alt'=>'image', 'title'=>'Image']) : '',
            'showUpload' => false,
            'showRemove' => false,
        ]
    ]); ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

