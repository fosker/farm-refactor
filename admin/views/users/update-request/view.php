<?php

use common\models\User;
use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = 'Пользователь: '.$model->name;

?>
<div class="update-request-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Список', ['index'], ['class'=>'btn btn-primary']) ?>
        <?= Html::a('Обновить', ['user/update', 'id' => $model->user_id, 'update_id'=>$model->user_id], ['class'=>'btn btn-success']);?>
        <?= Html::a('Удалить', ['delete', 'user_id' => $model->user_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы уверены, что хотите удалить запрос на обновление пользователя?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'user_id',
            'name',
            [
                'attribute'=>'sex',
                'value'=>$model->sex == User::SEX_MALE ? 'мужской' : 'женский',
            ],
            'email:email',
            'phone',
            'mail_address',
            'education.name',
            [
                'attribute'=>'pharmacy.name',
                'value'=>Html::a($model->pharmacy->name,['/pharmacy/view','id'=>$model->pharmacy_id]),
                'format'=>'html',
            ],
            'position.name',
            'details',
            'date_add:datetime'

        ],
    ]) ?>

</div>
