<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Seminar */

$this->title = 'Редактирование данных: ' . ' ' . $model->title;
?>
<div class="seminar-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'cities'=>$cities,
        'pharmacies'=>$pharmacies,
        'education'=>$education,
        'seminar_cities' => $seminar_cities,
        'seminar_pharmacies' => $seminar_pharmacies,
        'seminar_education' => $seminar_education,
        'old_cities' => $old_cities,
        'old_pharmacies' => $old_pharmacies,
        'old_education' => $old_education
    ]) ?>

</div>
