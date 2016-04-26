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
        'cities'=>$cities,
        'pharmacies'=>$pharmacies,
        'education'=>$education,
        'news_cities' => $news_cities,
        'news_pharmacies' => $news_pharmacies,
        'news_education' => $news_education,
        'old_cities' => $old_cities,
        'old_pharmacies' => $old_pharmacies,
        'old_education' => $old_education
    ]) ?>

</div>
