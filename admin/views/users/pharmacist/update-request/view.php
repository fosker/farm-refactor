<?php

use common\models\User;
use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = 'Фармацевт: '.$model->name;

?>
<div class="update-request-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Список', ['index'], ['class'=>'btn btn-primary']) ?>
        <?= Html::a('Обновить', ['user/update', 'id' => $model->pharmacist_id, 'update_id'=>$model->pharmacist_id], ['class'=>'btn btn-success']);?>
        <?= Html::a('Удалить', ['delete', 'user_id' => $model->pharmacist_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы уверены, что хотите удалить запрос на обновление фармацевта?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'pharmacist_id',
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
            [
                'attribute'=>'user.inList',
                'value'=> $model->user->comment ? $model->user->lists . " (".$model->user->comment.")" : $model->user->lists
            ],
            'position.name',
            'details',
            'date_add:datetime'
        ],
    ]) ?>

</div>
