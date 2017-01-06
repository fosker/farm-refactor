<?php

use yii\helpers\Html;


$this->title = 'Добавить компанию';
?>
<div class="factory-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
