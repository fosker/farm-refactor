<?php

use yii\helpers\Html;


$this->title = 'Добавить тип пользователя';
?>
<div class="type-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
