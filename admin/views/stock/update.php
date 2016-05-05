<?php

use yii\helpers\Html;


$this->title = 'Редактирование данных: ' . ' ' . $model->title;

?>
<div class="stock-update">

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
        'old_education' => $old_education
    ]) ?>

</div>
