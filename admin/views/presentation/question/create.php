<?php

use yii\helpers\Html;
use common\models\Presentation;

$this->title = 'Добавление вопроса';
$this->params['breadcrumbs'][] = ['label' => Presentation::findOne(
    ['id' => Yii::$app->request->get('presentation_id')])->title,
    'url' => ['/presentation/view', 'id' => Yii::$app->request->get('presentation_id')]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="question-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
