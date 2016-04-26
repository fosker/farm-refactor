<?php

use yii\helpers\Html;

$this->title = 'Добавить баннер';
?>
<div class="banner-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'education'=>$education,
        'cities'=>$cities,
        'pharmacies'=>$pharmacies,
        'banner_cities' => $banner_cities,
        'banner_pharmacies' => $banner_pharmacies,
        'banner_education' => $banner_education
    ]) ?>

</div>
