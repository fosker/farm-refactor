<?php

use common\models\User;
use kartik\widgets\Growl;
use yii\helpers\Html;
use yii\grid\GridView;
use kartik\widgets\Select2;

$this->title = 'Фармацевты';
$this->registerJsFile('js/show-comment.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
<div class="pharmacist-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'rowOptions' => function($model) {
            if($model->user->status == 0 || $model->user->status == 2) {
                return ['class' => 'danger'];
            };
            if($model->user->inGray) {
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
                'attribute' => 'pharmacy_id',
                'value'=>function($model) {
                    return Html::a($model->pharmacy->name, ['/pharmacy/view', 'id' => $model->pharmacy_id]);
                },
                'format'=>'html',
                'filter'=>Select2::widget([
                    'model' => $searchModel,
                    'data' => $pharmacies,
                    'attribute'=>'pharmacy_id',
                    'options' => [
                        'placeholder' => 'Выберите аптеку ...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'width' => '150px'
                    ],
                ]),
            ],
            [
                'label' => 'Компания',
                'attribute' => 'pharmacy.company.id',
                'value'=>function($model) {
                    return Html::a($model->pharmacy->company->title, ['/company/view', 'id' => $model->pharmacy->company_id]);
                },
                'format'=>'html',
                'filter'=>Select2::widget([
                    'model' => $searchModel,
                    'data' => $companies,
                    'attribute'=>'pharmacy.company.id',
                    'options' => [
                        'placeholder' => 'Выберите компанию ...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'width' => '150px'
                    ],
                ]),
            ],
            [
                'label' => 'Город',
                'attribute' => 'pharmacy.city.id',
                'value'=>'pharmacy.city.name',
                'filter'=>Select2::widget([
                    'model' => $searchModel,
                    'data' => $cities,
                    'attribute'=>'pharmacy.city.id',
                    'options' => [
                        'placeholder' => 'Выберите город ...',
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
                'contentOptions'=>['style'=>'width: 200px;'],
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
                            'title'=>'Добавить в серый список',
                        ]) : '';
                    },
                    'not-gray' => function ($url, $model, $key) {
                        return $model->user->inGray ? Html::a('<i class="glyphicon glyphicon-list" style="color:gray"></i>', ['not-gray', 'id'=>$model->id], [
                            'data-confirm' => 'Вы уверены, что хотите убрать пользователя из серого списка?',
                            'class' => 'to_gray',
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
