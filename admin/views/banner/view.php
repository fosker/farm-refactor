<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = $model->title;
?>
<div class="banner-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Список', ['index'],['class'=>'btn btn-info']) ?>
        <?=Html::a($model->status == $model::STATUS_HIDDEN ? 'Утвердить' : 'Скрыть', [$model->status == $model::STATUS_HIDDEN ? 'approve' : 'hide' , 'id' => $model->id], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Удалить баннер?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'title',
            [
                'attribute'=>'image',
                'value'=>Html::img($model->imagePath, ['alt' => 'Изображение', 'width' => '50%', 'height' => '200px']),
                'format'=>'html',
            ],
            [
                'label'=>'Превью',
                'value'=>'<div class="banner-preview position-'.$model->position.'" style="background: url('.$model->imagePath.') center;background-size: cover;"></div>',
                'format'=>'raw'
            ],
            [
                'attribute'=>'position',
                'value'=>$model::positions()[$model->position]
            ],
            [
                'attribute'=>'link',
                'value'=> $model->linktitleHref,
                'format'=>'html',
            ],
            [
                'label'=>'Для городов',
                'value'=>$model->getCitiesView(true)
            ],
            [
                'label'=>'Для фирм',
                'value'=>$model->getFirmsView(true)
            ],
            [
                'label'=>'Для групп',
                'value'=>$model->getEducationsView(true)
            ],
            [
                'attribute'=>'status',
                'value'=>$model::getStatusList()[$model->status],
            ],
        ],
    ]) ?>

</div>
