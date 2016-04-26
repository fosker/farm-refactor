<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\block\Comment */

$this->title = $model->user->name.' прокомментировал "'.$model->block->title.'"';
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
                'value'=>Html::a($model->user->name, ['/user/view', 'id'=>$model->user_id]),
                'format'=>'html',
                'label'=>'Автор',
            ],
            'comment:ntext',
            [
                'attribute'=>'block.title',
                'value'=>Html::a($model->block->title, ['/block/view', 'id'=>$model->block_id]),
                'format'=>'html',
                'label'=>'Страница',
            ],
            'date_add:datetime',
        ],
    ]) ?>

</div>
