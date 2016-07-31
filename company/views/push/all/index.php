<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\date\DatePicker;

$this->title = 'Оповещения';
?>
<div class="pushes-index">

    <h1><?= Html::encode($this->title) ?></h1>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'id',
                'contentOptions'=>['style'=>'width: 100px;'],
            ],
            [
                'attribute'=>'link',
                'filter'=>$links,
                'value'=>function($model) {
                    return $model->linkTitleHref;
                },
                'format' => 'html',
                'contentOptions'=>['style'=>'width: 200px;'],
            ],
            'message',
            [
                'attribute' => 'device_count',
                'contentOptions'=>['style'=>'width: 80px;'],
            ],
            [
                'attribute' => 'views',
                'contentOptions'=>['style'=>'width: 80px;'],
            ],
            [
                'attribute' => 'date_send',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'date_send',
                    'pluginOptions' => [
                        'format' => 'yyyy-mm-dd',
                    ]
                ]),
                'format' => ['datetime'],
                'contentOptions'=>['style'=>'width: 200px;'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template'=>'{view}{delete}',
            ],
        ],
    ]); ?>

</div>
