<?php

use yii\helpers\Html;

$this->title = 'Изменить критерий: ' . $model->name;
?>
<div class="criterion-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
