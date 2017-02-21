<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\generated\app\Android */

$this->title = 'Изменить версию: ' . $model->id;
?>
<div class="android-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
