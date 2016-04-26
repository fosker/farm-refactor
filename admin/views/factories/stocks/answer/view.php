<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\widgets\Select2;

?>
<div class="reply-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Список', ['index'],['class'=>'btn btn-info']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Удалить ответ?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute'=>'photo',
                'value'=>Html::img($model->imagePath, ['alt' => 'Изображение', 'width' => '50%', 'height' => '350px']),
                'format'=>'html',
            ],
            [
                'attribute'=>'user.name',
                'value'=>Html::a($model->user->name, ['/user/view', 'id'=>$model->user_id]),
                'format'=>'html',
            ],
            [
                'attribute'=>'stock.title',
                'value'=>Html::a($model->stock->title, ['/factories/stock/view', 'id'=>$model->stock_id]),
                'format'=>'html',
            ],
            'date_add:datetime'
        ],
    ]) ?>

</div>
