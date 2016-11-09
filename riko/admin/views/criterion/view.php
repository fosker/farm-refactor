<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = $model->name;
?>
<div class="criterion-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы действительно хотите удалить критерий?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'description:ntext',
            'abbr',
            [
                'attribute'=>'type',
                'value' => $model->types
            ],
            'min',
            'max',
            'step'
        ],
    ]) ?>

</div>
