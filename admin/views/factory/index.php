<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\widgets\Select2;

$this->title = 'Фабрики';
?>
<div class="factory-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить фабрику', ['create'], ['class' => 'btn btn-success']) ?>
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
                        'placeholder' => 'Выберите фабрику ...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'width' => '500px'
                    ],
                ]),
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
