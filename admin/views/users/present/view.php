<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\shop\Present */

$this->title = $model->user->name .' приобрел "'.$model->item->title.'"';
?>
<div class="present-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Список', ['index'],['class'=>'btn btn-info']) ?>
        <?= $model->promo ? Html::a('Использовать', ['use', 'id' => $model->id], ['class' => 'btn btn-warning']) : ''; ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Удалить подарок?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute'=>'user.name',
                'value'=>Html::a($model->user->name,['/user/view','id'=>$model->user_id]),
                'format'=>'html',
                'label'=>'Покупатель',
            ],
            [
                'value'=>Html::a($model->item->title,['/present/view','id'=>$model->item_id]),
                'format'=>'html',
                'label'=>'Подарок',
            ],'count',
            'promo',
            'date_buy:datetime',
            'comment'
        ],
    ]) ?>

</div>
