<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\widgets\Select2;

$this->title = 'Темы';
?>
<div class="theme-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить тему', ['create'], ['class' => 'btn btn-success']) ?>
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
                'attribute' => 'title',
                'value'=>'title',
                'filter'=>Select2::widget([
                    'model' => $searchModel,
                    'data' => $titles,
                    'attribute'=>'title',
                    'options' => [
                        'placeholder' => 'Выберите тему ...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'width' => '250px',
                    ],
                ]),
            ],
            [
                'attribute'=>'factory_id',
                'value'=>function($model) {
                    return Html::a($model->factory->title,['/factory/view','id'=>$model->factory_id]);
                },
                'format' => 'html',
                'filter'=>Select2::widget([
                    'model' => $searchModel,
                    'data' => $factories,
                    'attribute'=>'factory_id',
                    'options' => [
                        'placeholder' => 'Выберите компанию ...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'width' => '200px',
                    ],
                ])
            ],
            [
                'label' => 'Тип темы',
                'attribute'=>'form_id',
                'value'=>function($model) {
                    return $model->getTypesList()[$model->form_id];
                },
                'filter'=>$searchModel::getTypesList(),
                'contentOptions'=>['style'=>'width: 250px;'],
            ],
            [
                'attribute' => 'email',
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
                        'width' => '250px',
                    ],
                ]),
            ],
            [
                'attribute'=>'status',
                'value'=>function($model) {
                    return $model::getStatusList()[$model->status];
                },
                'filter'=>$searchModel::getStatusList(),
                'contentOptions'=>['style'=>'width: 125px;'],
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
