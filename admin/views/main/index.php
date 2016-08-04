<?php

use yii\widgets\ListView;
$this->title = 'Главная';
$this->registerJsFile('js/hide-show.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>

<div class="main-index">
    <h1 class="text-center">Добро пожаловать</h1>
</div>

<div class="col-md-12">
    <div class="col-md-5 col-md-offset-1">
        <div class="row">
            <h3>Пользователи в городах</h3>
        </div>
        <div class="row">
            <?php
            echo ListView::widget([
                'dataProvider' => $regions,
                'itemView' => '_itemRegion',
                'options' => [
                    'tag' => 'div',
                ],
                'itemOptions' => [
                    'class' => 'region'
                ],
            ]);
            ?>
        </div>
    </div>
    <div class="col-md-5 col-md-offset-1">
        <div class="row">
            <h3>Пользователи в аптеках</h3>
        </div>
        <div class="row">
            <?php
            echo ListView::widget([
                'dataProvider' => $companies,
                'itemView' => '_itemCompany',
                'options' => [
                    'tag' => 'div',
                ],
                'itemOptions' => [
                    'class' => 'company'
                ],
            ]);
            ?>
        </div>
    </div>
</div>