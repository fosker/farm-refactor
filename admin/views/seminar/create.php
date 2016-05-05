<?php

use yii\helpers\Html;

$this->title = 'Добавить семинар';
?>
<div class="seminar-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'education'=>$education,
        'regions'=>$regions,
        'cities'=>$cities,
        'companies'=>$companies,
        'types'=>$types,
        'factories'=>$factories,
    ]) ?>

</div>
