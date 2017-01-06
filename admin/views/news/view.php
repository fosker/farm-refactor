<?php

use yii\helpers\Html;
use yii\widgets\DetailView;


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
        <?= Html::a('Статистика просмотров', ['statistics', 'id' => $model->id],['class'=>'btn btn-info']) ?>
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
            'priority',
            [
                'label' => 'Компания',
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
                'label'=>'Для организаций',
                'value'=>$model->getCompanyView(true)
            ],
            [
                'label'=>'Для типов пользователей',
                'value'=>$model->getTypesView(true)
            ],
            'text:html',
            [
                'attribute'=>'views',
                'value'=>$model->countUniqueViews() . ' (' . $model->countRealViews() . ')'
            ],
            'date:datetime'
        ],
    ]) ?>

    <h4>Рекомендуемые новости</h4>
    <ul>
    <?php foreach($model->recommended as $recommend):?>
        <li>
            <?php echo Html::a($recommend->title,['/news/view','id'=>$recommend->id])?>
        </li>
    <?php endforeach;?>
    </ul>

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
        <div class="col-md-8" style="word-wrap: break-word;">
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
