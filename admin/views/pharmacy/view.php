<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = $model->name;
?>
<div class="pharmacy-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Список', ['index'],['class'=>'btn btn-info']) ?>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Удалить аптеку?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'address',
            [
                'attribute'=>'firm.name',
                'value'=>Html::a($model->firm->name,['/firm/view','id'=>$model->firm_id]),
                'format'=>'html',
            ],
            [
                'attribute'=>'city.name',
                'value'=>Html::a($model->city->name,['/city/view','id'=>$model->city_id]),
                'format'=>'html',
            ],
        ],
    ]) ?>

</div>
