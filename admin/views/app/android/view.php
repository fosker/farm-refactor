<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\generated\app\Android */

$this->title = $model->version;
$this->params['breadcrumbs'][] = ['label' => 'Версии Android', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="android-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Список', ['index'], ['class' => 'btn btn-info']) ?>
        <?= Html::a('Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы действительно хотите удалить версию Android?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'version',
            [
                'attribute' => 'is_forced',
                'value' => $model->is_forced ? 'да' : 'нет'
            ],
            'message'
        ],
    ]) ?>

</div>
