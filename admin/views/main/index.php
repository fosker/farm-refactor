<?php

use yii\widgets\ListView;

$this->title = 'Главная';
$this->registerJsFile('js/hide-show.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>

<div class="main-index">
    <h1 class="text-center">Добро пожаловать</h1>
</div>

<div class="col-md-12">
    <div class="row">
        <div class="col-md-5 col-md-offset-1">
            <h4>Всего пользователей: <?=$pharmacists?></h4>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="row">
                <h3 class="text-center">Время регистрации</h3>
            </div>
            <div class="row">
                <?php foreach($years as $year):?>
                    <?php foreach($months as $index => $month):?>
                    </br>
                    <div class="month">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-4">
                                <b><?=$month . ' ' . $year ;?></b>
                            </div>
                           <?php if($count_in_month[$index]):?>
                                <div class="col-md-3"><button type="button" class="btn btn-info more-regions">+</button></div>
                           <?php endif;?>
                            <div class="col-md-2"><i><?=$count_in_month[$index] ? $count_in_month[$index] : 0?></i></div>
                        </div>
                    </div>
                    </br>
                    <div class="row region-months" style="display: none">
                        <div class="month-region">
                            <ul style="list-style: none">
                                <?php foreach($region_month as $region):?>
                                    <?php if($index == $region['month'] && $region['name'] != ''):?>
                                            <li>
                                                <br>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <b><?=$region['name']?></b>
                                                    </div>
                                                    <div class="col-md-3"><button type="button" class="btn btn-info more-users">+</button></div>
                                                    <div class="col-md-4"><i><?=$region['count']?></i></div>
                                                </div>
                                                <div class="row user-region-month" style="display: none">
                                                    <ul style="list-style: none">
                                                        <?php foreach($user_region_month as $user):?>
                                                            <?php if($index == $user['month'] && $region['name'] == $user['region']):?>
                                                                <li>
                                                                    <br>
                                                                    <div class="row">
                                                                        <div class="col-md-8">
                                                                            <?=$user['name'];?>
                                                                        </div>
                                                                    <div>
                                                                        <div class="col-md-4"><i>
                                                                                <?=$user['date_reg']?></i>
                                                                        </div>
                                                                    </div>
                                                                        <br>
                                                                </li>
                                                            <?php endif;?>
                                                        <?php endforeach;?>
                                                    </ul>
                                            </div>
                                            <br>
                                            </li>
                                    <?php endif;?>
                                <?php endforeach;?>
                            </ul>
                        </div>
                    </div>
                    </div>
                    <?php endforeach;?>
                <?php endforeach;?>
            </div>
        </div>
        <div class="col-md-4">
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
        <div class="col-md-4">
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
</div>