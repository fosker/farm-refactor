<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\generated\app\Android */

$this->title = 'Создать версию';
$this->params['breadcrumbs'][] = ['label' => 'Версии Android', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="android-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
