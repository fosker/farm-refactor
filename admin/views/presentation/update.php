<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Presentation */

$this->title = 'Редактирование данных: ' . ' ' . $model->title;
?>
<div class="presentation-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'cities'=>$cities,
        'pharmacies'=>$pharmacies,
        'education'=>$education,
        'presentation_cities' => $presentation_cities,
        'presentation_pharmacies' => $presentation_pharmacies,
        'presentation_education' => $presentation_education,
        'old_cities' => $old_cities,
        'old_pharmacies' => $old_pharmacies,
        'old_education' => $old_education
    ]) ?>

</div>
