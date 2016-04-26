<?php

use yii\helpers\Html;
use yii\widgets\DetailView;


$this->title = $model->user->name.' прокомментировал "'.$model->video->title.'"';
?>
<div class="comment-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Список', ['index'],['class'=>'btn btn-info']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Удалить комментарий?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'label'=>'Автор',
                'attribute'=>'user.name',
                'value'=>Html::a($model->user->name, ['/user/view', 'id'=>$model->user_id]),
                'format'=>'html',
            ],
            'comment:text',
            [
                'label'=>'Видео',
                'attribute'=>'video.title',
                'value'=>Html::a($model->video->title, ['/video/view', 'id'=>$model->video_id]),
                'format'=>'html',
            ],
            'date_add:datetime',
        ],
    ]) ?>

</div>
