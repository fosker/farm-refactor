<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\widgets\Select2;

$this->title = 'Видео';
?>
<div class="video-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить видео', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'id',
                'contentOptions'=>['style'=>'width: 150px;'],
            ],
            [
                'attribute'=>'title',
                'value'=>'title',
                'filter'=>Select2::widget([
                    'model' => $searchModel,
                    'data' => $seminars,
                    'attribute'=>'title',
                    'options' => [
                        'placeholder' => 'Выберите видео ...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'width' => '300px'
                    ],
                ]),
            ],
            'tags',
            [
                'class' => 'yii\grid\ActionColumn',
            ],
        ],
    ]); ?>

</div>
