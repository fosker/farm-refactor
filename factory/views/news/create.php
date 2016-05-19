<?php

use yii\helpers\Html;


$this->title = 'Добавить новость';
?>
<div class="news-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
