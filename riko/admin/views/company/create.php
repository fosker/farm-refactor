<?php

use yii\helpers\Html;

$this->title = 'Создать компанию';
?>
<div class="company-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
