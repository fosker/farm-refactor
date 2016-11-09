<?php

use yii\helpers\Html;

$this->title = 'Создать критерий';
?>
<div class="criterion-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
