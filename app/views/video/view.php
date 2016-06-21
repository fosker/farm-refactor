<?php

use yii\helpers\Html;
use app\components\YoutubeWidget;
use kartik\widgets\ActiveForm;

$this->title = $model->title;
?>
<div class="video-view">

    <h1 class="text-center"><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Список', ['/videos'],['class'=>'btn btn-info']) ?>
    </p>

    <div>
        <h3 class="text-center"><?=$model->title?></h3>
        <div>
            <?=Html::tag('div', YoutubeWidget::widget([
                "code"=> substr($model->link,-11),
            ]), ['class' => 'video-container'])?>
        </div>
        <br/>
        <div class="video_description well">
            <?=$model->description?>
        </div>
        <h6 class="text-center">Теги: <?=$model->tags?></h6>
    </div>

        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <?php $form = ActiveForm::begin(); ?>
                <?= $form->field($comment, 'comment')->textArea(['rows' => '6']) ?>
                <?= Html::submitButton('Добавить комментарий', ['class' => 'btn btn-primary']); ?>

                <?php ActiveForm::end(); ?>
            </div>
        </div>

        </br>
        <?php foreach($model->comments as $comment) : ?>
            <div class="row">
                <div class="col-md-6 col-md-offset-3">
                    <h4 class="text-center"><?=$comment->user->name?></h4>
                    <p class="text-center"><?=$comment->comment?></p>
                    <h6 class="text-center"><?=$comment->date_add?></h6>
                    </br>
                </div>
            </div>
        <?php endforeach;?>

</div>
