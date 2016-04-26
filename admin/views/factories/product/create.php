<?php

use yii\helpers\Html;


$this->title = 'Добавить продукт';

?>
<div class="product-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'factories' => $factories,
    ]) ?>

</div>
