<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = $model->title;
?>
<div class="presentation-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Список', ['index'],['class'=>'btn btn-info']) ?>
        <?= Html::a('Ответы ('.$model->answersCount.')', ['presentations/answer/index', 'Search[presentation.id]' => $model->id], ['class' => 'btn btn-warning']) ?>
        <?= Html::a('Экспорт pdf', ['presentations/answer/export-pdf', 'presentation_id' => $model->id], ['class' => 'btn btn-danger'.($model->answersCount == 0 ? ' disabled':''),'target'=>'_blank']) ?>
        <?= Html::a('Экспорт xls', ['presentations/answer/export-xls', 'presentation_id' => $model->id], ['class' => 'btn btn-success'.($model->answersCount == 0 ? ' disabled':''),'target'=>'_blank']) ?>

        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'pull-right btn btn-danger',
            'data' => [
                'confirm' => 'Удалить презентацию и все ответы?',
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

        <?= Html::a(
            $model->home == $model::HOME_HIDDEN ? 'Разместить на главной' : 'Убрать с главной',
            [$model->home == $model::HOME_HIDDEN ? 'approve-home' : 'hide-home' , 'id' => $model->id],
            ['class' => 'pull-right btn btn-success'])
        ?>
    </p>

    <h2>Информация о презентации</h2>

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
            [
                'label' => 'Фабрика',
                'attribute' => 'factory.title',
                'value'=>Html::a($model->factory->title,['/factory/view','id'=>$model->factory->id]),
                'format'=>'html',
            ],
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
            'description:html',
            'points',
            [
                'attribute'=>'grayList',
                'value'=>$model->grayList ? 'да' : 'нет',
            ],
            [
                'attribute'=>'whiteList',
                'value'=>$model->whiteList ? 'да' : 'нет',
            ],
            'views_limit',
            'home_priority',
            [
                'attribute'=>'home',
                'value'=>$model::getHomeStatusList()[$model->home],
            ],
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

   <h2>Слайды
       <?= Html::a(
           'Добавить',
           ['add-slide', 'presentation_id'=>$model->id],
           ['class' => 'pull-right btn btn-success'])
       ?>
   </h2>

    <table class="table">
        <tr><th>Слайд</th><th>Описание слайда</th><th>Порядковый номер</th><th>Действия</th></tr>

        <?php foreach($model->slides as $slide) : ?>
            <tr>
                <td><?=Html::img($slide->imagePath, ['alt' => 'Изображение', 'height'=>'200px']);?></td>
                <td><?= $slide->description; ?></td>
                <td><?=$slide->order_index?></td>
                <td>
                    <?=Html::a('<span class="glyphicon glyphicon-pencil"></span>',['edit-slide', 'id'=>$slide->id],['class'=>'btn btn-primary btn-xs']);?>

                    <?=Html::a('<span class="glyphicon glyphicon-trash"></span>',
                        ['delete-slide', 'id'=>$slide->id],
                        ['class'=>'btn btn-danger btn-xs',
                            'data' => [
                                'confirm' => 'Удалить слайд?',
                                'method' => 'post',
                            ]
                        ]
                    );?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <h2>Вопросы
        <?= Html::a(
            'Добавить',
            ['add-question', 'presentation_id'=>$model->id],
            ['class' => 'pull-right btn btn-success'])
        ?>
    </h2>

    <table class="table">
        <tr><th>Вопрос</th><th>Варианты ответа/Ответ</th><th>Порядковый номер</th><th>Количество правильных ответов</th><th>Действия</th></tr>

        <?php foreach($model->questions as $question) : ?>
            <tr>
                <td><?=$question->question?></td>
                <td>
                    <p class="text-info"><?=$question->validAnswer;?></p>
                    <table>
                        <?php foreach($question->options as $option) :?>
                            <tr><td><p class="text-info"><?=$option->value?></p></td></tr>
                        <?php endforeach; ?>
                    </table>
                </td>
                <td><?=$question->order_index?></td>
                <td><?=$question->right_answers?></td>
                <td>
                    <?=Html::a('<span class="glyphicon glyphicon-list"></span>',['view-option', 'question_id'=>$question->id, 'presentation_id'=>$model->id],['class'=>'btn btn-warning btn-xs']);?>

                    <?=Html::a('<span class="glyphicon glyphicon-pencil"></span>',['edit-question', 'id'=>$question->id, 'presentation_id'=>$model->id],['class'=>'btn btn-primary btn-xs']);?>

                    <?=Html::a('<span class="glyphicon glyphicon-trash"></span>',
                        ['delete-question', 'id'=>$question->id],
                        ['class'=>'btn btn-danger btn-xs',
                            'data' => [
                                'confirm' => 'Удалить вопрос и все ответы?',
                                'method' => 'post',
                            ]
                        ]
                    );?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <h4>Комментарии</h4>
    <div class="col-md-8">
        <?php if(!$model->comments) {
            echo "Комментариев нет.";
        } else {
        foreach ($model->comments as $comment): ?>
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
        <div class="col-md-4" style="word-wrap: break-word;">
            <p><?=$comment->comment?></p>
            <h6><i><?=$comment->date_add?></i></h6>
        </div>
    </div>
    <div class="row">
        <div class="col-md-2 col-md-offset-1">
            <?= Html::a('Удалить', ['delete-comment', 'id' => $comment->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Удалить комментарий?',
                'method' => 'post',
            ],
            ]) ?>
        </div>
    </div>
    </br>
<?php endforeach;
}?>
    </div>

</div>
