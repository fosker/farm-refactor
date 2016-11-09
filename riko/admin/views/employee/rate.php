<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use admin\models\Criterion;

$this->title = 'Оценка сотрудника: ' . ' ' . $model->name;
?>
<div class="user-rate">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="rate-form">

    <?php $form = ActiveForm::begin(); ?>
    <?php  foreach ($criteria as $index => $criterion) {
        echo "<div class='well'>";
        $values = range($criterion->min, $criterion->max, $criterion->step);
        $buttons = array_combine(array_values($values), array_values($values));
        echo $form->field($rates[$criterion->id], "[$criterion->id]rate")->label($criterion->customName)->dropDownList($buttons);
        echo $form->field($rates[$criterion->id], "[$criterion->id]comment")->textInput();
        echo "</div>";
    } ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

</div>
