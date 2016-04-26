<?php

use common\models\User;
use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = 'Пользователь: '.$model->name;

?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Список', ['index'],['class'=>'btn btn-primary']) ?>
        <?= $model->status == User::STATUS_VERIFY ? Html::a('Верифицировать', ['accept', 'id' => $model->id], [
            'class' => 'btn btn-success',
            'data' => [
                'confirm' => 'Вы уверены, что хотите подтвердить пользователя?',
                'method' => 'post',
            ],
        ]) :
        Html::a('Забанить', ['ban', 'id' => $model->id], [
            'class' => 'btn btn-warning',
            'data' => [
                'confirm' => 'Вы уверены, что хотите забанить пользователя?',
                'method' => 'post',
            ],
        ]); ?>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], [
            'class' => 'btn btn-info',
        ]) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы уверены, что хотите удалить пользователя?',
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
                'value'=>$model->sex == User::SEX_MALE ? 'мужской' : 'женский',
            ],
            'email:email',
            'education.name',
            [
                'attribute' => 'pharmacy.city.name',
                'value'=>Html::a($model->city->name,['/city/view','id'=>$model->city->id]),
                'format'=>'html',
            ],
            'pharmacy.city.region.name',
            [
                'attribute' => 'pharmacy.firm.name',
                'value'=>Html::a($model->firm->name,['/firm/view','id'=>$model->firm->id]),
                'format'=>'html',
            ],
            [
                'attribute'=>'pharmacy.name',
                'value'=>Html::a($model->pharmacy->name,['/pharmacy/view','id'=>$model->pharmacy_id]),
                'format'=>'html',
            ],
            'position.name',
            'phone',
            'mail_address',
            [
                'attribute'=>'status',
                'value'=> $model->status == User::STATUS_VERIFY ? 'Ожидает верификацию' : 'активный',
            ],
            'date_reg:datetime',
            'points',
            [
                'label' => 'Комментарии страниц',
                'value'=>Html::a('Комментарии',['/blocks/comment', 'Search[user.id]' => $model->id]),
                'format'=>'html',
            ],
            [
                'label' => 'Оценки страниц',
                'value'=>Html::a('Оценки',['/blocks/mark', 'Search[user.id]' => $model->id]),
                'format'=>'html',
            ],
            [
                'label' => 'Комментарии презентаций',
                'value'=>Html::a('Комментарии',['/presentations/comment', 'Search[user.id]' => $model->id]),
                'format'=>'html',
            ],
            [
                'label' => 'Ответы презентаций',
                'value'=>Html::a('Ответы',['/presentations/answer', 'Search[user.id]' => $model->id]),
                'format'=>'html',
            ],
            [
                'label' => 'Комментарии семинаров',
                'value'=>Html::a('Комментарии',['/seminars/comment', 'Search[user.id]' => $model->id]),
                'format'=>'html',
            ],
            [
                'label' => 'Записи на семинары',
                'value'=>Html::a('Записи',['/seminars/sign', 'Search[user.id]' => $model->id]),
                'format'=>'html',
            ],
            [
                'label' => 'Ответы анкет',
                'value'=>Html::a('Ответы',['/surveys/answer', 'Search[user.id]' => $model->id]),
                'format'=>'html',
            ],
            [
                'label' => 'Ответы акций',
                'value'=>Html::a('Ответы',['/factories/stocks/answer', 'Search[user.id]' => $model->id]),
                'format'=>'html',
            ],
            [
                'label' => 'Подарки',
                'value'=>Html::a('Подарки',['/users/present', 'Search[user.id]' => $model->id]),
                'format'=>'html',
            ],
            'details'
        ],
    ]) ?>

</div>
