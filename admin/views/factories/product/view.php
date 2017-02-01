<?php

use yii\helpers\Html;
use yii\widgets\DetailView;


$this->title = $model->title;

?>
<div class="product-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Список', ['index'],['class'=>'btn btn-info']) ?>
        <?=Html::a($model->status == $model::STATUS_HIDDEN ? 'Утвердить' : 'Скрыть', [$model->status == $model::STATUS_HIDDEN ? 'approve' : 'hide' , 'id' => $model->id], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Удалить продукт?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute'=>'thumbnail',
                'value'=>Html::img($model->thumbPath, ['alt' => 'Превью', 'width' => '50%', 'height' => '200px']),
                'format'=>'html',
            ],
            [
                'attribute'=>'image',
                'value'=>Html::img($model->imagePath, ['alt' => 'Изображение', 'width' => '50%', 'height' => '200px']),
                'format'=>'html',
            ],
            'title',
            'priority',
            [
                'attribute'=>'factory_id',
                'value'=>Html::a($model->factory->title,['/factory/view','id'=>$model->factory_id]),
                'format'=>'html',
            ],
            'description:html',
            [
                'attribute'=>'status',
                'value'=>$model::getStatusList()[$model->status],
            ],
        ],
    ]) ?>

</div>
