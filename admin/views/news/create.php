<?php

use yii\helpers\Html;


$this->title = 'Добавить новость';
?>
<div class="news-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'cities'=>$cities,
        'pharmacies'=>$pharmacies,
        'education'=>$education,
        'news_cities' => $news_cities,
        'news_pharmacies' => $news_pharmacies,
        'news_education' => $news_education
    ]) ?>

</div>
