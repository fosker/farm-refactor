<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\form\ActiveForm;

$this->title = $model->title;
?>
<div class="news-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Список', ['index'],['class'=>'btn btn-info']) ?>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Удалить новость, просмотры и все комментарии?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute'=>'thumbnail',
                'value'=>Html::img($model->thumbPath, ['alt' => 'Превью']),
                'format'=>'html',
            ],
            [
                'attribute'=>'image',
                'value'=>Html::img($model->imagePath, ['alt' => 'Изображение', 'width'=>'50%', 'height'=>'200px']),
                'format'=>'html',
            ],
            'title',
            'text:html',
            [
                'label'=>'Для аптек',
                'value'=>$model->getPharmaciesView(false)
            ],
            [
                'label'=>'Для образования',
                'value'=>$model->getEducationsView(true)
            ],
            [
                'attribute'=>'views',
                'value'=>$model->countUniqueViews()
            ],
            'date:datetime',
        ],
    ]) ?>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($comment, 'comment')->textArea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton('Добавить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <h4>Комментарии</h4>
    <div class="col-md-8">
        <?php foreach ($model->comments as $comment): ?>
        <div class="row">
            <div class="col-md-1">
                <div class="row">
                    <p class="text-center"><?=$comment->user->login?></p>
                </div>
                <div class="row">
                    <?=Html::img($comment->user->avatarPath, ['class' => 'img-responsive']);?>
                </div>
            </div>
            </br>
            <div class="col-md-8" style="word-wrap: break-word;">
                <p><?=$comment->comment?></p>
                <h6><?=$comment->date_add?></h6>
            </div>
        </div>
        </br>
    <?php endforeach;?>
    </div>

</div>
