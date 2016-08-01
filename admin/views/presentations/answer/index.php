<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\widgets\Select2;
use kartik\date\DatePicker;


$this->title = 'Ответы на презентации';
$this->registerJsFile('js/delete-selected.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
<div class="presentation-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <input type="button" class="btn btn-danger pull-right" value="Удалить" id="delete-presentation" data-confirm="Удалить ответ?">
    </br>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'label' => 'ID Презентации',
                'attribute'=>'presentation.id',
                'value'=>'presentation.id',
                'contentOptions'=>['style'=>'width: 150px;'],
            ],
            [
                'attribute'=>'presentation.title',
                'value'=>function($model) {
                    return Html::a($model->presentation->title, ['/presentation/view', 'id'=>$model->presentation->id]);
                },
                'filter'=>Select2::widget([
                    'model' => $searchModel,
                    'data' => $titles,
                    'attribute'=>'presentation.title',
                    'options' => [
                        'placeholder' => 'Выберите презентацию ...',
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
                'class' => 'yii\grid\CheckboxColumn',
                'header' => Html::checkBox('selection_all', false, [
                    'class' => 'select-on-check-all',
                    'label' => 'Все',
                ]),
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template'=>'{view} {delete}',
                'buttons'=> [
                    'view'=>function ($url, $model, $key) {
                        return Html::a('<i class="glyphicon glyphicon-eye-open"></i> ', ['view', 'user_id'=>$model->user->id, 'presentation_id'=>$model->presentation->id], [
                            'title'=>'Просмотреть',
                        ]);
                    },
                ],
            ],
        ],
    ]); ?>

</div>
