<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\widgets\Select2;
use kartik\date\DatePicker;
use common\models\stock\Reply;

$this->title = 'Ответы на акции';
?>
<div class="reply-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'label' => 'ID Акции',
                'attribute'=>'stock_id',
                'value'=>'stock_id',
                'contentOptions'=>['style'=>'width: 150px;'],
            ],
            [
                'attribute'=>'user.login',
                'value'=>function($model) {
                    return Html::a($model->user->login, ['/user/view', 'id'=>$model->user->id]);
                },
                'format' => 'html',
                'filter'=>Select2::widget([
                    'model' => $searchModel,
                    'data' => $logins,
                    'attribute'=>'user.id',
                    'options' => [
                        'placeholder' => 'Выберите логин пользователя ...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'width' => '350px'
                    ],
                ]),
            ],
            [
                'attribute'=>'stock.title',
                'value'=>function($model) {
                    return Html::a($model->stock->title, ['/stock/view', 'id'=>$model->stock->id]);
                },
                'format' => 'html',
                'filter'=>Select2::widget([
                    'model' => $searchModel,
                    'data' => $stocks,
                    'attribute'=>'stock.title',
                    'options' => [
                        'placeholder' => 'Выберите акцию ...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'width' => '350px'
                    ],
                ]),
            ],
            [
                'attribute' => 'date_add',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'date_add',
                    'pluginOptions' => [
                        'format' => 'yyyy-mm-dd',
                    ]
                ]),
                'format' => ['datetime'],
                'contentOptions'=>['style'=>'width: 250px;'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template'=>'{view} {delete}',
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template'=>'{downloaded}',
                'buttons'=>[
                    'downloaded'=>function ($url, $model, $key) {
                        return Html::a($model->downloaded == Reply::DOWNLOADED ? '<i class="glyphicon glyphicon-ok" style="color:lime"></i>' : '<i class="glyphicon glyphicon-ok text-muted"></i>', [$model->downloaded == Reply::NOT_DOWNLOADED ? 'downloaded' : 'not-downloaded', 'id'=>$model->id],
                            ['title'=>$model->downloaded ? 'Не скачано' : "Скачано"]);
                    },
                ],
            ],
        ],
    ]); ?>
</div>
