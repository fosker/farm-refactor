<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = "Пользователь " . $model->user->name . " прислал вам сообщение. ";
?>
<div class="contact-form-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Список', ['index'], ['class' => 'btn btn-info']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Удалить сообщение?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'label' => 'Пользователь',
                'attribute'=>'user.name',
                'value'=>Html::a($model->user->name,['/user/view','id'=>$model->user_id]),
                'format'=>'html',
            ],
            'subject',
            'message',
            'date:datetime',
        ],
    ]) ?>

</div>
