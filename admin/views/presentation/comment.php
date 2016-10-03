<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<div class="comment-present">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'admin_comment')->textInput()?>

    <div class="form-group">
        <?= Html::submitButton('Комментировать', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>