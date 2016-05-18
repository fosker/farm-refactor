<?php

use yii\helpers\Html;

$this->title = 'Создание администратора';

?>
<div class="admin-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'factories' => $factories,
    ]) ?>

</div>