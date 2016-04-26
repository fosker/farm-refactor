<?php

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Администраторы';

?>
<div class="admin-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Создать администратора', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'name',
            'email:email',

            [
                'class' => 'yii\grid\ActionColumn',
                'template'=>'{view} {update} {rights} {delete}',
                'header'=>'Действия',
                'buttons'=>[
                    'rights' => function ($url, $model, $key) {
                        return Html::a('<i class="fa fa-users"></i>', ['rights', 'id'=>$model->id], [
                            'title'=>'Права',
                            'data-pjax'=>0,
                            'data-method'=>'post',
                        ]);
                    },
                ]
            ],
        ],
    ]); ?>

</div>
