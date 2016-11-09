<?php

use yii\helpers\Html;

$this->title = 'Изменить должность: ' . $model->name;
?>
<div class="position-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
