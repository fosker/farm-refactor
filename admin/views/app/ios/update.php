<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\generated\app\Ios */

$this->title = 'Изменить версию: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Версии Ios', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Изменить';
?>
<div class="ios-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
