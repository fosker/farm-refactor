<?php

use yii\helpers\Html;

$this->title = 'Изменить компанию: ' . $model->name;
?>
<div class="company-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
