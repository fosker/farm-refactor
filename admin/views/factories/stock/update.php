<?php

use yii\helpers\Html;


$this->title = 'Редактирование данных: ' . ' ' . $model->title;

?>
<div class="stock-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'factories' => $factories,
        'cities'=>$cities,
        'pharmacies'=>$pharmacies,
        'education'=>$education,
        'stock_cities' => $stock_cities,
        'stock_pharmacies' => $stock_pharmacies,
        'stock_education' => $stock_education,
        'old_cities' => $old_cities,
        'old_pharmacies' => $old_pharmacies,
        'old_education' => $old_education
    ]) ?>

</div>
