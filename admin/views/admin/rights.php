<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Редактирование прав: ' . ' ' . $model->name;

$buttons = [1 => 'да', 0 => 'нет'];

?>
<div class="admin-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="admin-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php  foreach ($rights as $index => $right) {
        echo $form->field($right, "[$index]value")->label($right->right->name)->radioList($buttons);
    } ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

</div>
