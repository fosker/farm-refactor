<?php

use common\models\User;
use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = 'Представитель: '.$model->name;

?>
<div class="update-request-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Список', ['index'], ['class'=>'btn btn-primary']) ?>
        <?= Html::a('Обновить', ['user/update', 'id' => $model->agent_id, 'update_id'=>$model->agent_id], ['class'=>'btn btn-success']);?>
        <?= Html::a('Удалить', ['delete', 'user_id' => $model->agent_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы уверены, что хотите удалить запрос на обновление представителя?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'agent_id',
            'name',
            'email:email',
            'phone',
            [
                'attribute'=>'factory.title',
                'value'=>Html::a($model->factory->title,['/factory/view','id'=>$model->factory_id]),
                'format'=>'html',
            ],
            [
                'attribute'=>'user.inList',
                'value'=> $model->user->comment ? $model->user->lists . " (".$model->user->comment.")" : $model->user->lists
            ],
            'details',
            'date_add:datetime'

        ],
    ]) ?>

</div>
