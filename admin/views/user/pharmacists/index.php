<?php

use common\models\User;
use kartik\widgets\Growl;
use yii\helpers\Html;
use yii\grid\GridView;
use kartik\widgets\Select2;

$this->title = 'Фармацевты';
?>
<div class="pharmacist-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'rowOptions' => function($model) {
            if($model->user->status == 0) {
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
                'attribute' => 'pharmacy_id',
                'value'=>'pharmacy.name',
                'filter'=>Select2::widget([
                    'model' => $searchModel,
                    'data' => $pharmacies,
                    'attribute'=>'pharmacy_id',
                    'options' => [
                        'placeholder' => 'Выберите аптеку ...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'width' => '175px'
                    ],
                ]),
            ],
            [
                'label' => 'Компания',
                'attribute' => 'pharmacy.company.id',
                'value'=>'pharmacy.company.title',
                'filter'=>Select2::widget([
                    'model' => $searchModel,
                    'data' => $pharmacies,
                    'attribute'=>'pharmacy.company.id',
                    'options' => [
                        'placeholder' => 'Выберите аптеку ...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'width' => '175px'
                    ],
                ]),
            ],
            [
                'label' => 'Город',
                'attribute' => 'pharmacy.city.id',
                'value'=>'pharmacy.city.name',
                'filter'=>Select2::widget([
                    'model' => $searchModel,
                    'data' => $pharmacies,
                    'attribute'=>'pharmacy.city.id',
                    'options' => [
                        'placeholder' => 'Выберите город ...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'width' => '175px'
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
                  return $model->user->status == User::STATUS_ACTIVE ? 'активен' : 'ожидает';
                },
                'filter'=>[User::STATUS_ACTIVE=>'активен',User::STATUS_VERIFY=>'ожидает'],
                'contentOptions'=>['style'=>'width: 250px;'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template'=>'{ban} {accept} {view} {delete} {update}',
                'buttons'=>[
                    'accept' => function ($url, $model, $key) {
                        return $model->user->status == User::STATUS_VERIFY ? Html::a('<i class="glyphicon glyphicon-ok"></i>', ['accept', 'id'=>$model->id], [
                            'title'=>'Утвердить',
                            'data-confirm' => 'Вы уверены, что хотите подтвердить пользователя?',
                            'data-pjax'=>0,
                            'data-method'=>'post',
                        ]) : '';
                    },

                    'ban' => function ($url, $model, $key) {
                        return $model->user->status == User::STATUS_ACTIVE ? Html::a('<i class="glyphicon glyphicon-remove"></i>', ['ban', 'id'=>$model->id], [
                            'data-confirm' => 'Вы уверены, что хотите забанить пользователя?',
                            'title'=>'Забанить',
                            'data-pjax'=>0,
                            'data-method'=>'post',
                        ]) : '';
                    },
                ]
            ],
        ],
    ]); ?>

</div>
