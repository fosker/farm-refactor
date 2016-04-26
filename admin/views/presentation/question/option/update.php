<?php

use yii\helpers\Html;
use common\models\Presentation;
use common\models\presentation\Question;

$this->title = 'Редактирование данных: ' . ' ' . $model->value;
$this->params['breadcrumbs'][] = ['label' => Presentation::findOne(
    ['id' => Yii::$app->request->get('presentation_id')])->title,
    'url' => ['/presentation/view', 'id' => Yii::$app->request->get('presentation_id')]];
$this->params['breadcrumbs'][] = ['label' => Question::findOne(
    ['id' => Yii::$app->request->get('question_id')])->question,
    'url' => ['/presentation/view-option', 'question_id' => Yii::$app->request->get('question_id'),
        'presentation_id' => Yii::$app->request->get('presentation_id')]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="option-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
