<?php

use yii\helpers\Html;

$this->title = 'Изменить сотрудника: ' . $model->name;
?>
<div class="employee-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'departments' => $departments,
        'positions' => $positions
    ]) ?>

</div>
