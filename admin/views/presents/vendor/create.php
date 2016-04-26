<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\shop\Vendor */

$this->title = 'Добавить поставщика';
?>
<div class="vendor-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
