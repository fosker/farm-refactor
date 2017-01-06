<?php

use common\models\factory\Admin;
use yii\helpers\Html;
use yii\grid\GridView;
use kartik\widgets\Select2;

$this->title = 'Администраторы производителей';
?>
<div class="factory-admin-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Создать администратора', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

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
                        'width' => '250px'
                    ],
                ]),
            ],
            [
                'label' => 'Организация',
                'attribute' => 'company_id',
                'value' => 'company.title',
                'format' => 'html',
                'filter' => Select2::widget([
                    'model' => $searchModel,
                    'data' => $companies,
                    'attribute'=>'company_id',
                    'options' => [
                        'placeholder' => 'Выберите компанию ...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'width' => '200px'
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
                        'width' => '200px'
                    ],
                ]),
            ],
            [
                'attribute'=>'status',
                'value' => function($model) {
                    return $model->status == Admin::STATUS_ACTIVE ? 'активен' : 'ожидает';
                },
                'filter'=>[Admin::STATUS_ACTIVE=>'активен',Admin::STATUS_VERIFY=>'ожидает'],
                'contentOptions'=>['style'=>'width: 250px;'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template'=>'{ban} {accept} {view} {delete} {update}',
                'buttons'=>[
                    'accept' => function ($url, $model, $key) {
                        return $model->status == Admin::STATUS_VERIFY ? Html::a('<i class="glyphicon glyphicon-ok"></i>', ['accept', 'id'=>$model->id], [
                            'title'=>'Утвердить',
                            'data-confirm' => 'Вы уверены, что хотите подтвердить пользователя?',
                            'data-pjax'=>0,
                            'data-method'=>'post',
                        ]) : '';
                    },

                    'ban' => function ($url, $model, $key) {
                        return $model->status == Admin::STATUS_ACTIVE ? Html::a('<i class="glyphicon glyphicon-remove"></i>', ['ban', 'id'=>$model->id], [
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
