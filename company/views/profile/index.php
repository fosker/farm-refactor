<?php
use kartik\detail\DetailView;
use yii\helpers\Html;

$this->title = 'Мой профиль';

echo Html::a('Сменить пароль', ['update-password'], [
    'class' => 'btn btn-info',
]);

echo '</br></br>';

echo DetailView::widget([
    'model'=>$model,
    'fadeDelay'=>0,
    'condensed'=>true,
    'hover'=>true,
    'mode'=>DetailView::MODE_VIEW,
    'formOptions' => [
        'options' => ['enctype' => 'multipart/form-data']
    ],
    'panel'=>[
        'heading'=> $model->login,
        'type'=>DetailView::TYPE_PRIMARY,
    ],
    'attributes'=>[
        [
            'attribute' => 'image',
            'type' => DetailView::INPUT_FILEINPUT,
            'value' => Html::img($model->avatarPath),
            'format' => 'html',
        ],
        [
            'attribute' => 'login',
            'displayOnly' => true,
        ],
        'name',
        'email',
        [
            'label' => 'Компания',
            'attribute'=>'company_id',
            'value'=>$model->company->title,
            'displayOnly' => true,
        ],
        [
            'attribute' => 'sex',
            'type' => DetailView::INPUT_DROPDOWN_LIST,
            'value' => $model->sex == 'male' ? 'мужчина' : 'женщина',
            'items' => [
                'male' => 'мужчина',
                'female' => 'женщина'
            ],

        ],
    ],
    'buttons1' => '{update}',
    'buttons2' => '{view}{save}{reset}'
]);
