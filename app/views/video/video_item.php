<?php

use yii\helpers\Html;
use app\components\YoutubeWidget;
use yii\helpers\Url;
?>

<div class="video_item">
    <a href="<?php echo Url::toRoute(['/video/view', 'id' => $model->id]);?>">
        <h3><?=$model->title?></h3>
        <?= Html::tag('div', YoutubeWidget::widget([
        "code"=> substr($model->link,-11)
        ]), ['class' => 'video-container'])?>
        <div class="text-center">
            <p class="glyphicon glyphicon-pencil"> <?=count($model->comments)?></p>
        </div>
    </a>
</div>