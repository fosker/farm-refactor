<?php

use common\models\factory\Admin;
use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = 'Администратор: '.$model->name;

?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Список', ['index'],['class'=>'btn btn-primary']) ?>
        <?= $model->status == Admin::STATUS_VERIFY ? Html::a('Верифицировать', ['accept', 'id' => $model->id], [
            'class' => 'btn btn-success',
            'data' => [
                'confirm' => 'Вы уверены, что хотите подтвердить фармацевта?',
                'method' => 'post',
            ],
        ]) :
            Html::a('Забанить', ['ban', 'id' => $model->id], [
                'class' => 'btn btn-warning',
                'data' => [
                    'confirm' => 'Вы уверены, что хотите забанить фармацевта?',
                    'method' => 'post',
                ],
            ]); ?>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], [
            'class' => 'btn btn-info',
        ]) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы уверены, что хотите удалить фармацевта?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute'=>'avatar',
                'value'=>Html::img($model->avatarPath, ['alt' => 'Аватар']),
                'format'=>'html',
            ],
            'login',
            'name',
            [
                'attribute'=>'sex',
                'value'=>$model->sex == Admin::SEX_MALE ? 'мужской' : 'женский',
            ],
            'email:email',
            [
                'label' => 'Производитель',
                'attribute' => 'factory.title',
                'value'=>Html::a($model->factory->title,['/factory/view','id'=>$model->factory_id]),
                'format'=>'html',
            ],
        ],
    ]) ?>

</div>
