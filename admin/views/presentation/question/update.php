<?php

use yii\helpers\Html;
use common\models\Presentation;

/* @var $this yii\web\View */
/* @var $model common\models\presentation\Question */

$this->title = 'Редактирование данных: ' . ' ' . $model->question;
$this->params['breadcrumbs'][] = ['label' => Presentation::findOne(
    ['id' => Yii::$app->request->get('presentation_id')])->title,
    'url' => ['/presentation/view', 'id' => Yii::$app->request->get('presentation_id')]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="question-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
