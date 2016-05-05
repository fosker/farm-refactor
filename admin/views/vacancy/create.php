<?php

use yii\helpers\Html;

$this->title = 'Добавить вакансию';
?>
<div class="vacancy-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'regions'=>$regions,
        'cities'=>$cities,
        'companies'=>$companies,
        'factories'=>$factories,
    ]) ?>

</div>
