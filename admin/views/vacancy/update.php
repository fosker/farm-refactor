<?php

use yii\helpers\Html;

$this->title = 'Редактирование данных: ' . ' ' . $model->title;
?>
<div class="vacancy-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'cities'=>$cities,
        'pharmacies'=>$pharmacies,
        'vacancy_cities' => $vacancy_cities,
        'vacancy_pharmacies' => $vacancy_pharmacies,
        'old_cities' => $old_cities,
        'old_pharmacies' => $old_pharmacies
    ]) ?>

</div>
