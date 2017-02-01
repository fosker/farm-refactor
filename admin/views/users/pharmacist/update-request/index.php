<?php

use common\models\User;
use yii\helpers\Html;
use yii\grid\GridView;
use kartik\date\DatePicker;

$this->title = 'Фармацевты, ожидающие подтверждение обновления';
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'label' => 'ID пользователя',
                'attribute' => 'pharmacist_id',
            ],
            [
                'label' => 'Имя пользователя',
                'attribute' => 'name',
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
                'contentOptions'=>['style'=>'width: 250px;'],
            ],
            [
                'attribute' => 'user.inList',
                'value' => function ($model) {
                    return [0 => 'в нейтральном', 1 => 'в черном', 2 => 'в белом', 3 => 'в сером'][$model->user->inList];
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template'=>'{accept} {view} {delete}',
                'buttons'=>[
                    'accept' => function ($url, $model, $key) {
                        return Html::a('<i class="glyphicon glyphicon-ok"></i>', ['user/update', 'id' => $model->pharmacist_id, 'update_id'=>$model->pharmacist_id]);
                    },
                    'view' => function ($url, $model, $key) {
                        return Html::a('<i class="glyphicon glyphicon-eye-open"></i>', ['view', 'user_id'=>$model->pharmacist_id]);
                    },
                    'delete' => function ($url, $model, $key) {
                        return Html::a('<i class="glyphicon glyphicon-trash"></i>', ['delete', 'user_id'=>$model->pharmacist_id], [
                            'title'=>'Удалить',
                            'data-confirm' => 'Вы уверены, что хотите удалить запрос на обновление фармацевта?',
                            'data-pjax'=>0,
                            'data-method'=>'post',
                        ]);
                    },
                ]
            ],
        ],
    ]); ?>

</div>
