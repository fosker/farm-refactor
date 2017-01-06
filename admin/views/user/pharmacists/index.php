<?php

use common\models\User;
use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Фармацевты';
$this->registerJsFile('js/show-comment.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('js/delete-selected.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
<div class="pharmacist-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', [
        'model' => $searchModel,
        'names' => $names,
        'pharmacies' => $pharmacies,
        'cities' => $cities,
        'positions' => $positions,
        'companies' => $companies,
        'emails' => $emails,
        'logins' => $logins
    ]); ?>

    <input type="button" class="btn btn-danger pull-right" value="Удалить" id="delete-user" data-confirm="Удалить пользователей?">
    </br>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'rowOptions' => function($model) {
            if($model->user->status == 0 || $model->user->status == 2) {
                return ['class' => 'danger'];
            };
        },
        'columns' => [
            [
                'attribute' => 'id',
                'contentOptions'=>['style'=>'width: 100px;'],
            ],
            [
                'label' => 'Логин',
                'attribute' => 'user.login',
                'value'=>'user.login',
                'contentOptions'=>['style'=>'width: 200px;'],
            ],
            [
                'label' => 'Имя',
                'attribute' => 'user.name',
                'contentOptions'=>['style'=>'width: 200px;'],
            ],
            [
                'attribute' => 'pharmacy_id',
                'value'=>function($model) {
                    return Html::a($model->pharmacy->name, ['/pharmacy/view', 'id' => $model->pharmacy_id]);
                },
                'format'=>'html',
                'contentOptions'=>['style'=>'width: 200px;'],
            ],
            [
                'label' => 'Организация',
                'attribute' => 'pharmacy.company.id',
                'value'=>function($model) {
                    return Html::a($model->pharmacy->company->title, ['/company/view', 'id' => $model->pharmacy->company_id]);
                },
                'format'=>'html',
                'contentOptions'=>['style'=>'width: 200px;'],
            ],
            [
                'label' => 'Город',
                'attribute' => 'pharmacy.city.id',
                'value'=>'pharmacy.city.name',
            ],
            [
                'attribute' => 'user.points',
                'value'=>'user.points',
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
                    return [0 => 'в нейтральном', 1 => 'в черном', 2 => 'в белом', 3 => 'в сером'][$model->user->inList];
                },
                'contentOptions'=>['style'=>'width: 150px;'],
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
                'template' => '{ban} {accept} {view} {delete} {update} {not-verify} {black} {white} {gray} {neutral} ',
                'buttons' => [
                    'accept' => function ($url, $model, $key) {
                        if ($model->user->status == User::STATUS_VERIFY || $model->user->status == User::STATUS_NOTE_VERIFIED) {
                            return Html::a('<i class="glyphicon glyphicon-ok"></i>', ['accept', 'id' => $model->id], [
                                'title' => 'Утвердить',
                                'data-confirm' => 'Вы уверены, что хотите подтвердить пользователя?',
                                'data-pjax' => 0,
                                'data-method' => 'post',
                            ]);
                        }
                    },
                    'ban' => function ($url, $model, $key) {
                        return $model->user->status == User::STATUS_ACTIVE ? Html::a('<i class="glyphicon glyphicon-remove"></i>', ['ban', 'id' => $model->id], [
                            'data-confirm' => 'Вы уверены, что хотите забанить пользователя?',
                            'title' => 'Забанить',
                            'data-pjax' => 0,
                            'data-method' => 'post',
                        ]) : '';
                    },
                    'not-verify' => function ($url, $model, $key) {
                        return $model->user->status == User::STATUS_VERIFY ? Html::a('<i class="glyphicon glyphicon-thumbs-down"></i>', ['not-verify', 'id' => $model->id], [
                            'data-confirm' => 'Вы уверены, что хотите отменить верификацию пользователя?',
                            'title' => 'Не прошёл верификацию',
                            'data-pjax' => 0,
                            'data-method' => 'post',
                        ]) : '';
                    },
                    'black' => function ($url, $model, $key) {
                        return ($model->user->inList != User::IN_BLACK) ? Html::a('<i class="glyphicon glyphicon-list"></i>', ['black', 'id' => $model->id], [
                            'title' => 'Добавить в черный список',
                        ]) : '';
                    },
                    'white' => function ($url, $model, $key) {
                        return ($model->user->inList != User::IN_WHITE) ? Html::a('<i class="glyphicon glyphicon-align-center"></i>', ['white', 'id' => $model->id], [
                            'title' => 'Добавить в белый список',
                        ]) : '';
                    },
                    'gray' => function ($url, $model, $key) {
                        return ($model->user->inList != User::IN_GRAY) ? Html::a('<i class="glyphicon glyphicon-align-left"></i>', ['gray', 'id' => $model->id], [
                            'title' => 'Добавить в серый список',
                        ]) : '';
                    },
                    'neutral' => function ($url, $model, $key) {
                        return ($model->user->inList == User::IN_WHITE || $model->user->inList == User::IN_BLACK || $model->user->inList == User::IN_GRAY) ? Html::a('<i class="glyphicon glyphicon-list" style="color: gray"></i>', ['neutral', 'id' => $model->id], [
                            'data-confirm' => 'Добавить в нейтральный список',
                            'class' => 'list-comment',
                            'title' => $model->user->comment,
                            'data-pjax' => 0,
                            'data-method' => 'post',
                        ]) : '';
                    },
                ]
            ],
        ],
    ]); ?>

</div>
