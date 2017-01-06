<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\widgets\Select2;
use kartik\widgets\DatePicker;

$this->title = 'Аптеки';
$this->registerJsFile('js/show-comment.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
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
                'contentOptions'=>['style'=>'width: 200px;'],
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
                    'options' => ['placeholder' => 'Выберите организацию ...'],
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
            [
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'date_from',
                    'attribute2' => 'date_to',
                    'type' => DatePicker::TYPE_RANGE,
                    'separator' => '-',
                    'pluginOptions' => ['format' => 'yyyy-mm-dd']
                ]),
                'attribute' => 'date_visit',
                'format' => 'date',
                'contentOptions' => function($model) {
                    return [
                        'class' => 'list-comment',
                        'title' => $model->comment,
                    ];
                }
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
