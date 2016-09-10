<?php

$this->title = 'ФармБонус';
use yii\bootstrap\NavBar;
use kartik\nav\NavX;

?>

<!--<header>-->
<!--    --><?php
//    NavBar::begin([
//        'options' => [
//            'class' => 'navbar navbar-fixed-top',
//        ],
//        'innerContainerOptions' => [
//            'class' => 'container-fluid'
//        ]
//    ]);
//    ?>
<!--    <div class="row" id="info">-->
<!--        <div class="col-md-2 col-md-offset-1">-->
<!--            <img src="img/earth.png" alt="earth"/>-->
<!--            <span class="lang-title">EN/RU</span>-->
<!--        </div>-->
<!--        <div class="col-md-4 col-md-offset-1" id="role">-->
<!--            <span class="role-title"><b>Фармацевтическая компания, медицинский представитель</b> / провизор, фармацевт</span>-->
<!--        </div>-->
<!--        <div class="col-md-2 col-sm-4" id="email">-->
<!--            <img src="img/email.png" alt="email"/>-->
<!--            <span class="lang-title">info@pharmbonus.by</span>-->
<!--        </div>-->
<!--        <div class="col-md-2 col-sm-4" id="phone">-->
<!--            <img src="img/phone.png" alt="phone"/>-->
<!--            <span class="lang-title">+375 29 195 37 06</span>-->
<!--        </div>-->
<!--    </div>-->
<!--    <div id="menu" class="row">-->
<!--        <div class="col-md-1 col-md-offset-1">-->
<!--            <div class="navbar-header">-->
<!--                <a class="navbar-brand" href="#">-->
<!--                    <img src="img/logo.png" alt="logo" />-->
<!--                </a>-->
<!--            </div>-->
<!--        </div>-->
<!--        <div class="col-md-10">-->
<!--        --><?//= NavX::widget([
//            'encodeLabels' => false,
//            'options' => ['class' => 'navbar-nav pull-right'],
//            'items' => [
//                [
//
//                    'label'=> 'Главная',
//                    'url'=>'#'
//                ],
//                [
//
//                    'label'=> 'Наши преимущества',
//                    'url'=>'#'
//                ],
//                [
//
//                    'label'=> 'Функционал',
//                    'url'=>'#'
//                ],
//                [
//
//                    'label'=> 'Мобильный HR',
//                    'url'=>'#'
//                ],
//                [
//
//                    'label'=> 'Контакты',
//                    'url'=>'#'
//                ],
//            ],
//        ]);
//        echo '<div class="clearfix"></div>';
//        NavBar::end();
//        ?>
<!--            <div id="slider" class="carousel" data-ride="carousel">-->
<!--                <ol class="carousel-indicators">-->
<!--                    <li data-target="#slider" data-slide-to="0" class="active"></li>-->
<!--                    <li data-target="#slider" data-slide-to="1"></li>-->
<!--                    <li data-target="#slider" data-slide-to="2"></li>-->
<!--                </ol>-->
<!---->
<!--                <div class="carousel-inner" role="listbox">-->
<!--                    <div class="item active">-->
<!--                        <img src="img/slider/picture1.png" alt="pic1">-->
<!--                        <div class="carousel-caption">-->
<!--                            <div class="row">-->
<!--                                <span class="slide-header">Мобильное приложение</span>-->
<!--                            </div>-->
<!--                            <div class="row">-->
<!--                                <span class="slide-text">ФАРМБОНУС</span>-->
<!--                            </div>-->
<!--                            <div class="row">-->
<!--                                <span class="slide-subtext col-md-8 col-md-offset-2">Многофункциональный инструмент, позволяющий-->
<!--                            быстро и при минимальных-->
<!--                                затратах добиться значимых-->
<!--                                результатов по продвижению брендов</span>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
<!---->
<!--                    <div class="item">-->
<!--                        <img src="img/slider/picture2.png" alt="Chania">-->
<!--                        <div class="carousel-caption">-->
<!--                            <div class="row">-->
<!--                                <span class="slide-header">Мобильное приложение</span>-->
<!--                            </div>-->
<!--                            <div class="row">-->
<!--                                <span class="slide-text">ФАРМБОНУС</span>-->
<!--                            </div>-->
<!--                            <div class="row">-->
<!--                                <span class="slide-subtext col-md-8 col-md-offset-2">Эффективно повышает узнаваемость-->
<!--                                марки и лоялность провизоров, фармацевтов к фармацевтической компании,-->
<!--                                    упрощает взаимодействие между ними</span>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
<!---->
<!--                    <div class="item">-->
<!--                        <img src="img/slider/picture3.png" alt="Flower">-->
<!--                        <div class="carousel-caption">-->
<!--                            <div class="row">-->
<!--                                <span class="slide-header">Мобильное приложение</span>-->
<!--                            </div>-->
<!--                            <div class="row">-->
<!--                                <span class="slide-text">ФАРМБОНУС</span>-->
<!--                            </div>-->
<!--                            <div class="row">-->
<!--                                <span class="slide-subtext col-md-8 col-md-offset-2">Используется для удобной коммуникации-->
<!--                                внутри компании.</span>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->
<!--                <a class="right carousel-control" href="#slider" role="button" data-slide="next">-->
<!--                    <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>-->
<!--                </a>-->
<!--            </div>-->
<!--</header>-->

