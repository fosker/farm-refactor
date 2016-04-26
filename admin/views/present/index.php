<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\widgets\Select2;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\present\Search */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $vendors array */
/* @var $cities array */

$this->title = 'Подарки';
?>
<div class="item-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить подарок', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'id',
                'contentOptions'=>['style'=>'width: 150px;'],
            ],
            'title',
            [
                'attribute'=>'vendor_id',
                'value'=>'vendor.name',
                'filter'=>Select2::widget([
                    'model' => $searchModel,
                    'data' => $vendors,
                    'attribute'=>'vendor_id',
                    'options' => [
                        'placeholder' => 'Выберите поставщика ...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'width' => '200px'
                    ],
                ])
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
                ])
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
