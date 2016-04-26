<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\widgets\Select2;
use kartik\date\DatePicker;

$this->title = 'Комментарии';
?>
<div class="comment-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'label' => 'Автор',
                'attribute' => 'user.name',
                'value'=>function($model) {
                    return Html::a($model->user->name, ['/user/view', 'id'=>$model->user_id]);
                },
                'format'=>'html',
                'filter'=>Select2::widget([
                    'model' => $searchModel,
                    'data' => $users,
                    'attribute'=>'user.id',
                    'options' => [
                        'placeholder' => 'Выберите пользователя ...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'width' => '200px'
                    ],
                ]),
            ],
            [
                'label'=>'Вакансия',
                'attribute' => 'vacancy_id',
                'value'=>function($model) {
                    return Html::a($model->vacancy->title, ['/vacancy/view', 'id' => $model->vacancy_id]);
                },
                'format'=>'html',
                'filter'=>Select2::widget([
                    'model' => $searchModel,
                    'data' => $vacancies,
                    'attribute'=>'vacancy_id',
                    'options' => [
                        'placeholder' => 'Выберите вакансию ...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'width' => '200px'
                    ],
                ]),
            ],
            [
                'attribute' => 'comment',
                'value' => 'comment',
                'contentOptions'=>['style'=>'width: 250px;'],

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
                'contentOptions'=>['style'=>'width: 300px;'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {delete}',
            ],
        ],
    ]); ?>

</div>
