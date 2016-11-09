<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\select2\Select2;

$this->title = 'Отделы';
?>
<div class="department-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a('Создать отдел', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'name',
            [
                'attribute'=>'company_id',
                'value'=>'company.name',
                'filter'=>Select2::widget([
                    'model' => $searchModel,
                    'data' => $companies,
                    'attribute'=>'company_id',
                    'options' => [
                        'placeholder' => 'Выберите компанию ...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ])
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
