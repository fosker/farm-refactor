<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Presentation */

$this->title = 'Добавить презентацию';
?>
<div class="presentation-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'education'=>$education,
        'regions'=>$regions,
        'cities'=>$cities,
        'companies'=>$companies,
        'types'=>$types,
        'factories'=>$factories,
    ]) ?>

</div>
