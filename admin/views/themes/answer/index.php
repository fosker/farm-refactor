<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\widgets\Select2;
use kartik\date\DatePicker;
use common\models\stock\Reply;

$this->title = 'Ответы на темы';
$this->registerJsFile('js/show-comment.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
<div class="reply-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute'=>'user.login',
                'value'=>function($model) {
                    return Html::a($model->user->login, ['/user/view', 'id'=>$model->user->id]);
                },
                'format' => 'html',
                'filter'=>Select2::widget([
                    'model' => $searchModel,
                    'data' => $logins,
                    'attribute'=>'user.login',
                    'options' => [
                        'placeholder' => 'Выберите логин пользователя ...',
                    ],
                ]),
            ],
            [
                'attribute'=>'user.name',
                'value'=>function($model) {
                    return Html::a($model->user->name, ['/user/view', 'id'=>$model->user->id]);
                },
                'format' => 'html',
                'filter'=>Select2::widget([
                    'model' => $searchModel,
                    'data' => $names,
                    'attribute'=>'user.name',
                    'options' => [
                        'placeholder' => 'Выберите имя пользователя ...',
                    ],
                ]),
            ],
            [
                'attribute'=>'theme.title',
                'value'=>function($model) {
                    return Html::a($model->theme->title, ['/theme/view', 'id'=>$model->theme->id]);
                },
                'format' => 'html',
                'filter'=>Select2::widget([
                    'model' => $searchModel,
                    'data' => $themes,
                    'attribute'=>'theme.title',
                    'options' => [
                        'placeholder' => 'Выберите тему ...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'width' => '350px'
                    ],
                ]),
            ],
            [
                'attribute' => 'date_added',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'date_added',
                    'pluginOptions' => [
                        'format' => 'yyyy-mm-dd',
                    ]
                ]),
                'format' => ['datetime'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template'=>'{view} {delete} {answered} {comment}',
                'buttons'=>[
                    'answered'=>function ($url, $model, $key) {
                        return Html::a($model->is_answered ? '<i class="glyphicon glyphicon-ok" style="color:lime"></i>' : '<i class="glyphicon glyphicon-ok text-muted"></i>', [$model->is_answered ? 'not-answered' : 'answered', 'id'=>$model->id],
                            ['title'=>$model->is_answered ? 'Не отвечено' : "Отвечено"]);
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
