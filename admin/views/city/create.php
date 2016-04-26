<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\location\City */

$this->title = 'Добавить город';
?>
<div class="city-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'regions' => $regions
    ]) ?>

</div>
