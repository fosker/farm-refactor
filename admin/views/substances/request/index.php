<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\date\DatePicker;
use kartik\widgets\Select2;

$this->title = 'Запросы по веществам';
?>
<div class="request-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'id',
                'contentOptions'=>['style'=>'width: 150px;'],
            ],
            [
                'label' => 'Пользователь',
                'attribute' => 'user.name',
                'value'=>function($model) {
                    return Html::a($model->user->name, ['/user/view', 'id'=>$model->user->id]);
                },
                'format'=>'html',
                'filter'=>Select2::widget([
                    'model' => $searchModel,
                    'data' => $users,
                    'attribute'=>'user.name',
                    'options' => [
                        'placeholder' => 'Выберите пользователя ...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'width' => '200px',
                    ],
                ]),
            ],
            [
                'attribute' => 'user.position_id',
                'value'=>'user.position.name',
                'filter'=>Select2::widget([
                    'model' => $searchModel,
                    'data' => $positions,
                    'attribute'=>'user.position_id',
                    'options' => [
                        'placeholder' => 'Выберите должность ...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'width' => '200px'
                    ],
                ]),
            ],
            [
                'attribute' => 'substance.cyrillic',
                'value'=>function($model) {
                    return Html::a($model->substance->cyrillic, ['/substance/view', 'id'=>$model->substance->id]);
                },
                'format'=>'html',
                'contentOptions'=>['style'=>'width: 250px;'],
            ],
            [
                'attribute' => 'date_request',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'date_request',
                    'pluginOptions' => [
                        'format' => 'yyyy-mm-dd',
                    ]
                ]),
                'format' => ['datetime'],
                'contentOptions'=>['style'=>'width: 200px;'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template'=>'{view} {delete}',
            ],
        ],
    ]); ?>

</div>
