<?php

use yii\helpers\Html;
use yii\widgets\DetailView;


$this->title = $model->user->name.' ответил "'.$model->theme->title.'"';

?>
<div class="sign-up-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Список', ['index'],['class'=>'btn btn-info']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Удалить ответ?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'date_added:datetime',
            [
                'attribute'=>'user.name',
                'value'=>Html::a($model->user->name, ['/user/view', 'id'=>$model->user_id]),
                'format'=>'html',
            ],
            [
                'label' => 'Тема',
                'attribute'=>'theme.title',
                'value'=>Html::a($model->theme->title, ['/theme/view', 'id'=>$model->theme_id]),
                'format'=>'html',
            ],
            [
                'attribute'=>'photo',
                'value'=>Html::img($model->imagePath, ['alt' => 'Фото']),
                'format'=>'html',
            ],
            'text:html'
        ],
    ]) ?>

</div>
