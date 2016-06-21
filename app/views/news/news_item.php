<?php

use yii\helpers\Html;
use yii\helpers\Url;
?>

<div class="news_item">
    <a href="<?php echo Url::toRoute(['/news/view', 'id' => $model->id]);?>">

        <h3 class="text-center"><?=$model->title?></h3>
        <h6 class="text-center" style="color:black"><?=$model->date?></h6>
        <img src="<?=$model->imagePath?>" class="img-responsive" style="width: 80%"/>
        <br/>
        <div class="news_text well">
            <?=strlen($model->text) > 1500 ? substr($model->text, 0, 1502) . '... Читать далее...' : $model->text?>
        </div>
        <div class="text-center">
            <p class="glyphicon glyphicon-eye-open"> <?=$model->countUniqueViews()?></p>
            <p class="glyphicon glyphicon-pencil" style="margin-left: 10px"> <?=count($model->comments)?></p>
        </div>
    </a>
</div>