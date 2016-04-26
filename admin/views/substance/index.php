<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\widgets\Select2;

$this->title = 'Действующие вещества';
?>
<div class="substance-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a('Добавить вещество', ['create'], ['class' => 'btn btn-success']) ?>
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
                'attribute' => 'cyrillic',
                'value'=>'cyrillic',
                'filter'=>Select2::widget([
                    'model' => $searchModel,
                    'data' => $cyrillic_names,
                    'attribute'=>'cyrillic',
                    'options' => [
                        'placeholder' => 'Выберите вещество ...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'width' => '450px',
                    ],
                ]),
            ],
            [
                'attribute' => 'name',
                'value'=>'name',
                'filter'=>Select2::widget([
                    'model' => $searchModel,
                    'data' => $names,
                    'attribute'=>'name',
                    'options' => [
                        'placeholder' => 'Выберите вещество ...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'width' => '450px',
                    ],
                ]),
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
