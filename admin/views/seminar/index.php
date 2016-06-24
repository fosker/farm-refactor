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
                    'data' => $titles,
                    'attribute'=>'title',
                    'options' => [
                        'placeholder' => 'Выберите семинар ...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'width' => '160px'
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
                'label'=>'Для компаний',
                'value'=>'companyView',
                'filter'=>Select2::widget([
                    'model' => $searchModel,
                    'data' => $companies,
                    'attribute'=>'company_id',
                    'options' => [
                        'placeholder' => 'Выберите компании ...',
                        'multiple' => true,
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'width' => '150px'
                    ],
                ])
            ],
            [
                'label'=>'Для типов',
                'value'=>'typesView',
                'filter'=>Select2::widget([
                    'model' => $searchModel,
                    'data' => $types,
                    'attribute'=>'type_id',
                    'options' => [
                        'placeholder' => 'Выберите типы пользователей ...',
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
