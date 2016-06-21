<?php

use app\models\User;
use yii\helpers\Html;
use yii\widgets\DetailView;
use app\components\YoutubeWidget;

$this->title = 'Пользователь: '.$model->name;

?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update-profile'], [
            'class' => 'btn btn-info',
        ]) ?>

        <?= Html::a('Сменить пароль', ['update-password'], [
            'class' => 'btn btn-info',
        ]) ?>

        <?= Html::a('Сменить аватар', ['update-avatar'], [
            'class' => 'btn btn-info',
        ]) ?>

    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute'=>'avatar',
                'value'=>Html::img($model->avatarPath, ['alt' => 'Аватар', 'class' => 'img-responsive']),
                'format'=>'html',
            ],
            'login',
            'name',
            [
                'attribute'=>'sex',
                'value'=>$model->sex == User::SEX_MALE ? 'мужской' : 'женский',
            ],
            'email',
            'education.name',
            [
                'attribute' => 'pharmacy.city.name',
                'value'=> $model->city->name,
            ],
            'pharmacy.city.region.name',
            [
                'attribute' => 'pharmacy.firm.name',
                'value'=> $model->firm->name,
            ],
            [
                'attribute'=>'pharmacy.name',
                'value'=> $model->pharmacy->name,

            ],
            'position.name',
            'phone',
            'mail_address',
            'date_reg:datetime',
            'points',
        ],
    ]) ?>

</div>
