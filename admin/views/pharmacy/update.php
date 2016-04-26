<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\profile\Pharmacy */
/* @var $firms array */
/* @var $cities array */

$this->title = 'Редактирование данных: ' . ' ' . $model->name;
?>
<div class="pharmacy-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'cities'=>$cities,
        'firms'=>$firms,
    ]) ?>

</div>
