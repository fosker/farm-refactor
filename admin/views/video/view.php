<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\components\YoutubeWidget;

$this->title = $model->title;
?>
<div class="video-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Список', ['index'],['class'=>'btn btn-info']) ?>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Удалить видео и все комментарии?',
                'method' => 'post',
            ],
        ]) ?>
    </p>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'title',
            [
                'attribute' => 'link',
                'format' => 'raw',
                'value' =>  Html::tag('div', YoutubeWidget::widget([
                    "code"=> substr(strstr($model->link, 'v='), 2, 11)
                ]), ['class' => 'video-container'])
            ],
            'description:html',
            'tags'
        ],
    ]) ?>

    <div class="container">
        <h2 class="text-center">Комментарии</h2>
        </br>
            <?php foreach($model->comments as $comment) : ?>
                <div class="row">
                    <div class="col-md-6 col-md-offset-3">
                        <h4 class="text-center"><?=$comment->user->name?></h4>
                        <p class="text-center"><?=$comment->comment?></p>
                        <h6 class="text-center"><?=$comment->date_add?></h6>
                        </br>
                    </div>
                </div>
            <?php endforeach;?>
    </div>

</div>
