<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\select2\Select2;

$this->title = 'Сотрудники';
?>
<div class="employee-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a('Создать сотрудника', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'name',
            'surname',
            'phone',
            [
                'attribute'=>'department_id',
                'value'=>'department.name',
                'filter'=>Select2::widget([
                    'model' => $searchModel,
                    'data' => $departments,
                    'attribute'=>'department_id',
                    'options' => [
                        'placeholder' => 'Выберите отдел ...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ])
            ],
            [
                'attribute'=>'position_id',
                'value'=>'position.name',
                'filter'=>Select2::widget([
                    'model' => $searchModel,
                    'data' => $positions,
                    'attribute'=>'position_id',
                    'options' => [
                        'placeholder' => 'Выберите должность ...',
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
