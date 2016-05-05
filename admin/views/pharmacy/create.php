<?php

use yii\helpers\Html;


$this->title = 'Добавить аптеку';
?>
<div class="pharmacy-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'cities'=>$cities,
        'companies'=>$companies,
    ]) ?>

</div>
