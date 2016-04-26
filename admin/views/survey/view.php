<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = $model->title;
?>
<div class="survey-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Список', ['index'],['class'=>'btn btn-info']) ?>
        <?= Html::a('Ответы ('.$model->answersCount.')', ['surveys/answer/index', 'Search[survey.id]' => $model->id], ['class' => 'btn btn-warning']) ?>
        <?= Html::a('Экспорт pdf', ['surveys/answer/export-pdf', 'survey_id' => $model->id], ['class' => 'btn btn-danger'.($model->answersCount == 0 ? ' disabled':''),'target'=>'_blank']) ?>
        <?= Html::a('Экспорт xls', ['surveys/answer/export-xls', 'survey_id' => $model->id], ['class' => 'btn btn-success'.($model->answersCount == 0 ? ' disabled':''),'target'=>'_blank']) ?>


        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'pull-right btn btn-danger',
            'data' => [
                'confirm' => 'Удалить анкету и все ответы?',
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a(
            'Редактировать',
            ['update', 'id' => $model->id],
            ['class' => 'pull-right btn btn-primary'])
        ?>
        <?= Html::a(
            $model->status == $model::STATUS_HIDDEN ? 'Утвердить' : 'Скрыть',
            [$model->status == $model::STATUS_HIDDEN ? 'approve' : 'hide' , 'id' => $model->id],
            ['class' => 'pull-right btn btn-success'])
        ?>
    </p>

    <h2>Информация об анкете</h2>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'label' => 'Превью',
                'attribute'=>'thumbnail',
                'value'=>Html::img($model->thumbPath, ['alt' => 'Превью', 'width' => '50%', 'height' => '200px']),
                'format'=>'html',
            ],
            [
                'label' => 'Изображение',
                'attribute'=>'image',
                'value'=>Html::img($model->imagePath, ['alt' => 'Изображение', 'width' => '50%', 'height' => '200px']),
                'format'=>'html',
            ],
            'title',
            'description:html',
            [
                'label'=>'Для городов',
                'value'=>$model->getCitiesView(true)
            ],
            [
                'label'=>'Для фирм',
                'value'=>$model->getFirmsView(true)
            ],
            [
                'label'=>'Для групп',
                'value'=>$model->getEducationsView(true)
            ],
            'points',
            [
                'attribute'=>'status',
                'value'=>$model::getStatusList()[$model->status],
            ],
        ],
    ]) ?>

    <h2>Вопросы</h2>

    <table class="table">
        <tr><th>Вопрос</th><th>Варианты ответа</th><th>Количество правильных ответов</th></tr>
        <?php foreach($model->questions as $question) : ?>
            <tr><td><?=$question->question?></td>
                <td>
                <table>
                    <?php foreach($question->options as $option) :?>
                        <tr><td><p class="text-info"><?=$option->value?></p></td></tr>
                <?php endforeach; ?>
                </table>
                </td>
                <td><?=$question->right_answers?></td>
            </tr>
        <?php endforeach; ?>
    </table>

</div>
