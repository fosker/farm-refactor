<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\News */

$this->title = 'Изменить новость: ' . ' ' . $model->title;
?>
<div class="news-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'education'=>$education,
        'regions'=>$regions,
        'cities'=>$cities,
        'pharmacies'=>$pharmacies,
        'old_cities'=>$old_cities,
        'old_education' => $old_education,
        'old_pharmacies' => $old_pharmacies
    ]) ?>

</div>
