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
        'education'=>$education,
        'regions'=>$regions,
        'cities'=>$cities,
        'companies'=>$companies,
        'types'=>$types,
        'factories'=>$factories,
        'old_cities'=>$old_cities,
        'old_companies'=>$old_companies,
        'old_types' => $old_types,
        'old_education' => $old_education,
        'old_lists' => $old_lists
    ]) ?>

</div>
