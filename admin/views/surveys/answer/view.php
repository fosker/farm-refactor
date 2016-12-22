<?php

use yii\helpers\Html;
use yii\helpers\HtmlPurifier;

/* @var $this yii\web\View */
/* @var $answers array */

$this->title = 'Ответ анкеты';
$title = Html::a($answers[0]->view->user->name,['/user/view','id'=>$answers[0]->view->user->id]).' ответил на "'.Html::a($answers[0]->question->survey->title,['/survey/view','id'=>$answers[0]->question->survey_id]).'"';
$time = $answers[0]->view->time_answer;

?>
<div class="answer-view">


    <h1><?= HtmlPurifier::process($title) ?></h1>

    <p>
        <?= Html::a('Список', ['index'], ['class' => 'btn btn-info']) ?>
        <?= Html::a('Удалить', ['delete', 'user_id'=>$answers[0]->view->user->id, 'survey_id'=>$answers[0]->question->survey_id], [
            'title'=>'удалить',
            'data-pjax'=>0,
            'data-method'=>'post',
            'data-confirm'=>'Удалить ответ?',
            'class'=>'btn btn-danger',
        ]) ?>
    </p>

    <p>Время заполнения: <?= gmdate("H:i:s", $time); ?></p>

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
