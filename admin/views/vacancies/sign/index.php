<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\widgets\Select2;
use kartik\date\DatePicker;

$this->title = 'Заявки на вакансии';
?>
<div class="sign-up-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'label' => 'ID Вакансии',
                'attribute'=>'vacancy_id',
                'value'=>'vacancy_id',
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
                        'placeholder' => 'Выберите пользователя ...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'width' => '250px'
                    ],
                ]),
            ],
            [
                'attribute'=>'vacancy.title',
                'value'=>function($model) {
                    return Html::a($model->vacancy->title, ['/vacancy/view', 'id'=>$model->vacancy->id]);
                },
                'format' => 'html',
                'filter'=>Select2::widget([
                    'model' => $searchModel,
                    'data' => $vacancies,
                    'attribute'=>'vacancy.title',
                    'options' => [
                        'placeholder' => 'Выберите вакансию ...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'width' => '250px'
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
                'contentOptions'=>['style'=>'width: 300px;'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template'=>'{view} {delete}',
            ],
        ],
    ]); ?>

</div>
