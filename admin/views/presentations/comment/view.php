<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = $model->user->name.' прокомментировал "'.$model->presentation->title.'"';
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
                'attribute'=>'user.name',
                'label'=>'Автор',
                'value'=>Html::a($model->user->name, ['/user/view', 'id'=>$model->user_id]),
                'format'=>'html',
            ],
            'comment:html',
            [
                'attribute'=>'presentation.title',
                'value'=>Html::a($model->presentation->title, ['/presentations/view', 'id'=>$model->presentation->id]),
                'format'=>'html',
            ],
            'date_add:datetime',
        ],
    ]) ?>

</div>
