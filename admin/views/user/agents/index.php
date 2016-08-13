<?php

use common\models\User;
use yii\helpers\Html;
use yii\grid\GridView;
use kartik\widgets\Select2;
use common\models\Factory;
use yii\helpers\Url;

$this->title = 'Представители';
?>
<div class="agent-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'rowOptions' => function($model) {
            if($model->user->status == 0 || $model->user->status == 2) {
                return ['class' => 'danger'];
            }
        },
        'columns' => [
            [
                'attribute' => 'id',
                'contentOptions'=>['style'=>'width: 150px;'],
            ],
            [
                'label' => 'Имя',
                'attribute' => 'user.name',
                'value'=>'user.name',
                'filter'=>Select2::widget([
                    'model' => $searchModel,
                    'data' => $names,
                    'attribute'=>'user.name',
                    'options' => [
                        'placeholder' => 'Выберите имя ...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'width' => '175px'
                    ],
                ]),
            ],
            [
                'label' => 'Фабрика',
                'attribute' => 'factory_id',
                'value'=>function($model) {
                    return Factory::find()->where(['id' => $model->factory_id])->exists() ?
                        Html::a($model->factory->title,['/factory/view','id'=>$model->factory_id]) :
                        $model->factory_id;
                },
                'format'=>'html',
                'filter'=>Select2::widget([
                    'model' => $searchModel,
                    'data' => $factories,
                    'attribute'=>'factory_id',
                    'options' => [
                        'placeholder' => 'Выберите фабрику ...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'width' => '150px'
                    ],
                ]),
            ],
            [
                'attribute' => 'user.email',
                'value'=>'user.email',
                'filter'=>Select2::widget([
                    'model' => $searchModel,
                    'data' => $emails,
                    'attribute'=>'user.email',
                    'options' => [
                        'placeholder' => 'Выберите email ...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'width' => '150px'
                    ],
                ]),
            ],
            [
                'attribute'=>'user.status',
                'value' => function($model) {
                    switch($model->user->status) {
                        case User::STATUS_ACTIVE:
                            return 'активен';
                        case User::STATUS_VERIFY:
                            return 'ожидает';
                        case User::STATUS_NOTE_VERIFIED:
                            return 'не прошёл верификацию';
                    }
                },
                'filter'=>[User::STATUS_ACTIVE=>'активен',User::STATUS_VERIFY=>'ожидает',User::STATUS_NOTE_VERIFIED=>'не прошёл верификацию'],
                'contentOptions'=>['style'=>'width: 250px;'],
            ],
            [
                'attribute'=>'user.inGray',
                'value' => function($model) {
                    return [1 => 'да', 0 => 'нет'][$model->user->inGray];
                },
                'filter'=>[User::IN_GRAY=>'да',User::NOT_IN_GRAY=>'нет'],
                'contentOptions'=>['style'=>'width: 150px;'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template'=>'{ban} {accept} {view} {delete} {update} {not-verify} {gray} {not-gray}',
                'buttons'=>[
                    'accept' => function ($url, $model, $key) {
                        if($model->user->status == User::STATUS_VERIFY || $model->user->status == User::STATUS_NOTE_VERIFIED) {
                            return Html::a('<i class="glyphicon glyphicon-ok"></i>', ['accept', 'id'=>$model->id], [
                                'title'=>'Утвердить',
                                'data-confirm' => 'Вы уверены, что хотите подтвердить пользователя?',
                                'data-pjax'=>0,
                                'data-method'=>'post',
                            ]);
                        }
                    },
                    'ban' => function ($url, $model, $key) {
                        return $model->user->status == User::STATUS_ACTIVE ? Html::a('<i class="glyphicon glyphicon-remove"></i>', ['ban', 'id'=>$model->id], [
                            'data-confirm' => 'Вы уверены, что хотите забанить пользователя?',
                            'title'=>'Забанить',
                            'data-pjax'=>0,
                            'data-method'=>'post',
                        ]) : '';
                    },
                    'not-verify' => function ($url, $model, $key) {
                        return $model->user->status == User::STATUS_VERIFY ? Html::a('<i class="glyphicon glyphicon-thumbs-down"></i>', ['not-verify', 'id'=>$model->id], [
                            'data-confirm' => 'Вы уверены, что хотите отменить верификацию пользователя?',
                            'title'=>'Не прошёл верификацию',
                            'data-pjax'=>0,
                            'data-method'=>'post',
                        ]) : '';
                    },
                    'gray' => function ($url, $model, $key) {
                        return !$model->user->inGray ? Html::a('<i class="glyphicon glyphicon-list"></i>', ['gray', 'id' => $model->id], [
                            'class' => 'to_gray',
                            'title'=>'Добавить в серый список',
                        ]) : '';
                    },
                    'not-gray' => function ($url, $model, $key) {
                        return $model->user->inGray ? Html::a('<i class="glyphicon glyphicon-list" style="color:gray"></i>', ['not-gray', 'id'=>$model->id], [
                            'data-confirm' => 'Вы уверены, что хотите убрать пользователя из серого списка?',
                            'title'=>'Убрать из серого списка',
                            'data-pjax'=>0,
                            'data-method'=>'post',
                        ]) : '';
                    },
                ]
            ],
        ],
    ]); ?>

</div>
