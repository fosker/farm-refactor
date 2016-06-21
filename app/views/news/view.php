<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;

$this->title = $model->title;
?>
<div class="news-view">

    <h1 class="text-center"><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Список', ['/news'],['class'=>'btn btn-info']) ?>
    </p>

    <div class="news_item">
        <h3 class="text-center"><?=$model->title?></h3>
        <h6 class="text-center" style="color:black"><?=$model->date?></h6>
        <img src="<?=$model->imagePath?>" class="img-responsive" style="width: 80%"/>
        <br/>
        <div class="news_text well">
            <?=$model->text?>
        </div>
        <div class="text-center">
            <p class="glyphicon glyphicon-eye-open"> <?=$model->countUniqueViews()?></p>
        </div>
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
