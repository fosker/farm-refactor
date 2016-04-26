<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\widgets\Select2;

$this->title = 'Города';
?>
<div class="city-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить город', ['create'], ['class' => 'btn btn-success']) ?>
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
                'attribute' => 'name',
                'value'=>'name',
                'filter'=>Select2::widget([
                    'model' => $searchModel,
                    'data' => $names,
                    'attribute'=>'name',
                    'options' => [
                        'placeholder' => 'Выберите город ...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'width' => '450px',
                    ],
                ]),
            ],
            [
                'attribute'=>'region_id',
                'value'=>'region.name',
                'filter'=>Select2::widget([
                    'model' => $searchModel,
                    'data' => $regions,
                    'attribute'=>'region_id',
                    'options' => [
                        'placeholder' => 'Выберите район ...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'width' => '450px',
                    ],
                ])
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
