<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Theme */

$this->title = 'Добавить тему';
?>
<div class="theme-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'factories' => $factories
    ]) ?>

</div>
