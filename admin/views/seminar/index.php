<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\widgets\Select2;

$this->title = 'Семинары';
?>
<div class="seminar-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить семинар', ['create'], ['class' => 'btn btn-success']) ?>
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
                        'placeholder' => 'Выберите семинар ...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'width' => '200px'
                    ],
                ]),
            ],
            [
                'attribute'=>'email',
                'value'=>'email',
                'filter'=>Select2::widget([
                    'model' => $searchModel,
                    'data' => $emails,
                    'attribute'=>'email',
                    'options' => [
                        'placeholder' => 'Выберите email ...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'width' => '150px'
                    ],
                ]),
                'format' => 'email',
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
                        'width' => '175px'
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
                        'width' => '175px'
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
                'attribute'=>'status',
                'value'=>function($model) {
                    return $model::getStatusList()[$model->status];
                },
                'filter'=>$searchModel::getStatusList(),
                'contentOptions'=>['style'=>'width: 150px;'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template'=>'{view} {approve} {update} {delete}',
                'buttons'=>[
                    'approve'=>function ($url, $model, $key) {
                        return Html::a($model->status == $model::STATUS_HIDDEN ? '<i class="glyphicon glyphicon-ok"></i>' : '<i class="glyphicon glyphicon-remove"></i>', [$model->status == $model::STATUS_HIDDEN ? 'approve' : 'hide', 'id'=>$model->id]);
                    },
                ],
            ],
        ],
    ]); ?>

</div>
