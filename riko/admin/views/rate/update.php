<?php

use yii\helpers\Html;

$this->title = 'Изменить оценку';
?>
<div class="rate-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'employees' => $employees,
        'criteria' => $criteria
    ]) ?>

</div>
