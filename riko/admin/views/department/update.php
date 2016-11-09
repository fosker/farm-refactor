<?php

use yii\helpers\Html;


$this->title = 'Изменить отдел: ' . $model->name;
?>
<div class="department-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'companies' => $companies
    ]) ?>

</div>
