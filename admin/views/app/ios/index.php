<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\app\Ios */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Версии IOS';
?>
<div class="ios-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Создать версию', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            'id',
            'version',
            [
                'attribute' => 'is_forced',
                'value' => function ($model) {
                    return $model->is_forced ? 'да' : 'нет';
                },
                'filter' => [0 => 'нет', 1 => 'да'],
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
