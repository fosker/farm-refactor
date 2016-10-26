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
        'companies'=>$companies,
        'pharmacies'=>$pharmacies,
        'types'=>$types,
        'factories'=>$factories,
        'news'=>$news,
        'old_cities'=>$old_cities,
        'old_companies'=>$old_companies,
        'old_types' => $old_types,
        'old_education' => $old_education,
        'old_relations' => $old_relations
    ]) ?>

</div>
