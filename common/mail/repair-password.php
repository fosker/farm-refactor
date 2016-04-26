<?php

use yii\helpers\Html;
use yii\helpers\Url;

?>

<p>Перейдите по <?= Html::a('ссылке',Url::to($route,true)); ?>.</p>

Или скопируйте в строку браузера: <?=Url::to($route,true);?>