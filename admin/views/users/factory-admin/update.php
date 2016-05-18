<?php

use yii\helpers\Html;

$this->title = 'Редактирование данных: ' . ' ' . $model->name;

?>
<div class="product-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'factories' => $factories,
        'update' => true,
    ]) ?>

</div>
