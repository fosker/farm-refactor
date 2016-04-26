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
        'cities'=>$cities,
        'pharmacies'=>$pharmacies,
        'survey_cities' => $survey_cities,
        'survey_pharmacies' => $survey_pharmacies,
        'survey_education' => $survey_education
    ]) ?>

</div>
