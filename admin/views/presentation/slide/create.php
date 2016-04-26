<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\presentation\Slide */

$this->title = 'Добавить слайд';
?>
<div class="slide-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
