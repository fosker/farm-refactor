<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\profile\Firm */

$this->title = 'Редактирование данных: ' . ' ' . $model->name;
?>
<div class="firm-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
