<?php

use yii\widgets\ListView;
use yii\bootstrap\Html;
use common\models\News;
use common\models\Presentation;
use common\models\Seminar;
use common\models\Vacancy;
use common\models\User;

$this->title = 'Главная';
$this->registerJsFile('js/hide-show.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('js/show-comment.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>

<div class="main-index">
    <h1 class="text-center">Добро пожаловать</h1>
</div>


<div class="comments_wrap">
    <div class="row text-center">
        <button type="button" class="btn btn-info more-comments">показать 20 последних комментариев</button>
    </div>
    </br>
    <div class="comments" style="display: none">
    <?php foreach($comments as $comment):
        switch($comment['content']) {
            case 'news':
                $url = Html::a('Новость: '.News::findOne($comment['content_id'])->title, ['/news/view', 'id'=>$comment['content_id']]);
                $comment_url = ['/news/comment', 'id'=>$comment['id']];
                break;
            case 'presentation':
                $url = Html::a('Презентация: '.Presentation::findOne($comment['content_id'])->title, ['/presentation/view', 'id'=>$comment['content_id']]);
                $comment_url = ['/presentation/comment', 'id'=>$comment['id']];
                break;
            case 'vacancy':
                $url = Html::a('Вакансия: '.Vacancy::findOne($comment['content_id'])->title, ['/vacancy/view', 'id'=>$comment['content_id']]);
                $comment_url = ['/vacancy/comment', 'id'=>$comment['id']];
                break;
            case 'seminar':
                $url = Html::a('Семинар: '.Seminar::findOne($comment['content_id'])->title, ['/seminar/view', 'id'=>$comment['content_id']]);
                $comment_url = ['/seminar/comment', 'id'=>$comment['id']];
                break;
        }
        ?>

        <div class="row">
            <div class="col-md-6 col-md-offset-3 well">
                <div class="row text-center"><?=$url?></div>
                </br>
                <div class="row">
                    <div class="col-md-3"><?=Html::a(User::findOne($comment['user_id'])->login, ['/user/view', 'id' => $comment['user_id']])?></div>
                    <div class="col-md-9"><?=$comment['comment']?></div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <h6><?=$comment['date_add']?></h6>
                    </div>
                    <div class="col-md-9">
                        <?=Html::a('<i class="glyphicon glyphicon-tag"></i>', $comment_url,
                            [
                                'class' => 'list-comment pull-right',
                                'title'=>$comment['admin_comment'],
                            ]);?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach;?>
    </div>
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
                           <?php if($calendar[$year][$index]):?>
                                <div class="col-md-3"><button type="button" class="btn btn-info more-regions">+</button></div>
                           <?php endif;?>
                            <div class="col-md-2"><i><?=$calendar[$year][$index] ? $calendar[$year][$index] : 0?></i></div>
                        </div>
                    </div>
                    </br>
                    <div class="row region-months" style="display: none">
                        <div class="month-region">
                            <ul style="list-style: none">
                                <?php foreach($region_month as $region):?>
                                    <?php if($index == $region['month'] && $region['name'] != '' && $region['year'] == $year):?>
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
                                                            <?php if($index == $user['month'] && $region['name'] == $user['region'] && $user['year'] == $year):?>
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
                    <hr>
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