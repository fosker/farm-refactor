<?php

use yii\helpers\Html;

$this->title = 'RikoMotivation';
?>

<div class="col-md-6 col-md-offset-3">
    <?= Html::a('Компании', ['/company/index'],['class'=>'btn btn-info']) ?>

    <?= Html::a('Отделы', ['/department/index'],['class'=>'btn btn-info']) ?>

    <?= Html::a('Должности', ['/position/index'],['class'=>'btn btn-info']) ?>

    <?= Html::a('Сотрудники', ['/employee/index'],['class'=>'btn btn-info']) ?>

    <?= Html::a('Критерии', ['/criterion/index'],['class'=>'btn btn-info']) ?>
</div>





