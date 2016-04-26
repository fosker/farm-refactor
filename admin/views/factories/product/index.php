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
            [
                'attribute'=>'factory_id',
                'value'=>'factory.title',
                'filter'=>Select2::widget([
                    'model' => $searchModel,
                    'data' => $factories,
                    'attribute'=>'factory_id',
                    'options' => [
                        'placeholder' => 'Выберите фабрику ...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'width' => '350px',
                    ],
                ])
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
