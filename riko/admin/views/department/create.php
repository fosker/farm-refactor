<?php

use yii\helpers\Html;

$this->title = 'Создать отдел';
?>
<div class="department-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'companies' => $companies
    ]) ?>

</div>
