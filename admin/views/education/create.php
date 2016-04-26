<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\profile\Education */

$this->title = 'Добавить образование';
?>
<div class="education-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
