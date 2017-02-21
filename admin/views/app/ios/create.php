<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\generated\app\Ios */

$this->title = 'Создать версию';
?>
<div class="ios-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
