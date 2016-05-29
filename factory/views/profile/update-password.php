<?php
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
$this->title = 'Изменить пароль';
?>
<div class="profile-update-password">
    <div class="row">
        <div class="col-md-6 col-md-offset-3 reset-password">
            <h2 class="text-center"><?= Html::encode($this->title); ?></h2>

            <?php $form = ActiveForm::begin(); ?>
            <?= $form->field($model, 'old_password')->passwordInput(); ?>
            <?= $form->field($model, 'password')->passwordInput(); ?>
            <?= $form->field($model, 're_password')->passwordInput(); ?>

            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']); ?>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>