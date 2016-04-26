<?php

use yii\helpers\Html;

$this->title = 'Добавить видео';
?>
<div class="video-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
