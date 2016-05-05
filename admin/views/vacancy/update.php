<?php

use yii\helpers\Html;

$this->title = 'Редактирование данных: ' . ' ' . $model->title;
?>
<div class="vacancy-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'regions'=>$regions,
        'cities'=>$cities,
        'companies'=>$companies,
        'factories'=>$factories,
        'old_cities'=>$old_cities,
        'old_companies'=>$old_companies,
    ]) ?>

</div>
