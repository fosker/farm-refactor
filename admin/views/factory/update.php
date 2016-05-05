<?php

use yii\helpers\Html;

$this->title = 'Редактирование данных: ' . ' ' . $model->title;
?>
<div class="factory-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
