<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\PharmCompany */

$this->title = 'Изменить фарм. компанию: ' . $model->name;
?>
<div class="pharm-company-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div> 