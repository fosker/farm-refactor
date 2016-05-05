<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\seminar\SignUp */

$this->title = $model->user->name.' записался на "'.$model->seminar->title.'"';

?>
<div class="sign-up-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Список', ['index'],['class'=>'btn btn-info']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Удалить запись?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'date_add:datetime',
            [
                'attribute'=>'user.name',
                'value'=>Html::a($model->user->name, ['/user/view', 'id'=>$model->user_id]),
                'format'=>'html',
            ],
            [
                'attribute'=>'seminar.title',
                'value'=>Html::a($model->seminar->title, ['/seminar/view', 'id'=>$model->seminar_id]),
                'format'=>'html',
            ],
            'contact',
        ],
    ]) ?>

</div>
