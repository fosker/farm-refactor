<?php
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\widgets\FileInput;
?>
<div class="profile-update-avatar">
    <div class="row">
        <div class="col-md-6 col-md-offset-3 reset-avatar">
            <h2 class="text-center"><?= Html::encode($this->title); ?></h2>

            <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
            <?= $form->field($model, 'image')->widget(FileInput::classname(),[
                'pluginOptions' => [
                    'initialPreview'=> $model->image ? Html::img($model->AvatarPath, ['class'=>'file-preview-image', 'alt'=>'image', 'title'=>'Image']) : '',
                    'showUpload' => false,
                    'showRemove' => false,
                ]
            ]); ?>
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']); ?>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>