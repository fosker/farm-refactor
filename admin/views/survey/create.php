<?php

use yii\helpers\Html;

$this->title = 'Добавить анкету';
?>
<div class="survey-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'questions'=>$questions,
        'options'=>$options,
        'education'=>$education,
        'regions'=>$regions,
        'cities'=>$cities,
        'companies'=>$companies,
        'types'=>$types,
        'factories'=>$factories,
    ]) ?>

</div>
