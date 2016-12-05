<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Фарм. компании';
?>
<div class="pharm-company-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php  echo $this->render('_search', ['model' => $searchModel, 'admins' => $admins]); ?>

    <p>
        <?= Html::a('Добавить фарм. компанию', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [

            'id',
            'name',
            [
                'label' => 'Кто добавил',
                'attribute' => 'admin.name'
            ],
            'type',
            'location',
            'size',
            'rx_otc',
            'first_visit',
            'planned_visit',

            [
                'class' => 'yii\grid\ActionColumn',
                'template'=> '{view}{delete}{update} ',
                'buttons'=> [
                    'delete' => function ($url, $model, $key) {
                        if($model->admin_id == Yii::$app->admin->id) {
                            return Html::a('<i class="glyphicon glyphicon-trash"></i>', ['delete', 'id'=>$model->id], [
                                'title' => 'Удалить',
                                'data-confirm' => 'Вы уверены, что хотите удалить фарм. компанию?',
                                'data-method'=>'post',
                            ]);
                        }
                    },
                    'update' => function ($url, $model, $key) {
                        if($model->admin_id == Yii::$app->admin->id) {
                            return Html::a('<i class="glyphicon glyphicon-pencil"></i>', ['update', 'id'=>$model->id], [
                                'title' => 'Изменить',
                            ]);
                        }
                    },
                ]
            ],
        ],
    ]); ?>
</div>