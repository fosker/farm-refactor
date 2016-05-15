<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\widgets\Select2;

$this->title = 'Аптеки';

?>
<div class="pharmacy-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить аптеку', ['create'], ['class' => 'btn btn-success']) ?>
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
                'attribute' => 'name',
                'value'=>'name',
                'filter'=>Select2::widget([
                    'model' => $searchModel,
                    'data' => $names,
                    'attribute'=>'name',
                    'options' => [
                        'placeholder' => 'Выберите аптеку ...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'width' => '200px'
                    ],
                ]),
            ],
            [
                'attribute' => 'address',
                'value' => 'address',
                'contentOptions'=>['style'=>'width: 300px;'],
            ],
            [
                'attribute' => 'company_id',
                'value'=>function($model) {
                    return Html::a($model->company->title, ['/company/view', 'id' => $model->company_id]);
                },
                'format'=>'html',
                'filter' => Select2::widget([
                    'model' => $searchModel,
                    'attribute' => 'company_id',
                    'data' => $companies,
                    'options' => ['placeholder' => 'Выберите компанию ...'],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'width' => '200px'
                    ],
                ]),
            ],
            [
                'attribute' => 'city_id',
                'value' => 'city.name',
                'filter' => Select2::widget([
                    'model' => $searchModel,
                    'attribute' => 'city_id',
                    'data' => $cities,
                    'options' => ['placeholder' => 'Выберите город ...'],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'width' => '200px'
                    ],
                ]),
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
