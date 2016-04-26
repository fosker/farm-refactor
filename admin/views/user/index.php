<?php

use common\models\User;
use kartik\widgets\Growl;
use yii\helpers\Html;
use yii\grid\GridView;
use kartik\widgets\Select2;

$this->title = 'Пользователи';
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'rowOptions' => function($model) {
            if($model->status == 0) {
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
                'attribute' => 'name',
                'value'=>'name',
                'filter'=>Select2::widget([
                    'model' => $searchModel,
                    'data' => $names,
                    'attribute'=>'name',
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
                'attribute' => 'city_id',
                'value'=>'city.name',
                'filter'=>Select2::widget([
                    'model' => $searchModel,
                    'data' => $cities,
                    'attribute'=>'city_id',
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
                'attribute' => 'pharmacy_id',
                'value'=>function($model) {
                    return Html::a($model->pharmacy->name, ['/pharmacy/view', 'id'=>$model->pharmacy->id]);
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
                'label' => 'Фирма',
                'attribute' => 'firm',
                'value'=>function($model) {
                    return Html::a($model->firm->name, ['/firm/view', 'id'=>$model->firm->id]);
                },
                'format'=>'html',
                'filter'=>Select2::widget([
                    'model' => $searchModel,
                    'data' => $firms,
                    'attribute'=>'firm',
                    'options' => [
                        'placeholder' => 'Выберите фирму ...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'width' => '150px'
                    ],
                ]),
            ],
            [
                'attribute' => 'email',
                'value'=>'email',
                'filter'=>Select2::widget([
                    'model' => $searchModel,
                    'data' => $emails,
                    'attribute'=>'email',
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
                'attribute'=>'status',
                'value' => function($model) {
                  return $model->status == User::STATUS_ACTIVE ? 'активен' : 'ожидает';
                },
                'filter'=>[User::STATUS_ACTIVE=>'активен',User::STATUS_VERIFY=>'ожидает'],
                'contentOptions'=>['style'=>'width: 250px;'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template'=>'{ban} {accept} {view} {delete} {update}',
                'buttons'=>[
                    'accept' => function ($url, $model, $key) {
                        return $model->status == User::STATUS_VERIFY ? Html::a('<i class="glyphicon glyphicon-ok"></i>', ['accept', 'id'=>$model->id], [
                            'title'=>'Утвердить',
                            'data-confirm' => 'Вы уверены, что хотите подтвердить пользователя?',
                            'data-pjax'=>0,
                            'data-method'=>'post',
                        ]) : '';
                    },

                    'ban' => function ($url, $model, $key) {
                        return $model->status == User::STATUS_ACTIVE ? Html::a('<i class="glyphicon glyphicon-remove"></i>', ['ban', 'id'=>$model->id], [
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
