<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\widgets\Select2;

$this->title = 'Продукты';
?>
<div class="product-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить продукт', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'id',
                'contentOptions'=>['style'=>'width: 100px;'],
            ],
            [
                'attribute'=>'title',
                'filter'=>Select2::widget([
                    'model' => $searchModel,
                    'data' => $titles,
                    'attribute'=>'title',
                    'options' => [
                        'placeholder' => 'Выберите товар ...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'width' => '350px',
                    ],
                ])
            ],
            'priority',
            [
                'attribute'=>'factory_id',
                'value'=>function($model) {
                    return Html::a($model->factory->title, ['/factory/view', 'id' => $model->factory_id]);
                },
                'format'=>'html',
                'filter'=>Select2::widget([
                    'model' => $searchModel,
                    'data' => $factories,
                    'attribute'=>'factory_id',
                    'options' => [
                        'placeholder' => 'Выберите компанию ...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'width' => '350px',
                    ],
                ])
            ],
            [
                'attribute'=>'status',
                'value'=>function($model) {
                    return $model::getStatusList()[$model->status];
                },
                'filter'=>$searchModel::getStatusList(),
                'contentOptions'=>['style'=>'width: 125px;'],
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
