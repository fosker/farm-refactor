<?php

use yii\helpers\Html;
use yii\helpers\HtmlPurifier;


$this->title = 'Ответ презентации';
$title = Html::a($answers[0]->view->user->name,['/user/view','id'=>$answers[0]->view->user->id]).' ответил на "'.Html::a($answers[0]->view->presentation->title,['/presentation/view','id'=>$answers[0]->view->presentation->id]).'"';
?>
<div class="answer-view">


    <h1><?= HtmlPurifier::process($title) ?></h1>

    <p>
        <?= Html::a('Список', ['index'], ['class' => 'btn btn-info']) ?>
        <?= Html::a('Удалить', ['delete', 'user_id'=>$answers[0]->view->user->id, 'presentation_id'=>$answers[0]->view->presentation->id], [
            'title'=>'удалить',
            'data-pjax'=>0,
            'data-method'=>'post',
            'data-confirm'=>'Удалить ответ?',
            'class'=>'btn btn-danger',
        ]) ?>
    </p>

    <table class="table">
        <thead>
            <tr><th>Вопрос</th><th>Ответ</th></tr>
        </thead>
        <tbody>
            <?php foreach($answers as $answer) : ?>
                <tr><td><?php echo $answer->question->question;?></td><td><?php echo $answer->value;?></td></tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</div>
