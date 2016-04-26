<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\date\DatePicker;
use kartik\widgets\Select2;

$this->title = 'Ответы на анкеты';
?>
<div class="answer-index">

    <h1><?= Html::encode($this->title) ?></h1>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'label' => 'ID Анкеты',
                'attribute'=>'survey.id',
                'value'=>'survey.id',
                'contentOptions'=>['style'=>'width: 150px;'],
            ],
            [
                'attribute'=>'survey.title',
                'value'=>function($model) {
                    return Html::a($model->survey->title, ['/survey/view', 'id'=>$model->survey->id]);
                },
                'filter'=>Select2::widget([
                    'model' => $searchModel,
                    'data' => $surveys,
                    'attribute'=>'survey.title',
                    'options' => [
                        'placeholder' => 'Выберите анкету ...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'width' => '250px'
                    ],
                ]),
                'format'=>'html',
            ],
            [
                'label' => 'Логин пользователя',
                'attribute'=>'user.login',
                'value'=>function($model) {
                    return Html::a($model->user->login, ['/user/view', 'id'=>$model->user->id]);
                },
                'filter'=>Select2::widget([
                    'model' => $searchModel,
                    'data' => $logins,
                    'attribute'=>'user.id',
                    'options' => [
                        'placeholder' => 'Выберите пользователя ...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'width' => '250px'
                    ],
                ]),
                'format'=>'html',
            ],
            [
                'attribute' => 'added',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'added',
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
                'buttons'=> [
                    'view'=>function ($url, $model, $key) {
                        return Html::a('<i class="glyphicon glyphicon-eye-open"></i> ', ['view', 'user_id'=>$model->user->id, 'survey_id'=>$model->survey->id], [
                            'title'=>'Просмотреть',
                        ]);
                    },
                    'delete'=>function ($url, $model, $key) {
                        return Html::a('<i class="glyphicon glyphicon-trash"></i> ', ['delete', 'user_id'=>$model->user->id, 'survey_id'=>$model->survey->id], [
                            'title'=>'Удалить',
                            'data-pjax'=>0,
                            'data-method'=>'post',
                            'data-confirm'=>'Удалить ответ?',
                        ]);
                    },
                ],
            ],
        ],
    ]); ?>

</div>
