<?php

$this->title = 'ФармБонус';

use yii\bootstrap\Carousel;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;

?>
<div class="landing">
    <header>
        <?php NavBar::begin([
            'options' => [
                'class' => 'navbar',
            ],
            'brandLabel' => Html::img('img/logo.png'),
        ]); ?>
        <?= Nav::widget([
            'options' => ['class' => 'navbar-nav navbar-right'],
            'encodeLabels' => false,
            'items' => [
                [
                    'label' => 'Главная',
                ],
                [
                    'label' => 'Наши преимущества',
                ],
                [
                    'label' => 'Функционал',
                ],
                [
                    'label' => 'Мобильный HR',
                ],
                [
                    'label' => 'Контакты',
                ],
                [
                    'label' => 'Отправить заявку',
                    'linkOptions' => ['class' => 'btn-custom'],
                ],
            ],
        ]);
        NavBar::end();?>
    </header>

    <div class="info">
        <div class="container">
            <div class="row">
                <div class="col-md-2">
                    <?=Html::img('img/info/earth.png', ['width' => '14px'])?> <?=Html::a('EN', ['', '#' => '']) ?> / <?=Html::a('RU', ['', '#' => ''], ['class' => 'active']) ?>
                </div>
                <div class="col-md-6 text-center col-md-offset-1">
                    <?=Html::a('Фармацевтическия компания, медицинский представитель', ['', '#' => ''], ['class' => 'active']) ?> / <?=Html::a('фармацевт,провизор', ['', '#' => '']) ?>
                </div>
                <div class="col-md-3 text-right">
                    <?=Html::img('img/info/email.png', ['width' => '18px'])?>
                    <a href="mailto:info@pharbonus.by">info@pharbonus.by</a>
                    <span class="phone">
                        <?=Html::img('img/info/phone.png', ['width' => '10px'])?>
                        <a href="tel:+375291953706">+375 29 195 37 06</a>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="home">
        <?= Carousel::widget([
            'options' => ['class' => 'carousel slide'],
            'controls' => [Html::img('img/slider/arrow_left.png'),Html::img('img/slider/arrow_right.png')],
            'clientOptions' => [
                'interval' => false,
            ],
            'items' => [
                [
                    'content' => Html::img('img/slider/slide2.jpg'),
                    'caption' => '<h2>Мобильное приложение</h2><h1 class="text-uppercase"><b>Фармбонус</b></h1><h3><i>Многофункциональный инструмент, позволяющий<br />быстро и при минимальных затратах добиться<br />значимых результатов по продвижению бренда</i></h3>',
                ],
                [
                    'content' => Html::img('img/slider/slide3.jpg'),
                    'caption' => '<h2>Мобильное приложение</h2><h1 class="text-uppercase"><b>Фармбонус</b></h1><h3><i>Эффективно повышает узнаваемость марки<br />и лояльность провизоров, фармацевтов<br />к фармацевтической компании, упрощает<br />взаимодействие между ними</i></h3>',
                ],
                [
                    'content' => Html::img('img/slider/slide1.jpg'),
                    'caption' => '<h2>Мобильное приложение</h2><h1 class="text-uppercase"><b>Фармбонус</b></h1><h3><i>Используйте для удобной<br />коммуникации внутри компании.</i></h3>',
                ],
            ]
        ]); ?>
    </div>

    <div class="advantages">
        <div class="container">
            <h1 class="section-header"><b>Наши преимущества</b></h1>
            <h4 class="section-sub-header">Эффективное продвижение бренда при минимальных затратах</h4>

            <div class="row infographics">
                <div class="col-md-4">
                    <div class="row advantage">
                        <div class="col-md-2">
                            <span class="number">01</span>
                        </div>
                        <div class="col-md-10">
                            <h4>Многофункциональность</h4>
                            <p>Приложение может эффективно использоваться для продвижения бренда, увеличения лояльности провизоров, информирования целевой аудитории о новинках, а так же для удобной коммуникации внутри компании</p>
                        </div>
                    </div>
                    <div class="row advantage">
                        <div class="col-md-2">
                            <span class="number">03</span>
                        </div>
                        <div class="col-md-10">
                            <h4>Опыт</h4>
                            <p>Опыт команды профессионалов с многолетним стажем работы в фармацевтической сфере, который задействован при создании Фармбонуса</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <?= Html::img('img/infographics.png', ['class' => 'info-background']) ?>
                    <?= Html::img('img/logo.png', ['class' => 'logo']) ?>
                </div>
                <div class="col-md-4">
                    <div class="row advantage">
                        <div class="col-md-2">
                            <span class="number">02</span>
                        </div>
                        <div class="col-md-10">
                            <h4>Минимизация затрат</h4>
                            <p>Приложение позволяет в короткие сроки достигать поставленных целей при сокращении расходов на продвижение</p>
                        </div>
                    </div>
                    <div class="row advantage">
                        <div class="col-md-2">
                            <span class="number">04</span>
                        </div>
                        <div class="col-md-10">
                            <h4>Только для работников фармацевтической сферы</h4>
                            <p>Данные о профильном образовании каждого пользователя проходят проверку прежде, чем будет получен доступ к приложению</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="functions">
        <h1 class="section-header">Функционал сервиса</h1>
        <h4 class="section-sub-header">Функциональность и удобство использования для каждого клиента</h4>
    </div>

    <div class="achievements">
        <h1 >Наши достижения в цифрах</h1>
        <div class="container">
            <div class="col-md-4">
                <h3>Показано</h3>
                <p>17</p>
                <h2>Презентаций</h2>
            </div>
            <div class="col-md-4">
                <h3>Проведено</h3>
                <p>23</p>
                <h2>Опроса</h2>
            </div>
            <div class="col-md-4">
                <h3>Опубликовано</h3>
                <p>15</p>
                <h2>Статей</h2>
            </div>
        </div>
    </div>

    <div class="mobile-hr">
        <h1 class="section-header">Мобильный HR</h1>
        <h4 class="section-sub-header">Быстрое оповещение о последних новостях компании и отрасли</h4>
        <div class="row task">
            <div class="col-md-7">
                <div class="problem">
                    Если в Вашей компании проблематично собрать всех сотрудников и обсудить с ними последние нововведения в фармацевтической сфере или события внутри компании, то Вам необходим <b>Мобильный HR.</b>
                </div>
            </div>
            <div class="col-md-5 solving">
                <h2>Мобильный HR</h2>
                <p>- Это Ваш интерактивный канал общения с работниками и оперативный инструмент для профессионального обучения</p>
            </div>
        </div>

        <div class="container steps">
            <div class="row">
                <div class="col-md-4 col-md-offset-1">

                    <p>Ускоренная адаптация новых сотрудников</p>
                </div>
                <div class="col-md-6 col-md-offset-1">

                    <p>Лояльный и профессиональный<br />коллектив</p>
                </div>
            </div>
        </div>

        <div class="fast-notify">
            <div class="container ">
                <h2>Быстрое оповещение о послежних новостях<br /> компании, для этого нужно только иметь:</h2>

                <div class="row">
                    <div class="col-md-4">
                        <?=Html::img('img/laptop.png') ?>
                        <p>Гаджет,<br />Ноутбук<br />или компьютер</p>
                    </div>
                    <div class="col-md-4">
                        <?=Html::img('img/world.png') ?>
                        <p>Интернет</p>
                    </div>
                    <div class="col-md-4">
                        <?=Html::img('img/app.png') ?>
                        <p>Мобильное приложение</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="install-app clearfix">
            <div class="clearfix">
                <div class="col-md-6">
                    <h1>Установите мобильное<br />приложение Фармбонус!</h1>
                    <p><?=Html::a(Html::img('img/apple.png'), '')?> <?=Html::a(Html::img('img/google.png'), '')?></p>
                </div>
                <div class="col-md-5">
                    <?= Html::img('img/phones.png') ?>
                </div>
            </div>
        </div>
        <div class="space-wrap">
        </div>
    </div>

    <div class="contacts">
        <div class="details">
            <div class="container">
                <div class="row">
                    <div class="col-md-4">
                        <div class="partner">
                            <h2><b>PharmBonus</b></h2>
                            <h4>Ваш надежный<br />партнер в цифровом<br />Мире!</h4>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-4">
                                <?=Html::img('img/contacts/email.png', ['width' => '40px', 'style' =>'margin-top: 24px;'])?>
                                <p><a href="mailto:info@pharbonus.by">info@pharbonus.by</a></p>
                            </div>
                            <div class="col-md-4">
                                <?=Html::img('img/contacts/phone.png', ['width' => '30px'])?>
                                <p><a href="tel:+375291953706">+375291953706</a></p>
                            </div>
                            <div class="col-md-4">
                                <?=Html::img('img/contacts/marker.png', ['width' => '30px'])?>
                                <p>г. Минск, пер. Корженевского, 28-115, пом. 2</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="map">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2353.6948614863054!2d27.524732306774702!3d53.848290923416386!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x46dbd056c57d12c5%3A0xf7c7a7a91d1c2e7a!2z0LfQsNCy0YPQu9Cw0Log0JrQsNGA0LbQsNC90LXRntGB0LrQsNCz0LAgMjgsINCc0ZbQvdGB0LosINCR0ZbQu9C-0YDRg9GB0Yw!5e0!3m2!1suk!2sua!4v1473624578016" width="100%" height="300" frameborder="0" style="border:0" allowfullscreen></iframe>
        </div>
        <div class="copy">
            &copy; PharmBonus, 2016
        </div>
    </div>
</div>
