<?php

use common\models\User;
use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\Factory;

$this->title = 'Представитель: '.$model->name;

?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Список', ['agents'],['class'=>'btn btn-primary']) ?>
        <?= $model->status == User::STATUS_VERIFY ? Html::a('Верифицировать', ['accept', 'id' => $model->id], [
            'class' => 'btn btn-success',
            'data' => [
                'confirm' => 'Вы уверены, что хотите подтвердить представителя?',
                'method' => 'post',
            ],
        ]) :
        Html::a('Забанить', ['ban', 'id' => $model->id], [
            'class' => 'btn btn-warning',
            'data' => [
                'confirm' => 'Вы уверены, что хотите забанить представителя?',
                'method' => 'post',
            ],
        ]); ?>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], [
            'class' => 'btn btn-info',
        ]) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы уверены, что хотите удалить представителя?',
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
            'email:email',
            [
                'attribute' => 'agent.factory.title',
                'value'=>
                    Factory::find()->where(['id' => $model->agent->factory_id])->exists() ?
                        Html::a($model->agent->factory->title,['/factory/view','id'=>$model->agent->factory_id]) :
                        $model->agent->factory_id
                ,
                'format'=>'html',
            ],
            [
                'attribute'=>'status',
                'value'=> $model->status == User::STATUS_VERIFY ? 'Ожидает верификацию' : 'активный',
            ],
            'phone',
            'date_reg:datetime',
            'points',
            [
                'label' => 'Ответы презентаций',
                'value'=>Html::a('Ответы',['/presentations/answer', 'Search[user.id]' => $model->id]),
                'format'=>'html',
            ],
            [
                'label' => 'Ответы анкет',
                'value'=>Html::a('Ответы',['/surveys/answer', 'Search[user.id]' => $model->id]),
                'format'=>'html',
            ],
            [
                'label' => 'Ответы акций',
                'value'=>Html::a('Ответы',['/stocks/answer', 'Search[user.id]' => $model->id]),
                'format'=>'html',
            ],
            'details'
        ],
    ]) ?>

</div>
