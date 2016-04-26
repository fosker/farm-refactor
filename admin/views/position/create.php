<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\profile\Position */

$this->title = 'Создать должность';
?>
<div class="position-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
