<?php

use common\models\User;
use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = 'Фармацевт: '.$model->name;

?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Список', ['pharmacists'],['class'=>'btn btn-primary']) ?>
        <?= $model->status == User::STATUS_VERIFY ? Html::a('Верифицировать', ['accept', 'id' => $model->id], [
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

        <?php

        if($model->inList == User::IN_BLACK || $model->inList == User::IN_WHITE || $model->inList == User::IN_GRAY) {
            echo Html::a('Убрать из списка', ['neutral', 'id' => $model->id], [
                'class' => 'btn btn-success',
                'data' => [
                    'confirm' => 'Вы уверены, что хотите добавить пользователя в нейтральный список?',
                    'method' => 'post',
                ]
            ]);
        }
        if($model->inList != User::IN_BLACK) {
            echo Html::a('В черный список', ['black', 'id' => $model->id], [
                'class' => 'btn btn-warning',
            ]);
        }
        if($model->inList != User::IN_WHITE) {
            echo Html::a('В белый список', ['white', 'id' => $model->id], [
                'class' => 'btn btn-warning',
            ]);
        }
        if($model->inList != User::IN_GRAY) {
            echo Html::a('В серый список', ['gray', 'id' => $model->id], [
                'class' => 'btn btn-warning',
            ]);
        }
        ?>

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
    <p>
        <?= Html::a('Управление Bon', ['edit-bon', 'id' => $model->id], [
            'class' => 'btn btn-info',
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
            'pharmacist.date_birth:date',
            [
                'attribute'=>'pharmacist.sex',
                'value'=>$model->pharmacist->sex == User::SEX_MALE ? 'мужской' : 'женский',
            ],
            'email:email',
            'pharmacist.education.name',
            [
                'label' => 'Город',
                'attribute' => 'pharmacist.city.name',
                'value'=>Html::a($model->pharmacist->city->name,['/city/view','id'=>$model->pharmacist->city->id]),
                'format'=>'html',
            ],
            [
                'label' => 'Регион',
                'attribute' => 'pharmacist.region.name',
            ],
            [
                'label' => 'Организация',
                'attribute' => 'pharmacist.company.title',
                'value'=>Html::a($model->pharmacist->company->title,['/company/view','id'=>$model->pharmacist->company->id]),
                'format'=>'html',
            ],
            [
                'attribute'=>'pharmacist.pharmacy.name',
                'value'=>Html::a($model->pharmacist->pharmacy->name,['/pharmacy/view','id'=>$model->pharmacist->pharmacy_id]),
                'format'=>'html',
            ],
            'pharmacist.position.name',
            'phone',
            'pharmacist.mail_address',
            [
                'attribute'=>'status',
                'value'=> $model->statuses
            ],
            [
                'attribute'=>'inList',
                'value'=> $model->comment ? $model->lists . " ($model->comment)" : $model->lists
            ],
            'date_reg:datetime',
            'points',
            [
                'label' => 'Ответы презентаций',
                'value'=>Html::a('Ответы',['/presentations/answer', 'Search[user.id]' => $model->id]),
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
                'value'=>Html::a('Ответы',['/stocks/answer', 'Search[user.id]' => $model->id]),
                'format'=>'html',
            ],
            [
                'label' => 'Записи на вакансии',
                'value'=>Html::a('Записи',['/vacancies/sign', 'Search[user.id]' => $model->id]),
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
