<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\widgets\Select2;
use kartik\date\DatePicker;

$this->title = 'Подарки';
$this->registerJsFile('js/show-comment.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
<div class="present-index">

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
                'label' => 'Логин',
                'attribute'=>'user.login',
                'value'=>function($model) {
                    return Html::a($model->user->login, ['/user/view', 'id'=>$model->user->id]);
                },
                'format'=>'html',
                'filter'=>Select2::widget([
                    'model' => $searchModel,
                    'data' => $logins,
                    'attribute'=>'user.id',
                    'options' => [
                        'placeholder' => 'Выберите логин ...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'width' => '250px'
                    ],
                ])
            ],
            [
                'label' => 'Название подарка',
                'attribute'=>'item.title',
                'value'=>function($model) {
                    return Html::a($model->item->title, ['/present/view', 'id'=>$model->item->id]);
                },
                'format'=>'html',
                'filter'=>Select2::widget([
                    'model' => $searchModel,
                    'data' => $items,
                    'attribute'=>'item.title',
                    'options' => [
                        'placeholder' => 'Выберите подарок ...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'width' => '250px'
                    ],
                ])
            ],
            'count',
            'promo',
            [
                'attribute' => 'date_buy',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'date_buy',
                    'pluginOptions' => [
                        'format' => 'yyyy-mm-dd',
                    ]
                ]),
                'format' => ['datetime'],
                'contentOptions'=>['style'=>'width: 250px;'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template'=>'{view} {use} {delete}{comment}',
                'buttons'=>[
                    'use'=>function ($url, $model, $key) {
                        return $model->promo ? Html::a('<i class="glyphicon glyphicon-gift"></i>', [ 'use', 'id'=>$model->id],
                            ['title'=>'Использовать']) : '';
                    },
                    'comment'=>function ($url, $model, $key) {
                        return Html::a('<i class="glyphicon glyphicon-tag"></i>', ['comment', 'id'=>$model->id],
                            [
                                'class' => 'list-comment',
                                'title'=>$model->comment,
                            ]);
                    },
                ],
            ],
        ],
    ]); ?>

</div>
