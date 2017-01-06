<?php

use yii\helpers\Html;
use yii\widgets\DetailView;


$this->title = $model->title;
?>
<div class="theme-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Список', ['index'],['class'=>'btn btn-info']) ?>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Удалить тему?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'title',
            [
                'label' => 'Компания Автор',
                'attribute' => 'factory.title',
                'value'=>Html::a($model->factory->title,['/factory/view','id'=>$model->factory_id]),
                'format'=>'html',
            ],
            'email',
            'description:html',
            [
                'label' => 'Тип темы',
                'attribute' => 'form_id',
                'value'=> $model->form_id == 0 ? 'Свободная тема' : $model->form->title,
                'format'=>'html',
            ],

        ],
    ]) ?>

</div>
