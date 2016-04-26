<?php

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Поставщики';
?>
<div class="vendor-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить поставщика', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'id',
                'contentOptions'=>['style'=>'width: 150px;'],
            ],
            'name',
            'email:email',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
