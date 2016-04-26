<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\profile\City */

$this->title = 'Редактирование данных: ' . ' ' . $model->name;
?>
<div class="city-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'regions' => $regions
    ]) ?>

</div>
