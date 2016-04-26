<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\widgets\Select2;
use kartik\date\DatePicker;
$this->title = 'Новости';
?>
<div class="news-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить новость', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute'=>'title',
                'value'=>'title',
                'filter'=>Select2::widget([
                    'model' => $searchModel,
                    'data' => $news,
                    'attribute'=>'title',
                    'options' => [
                        'placeholder' => 'Выберите новость ...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'width' => '200px'
                    ],
                ]),
            ],
            [
                'label'=>'Для городов',
                'value'=>'citiesView',
                'filter'=>Select2::widget([
                    'model' => $searchModel,
                    'data' => $cities,
                    'attribute'=>'city_id',
                    'options' => [
                        'placeholder' => 'Выберите города ...',
                        'multiple' => true,
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'width' => '150px'
                    ],
                ]),
            ],
            [
                'label'=>'Для фирм',
                'value'=>'firmsView',
                'filter'=>Select2::widget([
                    'model' => $searchModel,
                    'data' => $firms,
                    'attribute'=>'firm_id',
                    'options' => [
                        'placeholder' => 'Выберите фирмы ...',
                        'multiple' => true,
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'width' => '150px'
                    ],
                ])
            ],
            [
                'label'=>'Для образования',
                'value'=>'educationsView',
                'filter'=>Select2::widget([
                    'model' => $searchModel,
                    'data' => $education,
                    'attribute'=>'education_id',
                    'options' => [
                        'placeholder' => 'Выберите группы ...',
                        'multiple' => true,
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'width' => '150px'
                    ],
                ])
            ],
            [
                'attribute'=>'views',
                'value'=>function($model) {
                    return $model->countUniqueViews();
                },
            ],
            [
                'attribute' => 'date',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'date',
                    'pluginOptions' => [
                        'format' => 'yyyy-mm-dd',
                    ]
                ]),
                'format' => ['datetime'],
                'contentOptions'=>['style'=>'width: 250px;'],
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
