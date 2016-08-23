<?php

use common\models\User;
use yii\helpers\Html;
use yii\grid\GridView;
use common\models\Factory;

$this->title = 'Представители';
$this->registerJsFile('js/show-comment.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
<div class="agent-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', [
        'model' => $searchModel,
        'names' => $names,
        'factories' => $factories,
    ]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'rowOptions' => function($model) {
            if($model->user->status == 0 || $model->user->status == 2) {
                return ['class' => 'danger'];
            }
            if($model->user->inList == User::IN_GRAY) {
                return ['style' => 'background: #C0C0C0'];
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
                'contentOptions'=>['style'=>'width: 300px;'],
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
                'contentOptions'=>['style'=>'width: 300px;'],
            ],
            [
                'attribute' => 'user.points',
                'value'=>'user.points',
                'contentOptions'=>['style'=>'width: 150px;'],
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
                'contentOptions'=>['style'=>'width: 150px;'],
            ],
            [
                'attribute'=>'user.inList',
                'value' => function($model) {
                    return [0 => 'нет', 1 => 'в сером', 2 => 'в белом'][$model->user->inList];
                },
                'contentOptions'=>['style'=>'width: 150px;'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template'=>'{ban} {accept} {view} {delete} {update} {not-verify} {gray} {white} {out-list} ',
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
                        return ($model->user->inList != User::IN_GRAY && $model->user->inList != User::IN_WHITE) ? Html::a('<i class="glyphicon glyphicon-list"></i>', ['gray', 'id' => $model->id], [
                            'title'=>'Добавить в серый список',
                        ]) : '';
                    },
                    'white' => function ($url, $model, $key) {
                        return ($model->user->inList != User::IN_WHITE && $model->user->inList != User::IN_GRAY) ? Html::a('<i class="glyphicon glyphicon-align-center"></i>', ['white', 'id' => $model->id], [
                            'title'=>'Добавить в белый список',
                        ]) : '';
                    },
                    'out-list' => function ($url, $model, $key) {
                        return ($model->user->inList == User::IN_WHITE || $model->user->inList == User::IN_GRAY) ? Html::a('<i class="glyphicon glyphicon-list" style="color: gray"></i>', ['out-list', 'id'=>$model->id], [
                            'data-confirm' => 'Вы уверены, что хотите убрать пользователя из списка?',
                            'class' => 'list-comment',
                            'title'=>$model->user->comment,
                            'data-pjax'=>0,
                            'data-method'=>'post',
                        ]) : '';
                    },
                ]
            ],
        ],
    ]); ?>

</div>