<div id="advantages">
    <div class="container">
        <b><p class="block-header text-center">Наши преимущества</p></b>
        <p class="block-text text-center">Эффективное продвижение бренда при минимальных затратах </p>
        <div class="row">
            <div class="row">
                <div class="col-md-4 margin-top">
                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-10">
                            <b><p class="advantage-title">Многофункциональность</p></b>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <span class="advantage-number">01</span>
                        </div>
                        <div class="col-md-10">
                            <p class="advantage-text">Приложение может эффективно
                                использоваться, для продвижения бренда,
                                увеличения лояльности провизоров,
                                информирования целевой аудитории о новинках,
                                а также для удобной коммуникации внутри компании </p>

                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-md-offset-4 margin-top">
                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-10">
                            <b><p class="advantage-title">Минимизация затрат</p></b>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <span class="advantage-number">03</span>
                        </div>
                        <div class="col-md-10">
                            <p class="advantage-text">Приложение позволяет
                                в короткие сроки достигать поставленных целей при сокращении расходов
                                на продвижение </p>

                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 margin-top">
                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-10">
                            <b><p class="advantage-title">Опыт</p></b>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <span class="advantage-number">02</span>
                        </div>
                        <div class="col-md-10">
                            <p class="advantage-text">Опыт команды профессионалов с
                                многолетним стажем работы в фармацевтической сфере,
                                который был задействован при создании ФармБонуса </p>

                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-md-offset-4 margin-top">
                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-10">
                            <b><p class="advantage-title">Только для работников фармацевтической сферы</p></b>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <span class="advantage-number">04</span>
                        </div>
                        <div class="col-md-10">
                            <p class="advantage-text">Данные о профильном образовании
                                каждого пользователя проходят
                                проверку прежде чем будет получен
                                доступ к программе </p>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="functions">
    <div class="container">
        <b><p class="block-header text-center">Функционал сервиса</p></b>
        <p class="block-text text-center">Функциональность и удобство использования для каждого клиента </p>
        <div class="row">
            <div id="tabs" class="margin-top"></div>
        </div>
    </div>
</div>

<div id="achievements">
    <div class="container-fluid">
        <b><p class="achievements-header text-center">Наши достижения в цифрах</p></b>
        <div class="row text-center">
            <div class="col-md-4">
                <i><p class="ach-header">Показано</p></i>
                <b><p class="ach-count">14</p></b>
                <b><p class="ach-title">Презентаций</p></b>
            </div>
            <div class="col-md-4">
                <i><p class="ach-header">Проведено</p></i>
                <b><p class="ach-count">88</p></b>
                <b><p class="ach-title">Опроса</p></b>
            </div>
            <div class="col-md-4">
                <i><p class="ach-header">Опубликовано</p></i>
                <b><p class="ach-count">228</p></b>
                <b><p class="ach-title">Статей</p></b>
            </div>
        </div>
    </div>
</div>

<div class="hr">
    <div class="container-fluid">
        <b><p class="block-header text-center">Мобильный HR</p></b>
        <p class="block-text text-center">Быстрое оповещение о последних новостях компании </br> и отрасли. </p>
        <div class="row">
            <div class="col-md-7 col-xs-12 hr-left">
                <i><p class="hr-left-text">Если в Вашей компании проблематично собрать всех сотрудников
                и обсудить с ними последние нововведения в фармацевтической сфере
                или события внутри компании, то Вам просто необходим </i><b>Мобильный HR</b>. </p>
            </div>
            <div class="col-md-5 col-xs-12 hr-right">
                <b><p class="hr-right-text-header hr-right-text">Мобильный HR</p></b>
                <b><p class="hr-right-text-content hr-right-text">- это Ваш интерактивный канал общения
                с работниками </br> и оперативный инструмент для  профессионального обучения</p></b>
            </div>
        </div>
        <div class="row">

        </div>
    </div>
</div>
