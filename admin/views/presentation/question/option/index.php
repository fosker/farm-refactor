<?php

use yii\helpers\Html;

use common\models\Presentation;
use common\models\presentation\Question;
$this->title = 'Варианты ответа';
$this->params['breadcrumbs'][] = ['label' => Presentation::findOne(
    ['id' => Yii::$app->request->get('presentation_id')])->title,
    'url' => ['/presentation/view', 'id' => Yii::$app->request->get('presentation_id')]];
$this->params['breadcrumbs'][] = ['label' => Question::findOne(
    ['id' => Yii::$app->request->get('question_id')])->question];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="option-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить вариант ответа', ['add-option','question_id'=>$_GET['question_id'],
    'presentation_id' => $_GET['presentation_id']], ['class' => 'btn btn-success']) ?>
    </p>

    <table class="table">
        <tr><th>Вариант ответа</th><th>Правильный ответ</th><th>Действия</th></tr>
        <? foreach($options as $option) : ?>
            <tr>
                <td><?=$option->value?></td>
                <td><?php echo $option->isValid ? "Да" : "Нет"?></td>
                <td>

                    <?=Html::a('<span class="glyphicon glyphicon-pencil"></span>',['edit-option', 'id'=>$option->id, 'question_id'=>$_GET['question_id'] ,'presentation_id'=>$_GET['presentation_id']],['class'=>'btn btn-primary btn-xs']);?>

                    <?=Html::a('<span class="glyphicon glyphicon-trash"></span>',
                        ['delete-option', 'id'=>$option->id, 'presentation_id'=>$_GET['presentation_id']],
                        ['class'=>'btn btn-danger btn-xs',
                            'data' => [
                                'confirm' => 'Удалить вариант ответа?',
                                'method' => 'post',
                            ]
                        ]
                    );?>
                </td>
            </tr>
        <? endforeach; ?>
    </table>

</div>
