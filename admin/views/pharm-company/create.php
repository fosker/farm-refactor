<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\PharmCompany */

$this->title = 'Добавить фарм. компанию';
?>
<div class="pharm-company-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>