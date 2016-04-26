<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\widgets\Select2;

$this->title = 'Фирмы';
?>
<div class="firm-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить фирму', ['create'], ['class' => 'btn btn-success']) ?>
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
                        'placeholder' => 'Выберите фирму ...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'width' => '500px',
                    ],
                ]),
            ],
            [
                'attribute'=>'producer',
                'value' => function ($model) {
                    return $model->producer == '1' ? 'да' : 'нет';
                },
                'filter'=>array("1"=>"да","0"=>"нет"),
                'contentOptions'=>['style'=>'width: 200px;'],
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
