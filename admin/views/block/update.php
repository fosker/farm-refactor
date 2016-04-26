<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Block */

$this->title = 'Редактирование данных: ' . ' ' . $model->title;
?>
<div class="block-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
