<?php

use yii\helpers\Html;


$this->title = 'Добавить акцию';
?>
<div class="stock-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'factories' => $factories,
        'cities'=>$cities,
        'pharmacies'=>$pharmacies,
        'education'=>$education,
        'stock_cities' => $stock_cities,
        'stock_pharmacies' => $stock_pharmacies,
        'stock_education' => $stock_education
    ]) ?>

</div>
