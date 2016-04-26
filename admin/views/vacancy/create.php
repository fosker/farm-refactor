<?php

use yii\helpers\Html;

$this->title = 'Добавить вакансию';
?>
<div class="vacancy-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'cities'=>$cities,
        'pharmacies'=>$pharmacies,
        'vacancy_cities' => $vacancy_cities,
        'vacancy_pharmacies' => $vacancy_pharmacies
    ]) ?>

</div>
