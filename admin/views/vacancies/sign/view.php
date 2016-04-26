<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\seminar\SignUp */

$this->title = $model->user->name.' подал заявку на "'.$model->vacancy->title.'"';

?>
<div class="sign-up-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Список', ['index'],['class'=>'btn btn-info']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Удалить заявку?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute'=>'user.name',
                'value'=>Html::a($model->user->name, ['/user/view', 'id'=>$model->user_id]),
                'format'=>'html',
            ],
            [
                'attribute'=>'vacancy.title',
                'value'=>Html::a($model->vacancy->title, ['/vacancy/view', 'id'=>$model->vacancy_id]),
                'format'=>'html',
            ],
            'contact',
            'date_add:text',
        ],
    ]) ?>

</div>
