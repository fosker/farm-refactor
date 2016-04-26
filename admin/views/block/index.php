<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\widgets\Select2;

$this->title = 'Страницы';

?>
<div class="block-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Создать страницу', ['create'], ['class' => 'btn btn-success']) ?>
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
                    'data' => $titles,
                    'attribute'=>'title',
                    'options' => [
                        'placeholder' => 'Выберите страницу ...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'width' => '400px'
                    ],
                ]),
            ],
            [
                'label' => 'Комментариев',
                'attribute' => 'comment_count',
                'format' => 'html',
                'value' => function($model){
                    return Html::a($model->comments, ['/blocks/comment', 'Search[block_id]' => $model->id]);
                },
            ],
            [
                'label' => 'Средняя оценка',
                'attribute' => 'mark_avg',
                'format' => 'html',
                'value' => function($model){
                    return Html::a($model->mark, ['/blocks/mark', 'Search[block_id]' => $model->id]);
                },
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
