<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Banner */
/* @var $education array */
/* @var $cities array */

$this->title = 'Редактировать баннер: ' . ' ' . $model->title;
?>
<div class="banner-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'education'=>$education,
        'cities'=>$cities,
        'pharmacies'=>$pharmacies,
        'banner_cities' => $banner_cities,
        'banner_pharmacies' => $banner_pharmacies,
        'banner_education' => $banner_education,
        'old_cities' => $old_cities,
        'old_pharmacies' => $old_pharmacies,
        'old_education' => $old_education
    ]) ?>

</div>
