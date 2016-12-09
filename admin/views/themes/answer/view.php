<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\seminar\SignUp */

$this->title = $model->user->name.' ответил на "'.$model->theme->title.'"';

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
            'date_added:text',
            [
                'attribute'=>'user.name',
                'value'=>Html::a($model->user->name, ['/user/view', 'id'=>$model->user_id]),
                'format'=>'html',
            ],
            [
                'attribute'=>'theme.title',
                'value'=>Html::a($model->theme->title, ['/theme/view', 'id'=>$model->theme_id]),
                'format'=>'html',
            ],
            'phone',
            'email:email',
            'text',
            [
                'attribute' => 'is_answered',
                'value' => $model->is_answered ? 'да' : 'нет'
            ],
            'comment'
        ],
    ]) ?>

</div>
