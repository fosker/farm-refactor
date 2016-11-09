<?php

use yii\helpers\Html;

$this->title = 'Создать сотрудника';
?>
<div class="employee-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'departments' => $departments,
        'positions' => $positions
    ]) ?>

</div>
