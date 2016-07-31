<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = 'Оповещение';
?>
<div class="push-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Список', ['index'],['class'=>'btn btn-info']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Удалить оповещение?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute'=>'link',
                'value'=> $model->linktitleHref,
                'format'=>'html',
            ],
            'message',
            'device_count',
            'views',
            'date_send:datetime'
        ],
    ]) ?>

</div>
