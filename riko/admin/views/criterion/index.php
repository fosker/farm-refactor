<?php

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Критерии';
?>
<div class="criterion-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Создать критерий', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'name',
            'abbr',
            [
                'attribute'=>'type',
                'filter' => [1 => '+', 2 => '-', 3 => '+/-'],
                'value' => function($model) {
                    return [1 => '+', 2 => '-', 3 => '+/-'][$model->type];
                },
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
