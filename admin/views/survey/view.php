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
        <?= Html::a('Экспорт xls', ['surveys/answer/export-xls', 'survey_id' => $model->id], ['class' => 'btn btn-danger'.($model->answersCount == 0 ? ' disabled':''),'target'=>'_blank']) ?>
    <div class="row">
        <?= Html::a('Экспорт статистики (по регионам)', ['export-regions', 'id' => $model->id], ['class' => 'btn btn-danger'.($model->answersCount == 0 ? ' disabled':''),'target'=>'_blank']) ?>
        <?= Html::a('Экспорт статистики (по компаниям)', ['export-companies', 'id' => $model->id], ['class' => 'btn btn-danger'.($model->answersCount == 0 ? ' disabled':''),'target'=>'_blank']) ?>
        <?= Html::a('Экспорт свободных вопросов', ['export-docx', 'id' => $model->id], ['class' => 'btn btn-danger'.(!$model->devidedQuestions['free'] ? ' disabled':''),'target'=>'_blank']) ?>
        <?= Html::a('Экспорт изображений статистики', ['export-images', 'id' => $model->id], ['class' => 'btn btn-danger'.($model->answersCount == 0 ? ' disabled':''),'target'=>'_blank']) ?>
    </div>
    </br>
    <div class="row">
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Удалить анкету и все ответы?',
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a(
            'Редактировать',
            ['update', 'id' => $model->id],
            ['class' => 'btn btn-primary'])
        ?>
        <?= Html::a(
            $model->status == $model::STATUS_HIDDEN ? 'Утвердить' : 'Скрыть',
            [$model->status == $model::STATUS_HIDDEN ? 'approve' : 'hide' , 'id' => $model->id],
            ['class' => ' btn btn-success'])
        ?>
    </div>


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
            [
                'label' => 'Фабрика',
                'attribute' => 'factory.title',
                'value'=>Html::a($model->factory->title,['/factory/view','id'=>$model->factory->id]),
                'format'=>'html',
            ],
            'title',
            'description:html',
            [
                'label'=>'Для аптек',
                'value'=>$model->getPharmaciesView(false)
            ],
            [
                'label'=>'Для образования',
                'value'=>$model->getEducationsView(true)
            ],
            [
                'label'=>'Для компаний',
                'value'=>$model->getCompanyView(true)
            ],
            [
                'label'=>'Для типов пользователей',
                'value'=>$model->getTypesView(true)
            ],
            [
                'label'=>'Уникальные просмотры',
                'value'=>\common\models\survey\Unique::find()->where(['survey_id' => $model->id])->count()
            ],
            'views_limit',
            'points',
            [
                'attribute'=>'forList',
                'value'=>$model->lists,
            ],
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
