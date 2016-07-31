<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\widgets\Select2;

$this->title = 'Формы';
?>
<div class="form-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить форму', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'id',
                'contentOptions'=>['style'=>'width: 150px;'],
            ],
            [
                'attribute' => 'title',
                'value'=>'title',
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
