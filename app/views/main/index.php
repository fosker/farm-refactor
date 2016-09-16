<?php

$this->title = 'ФармБонус';

use yii\bootstrap\Carousel;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;

$this->registerJsFile('js/wow.js');
$this->registerJsFile('js/landing.js', ['depends' => 'yii\web\JqueryAsset']);
$this->registerJsFile('js/slimscroll.min.js', ['depends' => 'yii\web\JqueryAsset']);
$this->registerJsFile('js/countUp.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('js/counters.js', ['depends' => [\yii\web\JqueryAsset::className()]]);


?>
<div class="landing">
    <header>
        <div class="info">
            <div class="container">
                <div class="row">
                    <div class="col-lg-2 col-md-2">
                        <?=Html::img('img/info/earth.png', ['width' => '14px'])?> <?=Html::a('EN', '?lang=en', ['class' => Yii::$app->language == 'en-US' ? 'active' : '']) ?> / <?=Html::a('RU', '?lang=ru', ['class' => Yii::$app->language == 'ru-RU' ? 'active' : '']) ?>
                    </div>
                    <div class="col-lg-6 text-center col-lg-offset-1 col-md-offset-0 col-md-6">
                        <?=Html::a('Фармацевтическия компания, медицинский представитель', '#', ['class' => 'active']) ?> / <?=Html::a('фармацевт,провизор', '#') ?>
                    </div>
                    <div class="col-lg-3 text-right col-md-4">
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
                    'url' => '#home',
                    'options' => ['id' => 'link-home'],
                    'linkOptions' => ['class' => 'active'],
                ],
                [
                    'label' => 'Наши преимущества',
                    'url' => '#advantages',
                    'options' => ['id' => 'link-advantages'],
                ],
                [
                    'label' => 'Функционал',
                    'url' => '#functions',
                    'options' => ['id' => 'link-functions'],
                ],
                [
                    'label' => 'Мобильный HR',
                    'url' => '#mobile-hr',
                    'options' => ['id' => 'link-mobile-hr'],

                ],
                [
                    'label' => 'Контакты',
                    'url' => '#contacts',
                    'options' => ['id' => 'link-contacts'],
                ],
                [
                    'label' => 'Отправить заявку',
                    'url' => 'callback',
                    'linkOptions' => ['class' => 'btn-custom'],
                ],
            ],
        ]);
        NavBar::end();?>
    </header>

    <div class="home" id="home">
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

    <div class="advantages" id="advantages">
        <div class="container">
            <h1 class="section-header wow fadeInDown"><b>Наши преимущества</b></h1>
            <h4 class="section-sub-header wow fadeInDown">Эффективное продвижение бренда при минимальных затратах</h4>

            <div class="row infographics">
                <div class="col-md-4">
                    <div class="row advantage wow fadeInLeft">
                        <div class="col-md-2">
                            <span class="number">01</span>
                        </div>
                        <div class="col-md-10">
                            <h4>Многофункциональность</h4>
                            <p>Приложение может эффективно использоваться для продвижения бренда, увеличения лояльности провизоров, информирования целевой аудитории о новинках, а так же для удобной коммуникации внутри компании</p>
                        </div>
                    </div>
                    <div class="row advantage wow fadeInLeft">
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
                    <?= Html::img('img/infographics.png', ['class' => 'info-background  wow fadeInDown']) ?>
                    <?= Html::img('img/logo.png', ['class' => 'logo  wow fadeInUp']) ?>
                </div>
                <div class="col-md-4">
                    <div class="row advantage wow fadeInRight">
                        <div class="col-md-2">
                            <span class="number">02</span>
                        </div>
                        <div class="col-md-10">
                            <h4>Минимизация затрат</h4>
                            <p>Приложение позволяет в короткие сроки достигать поставленных целей при сокращении расходов на продвижение</p>
                        </div>
                    </div>
                    <div class="row advantage wow fadeInRight">
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

    <div class="functions" id="functions">
        <h1 class="section-header wow fadeInDown">Функционал сервиса</h1>
        <h4 class="section-sub-header wow fadeInDown">Функциональность и удобство использования для каждого клиента</h4>

        <div class="container">
            <div class="row presentation">
                <div class="col-md-4 col-lg-5">
                    <ul>
                        <li class="active">
                            <a class="to-slide" data-slide="1">
                                <div class="row slide-link">
                                    <div class="col-md-3">
                                        <?=Html::img('img/functions/1.png') ?>
                                    </div>
                                    <div class="col-md-9">
                                        <h3>Презентации</h3>
                                        <p>Рассказывайте о своей продукции с помощью наглядных презентаций.</p>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a class="to-slide" data-slide="2">
                                <div class="row slide-link">
                                    <div class="col-md-3">
                                        <?=Html::img('img/functions/2.png') ?>
                                    </div>
                                    <div class="col-md-9">
                                        <h3>Анкеты</h3>
                                        <p>Создавайте и проводите опросы.</p>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a class="to-slide" data-slide="3">
                                <div class="row slide-link">
                                    <div class="col-md-3">
                                        <?=Html::img('img/functions/3.png') ?>
                                    </div>
                                    <div class="col-md-9">
                                        <h3>Новости</h3>
                                        <p>Делитесь своими новостями с фармацевтической целевой аудиторией.</p>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a class="to-slide" data-slide="4">
                                <div class="row slide-link">
                                    <div class="col-md-3">
                                        <?=Html::img('img/functions/4.png') ?>
                                    </div>
                                    <div class="col-md-9">
                                        <h3>Акции</h3>
                                        <p>Отличная возможность привлечь дополнительное внимание к вашему продукту.</p>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a class="to-slide" data-slide="5">
                                <div class="row slide-link">
                                    <div class="col-md-3">
                                        <?=Html::img('img/functions/5.png') ?>
                                    </div>
                                    <div class="col-md-9">
                                        <h3>Обратная связь</h3>
                                        <p>Создавайте актуальные темы и эффективно взаимодействуйте с провизорами, фармацевтами.</p>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a class="to-slide" data-slide="6">
                                <div class="row slide-link">
                                    <div class="col-md-3">
                                        <?=Html::img('img/functions/6.png') ?>
                                    </div>
                                    <div class="col-md-9">
                                        <h3>Семинары, вебинары</h3>
                                        <p>Оповещайте широкую аудиторию о своих мероприятиях.</p>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a class="to-slide" data-slide="7">
                                <div class="row slide-link">
                                    <div class="col-md-3">
                                        <?=Html::img('img/functions/7.png') ?>
                                    </div>
                                    <div class="col-md-9">
                                        <h3>Вакансии</h3>
                                        <p>Решайте кадровый вопрос эффективно, разместив вакансии в мобильном приложении.</p>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a class="to-slide" data-slide="8">
                                <div class="row slide-link">
                                    <div class="col-md-3">
                                        <?=Html::img('img/functions/8.png') ?>
                                    </div>
                                    <div class="col-md-9">
                                        <h3>Фармацевтические компании</h3>
                                        <p>Станьте ближе для провизоров, фармацевтов - расскажите о своей компании в мобильном приложении.</p>
                                    </div>
                                </div>
                            </a>
                        </li>

                    </ul>
                </div>
                <div class="col-md-8 col-lg-7">
                    <div class="slide active" id="slide1">
                        <?=Html::img('img/functions/bg/1.png')?>
                        <div class="capture">
                            <h2>Презентации</h2>
                            <h4>Почему стоит создавать и размещать презентации вашей компании в мобильном приложении <b>ФармБонус</b>?</h4>
                            <p>Потому, что провизоры и фармацевты могут просмотреть ее в любое время: во время поездки в транспорте или за чашкой чая на кухне. Презентаци и по сей день являются серьезным инструментом для увеличения стоимости бренда. Грамотно составленная логически прописання презентаци способна подробно проинформировать , акцентировать внимание на ключевые выгоды и преимущества Вашей продукции. А художественное оформление имеет способность затронуть и эстетические, и эмоциональные страны провизора, фармацевта.</p>
                            <a class="to-slide" data-slide="2"><i class="glyphicon glyphicon-chevron-right"></i></a>
                        </div>
                    </div>
                    <div class="slide" id="slide2">
                        <?=Html::img('img/functions/bg/1.png')?>
                        <div class="capture">
                            <h2>Анкеты</h2>
                            <h4>Для активного продвижения на фармацевтический рынок новой продукции, а также для обновления информации о уже существующем продукте, в мобильном приложении ФармБонус есть возможность проведения опросов или анкетирования.</h4>
                            <p>Сильной стороной создания таких опросов является возможность задать локальность опрашиваемых, а участники опроса могут ответить, когда угодно и где угодно. Например, можно настроить опрос так, что он будет показываться только фармацевтам в г. Минске, в Октябрьском районе, провизорам Брестской области, города Микашевичи или провизорам, фармацевтам г.п.Зельва, Гомельской области.</p>
                            <a class="to-slide" data-slide="3"><i class="glyphicon glyphicon-chevron-right"></i></a>
                        </div>
                    </div>
                    <div class="slide" id="slide3">
                        <?=Html::img('img/functions/bg/1.png')?>
                        <div class="capture">
                            <h2>Новости</h2>
                            <h4>Ваша компания провела грандиозное исследование в определённой области фармакологии? Разработали новый уникальный препарат и успешно прошли все этапы регистрации нового продукта? Или произошли изменения в самой компании, о которых должен знать каждый работник фармацевтической сферы?</h4>
                            <p>Именно для новостей Вашей компании создан этот раздел. Здесь вы можете указать все самые актуальные и важные новости компании.</p>
                            <a class="to-slide" data-slide="4"><i class="glyphicon glyphicon-chevron-right"></i></a>
                        </div>
                    </div>
                    <div class="slide" id="slide4">
                        <?=Html::img('img/functions/bg/1.png')?>
                        <div class="capture">
                            <h2>Акции</h2>
                            <h4>Как правило, акции являются отличной возможностью привлечь дополнительное внимание к продукту, а грамотно составленная акция может сработать лучше, чем оплачиваемая реклама.</h4>
                            <p>В мобильном приложении ФармБонус есть все необходимые инструменты для оповещения фармацевтических работников о проходящих акциях, а так же удобном сборе необходимых данных по ним.</p>
                            <a class="to-slide" data-slide="5"><i class="glyphicon glyphicon-chevron-right"></i></a>
                        </div>
                    </div>
                    <div class="slide" id="slide5">
                        <?=Html::img('img/functions/bg/1.png')?>
                        <div class="capture">
                            <h2>Обратная связь</h2>
                            <h4>Своевременная обратная связь между фармацевтической компанией и провизорами, фармацевтами – залог успешного и длительного сотрудничества.</h4>
                            <p>С помощью этой функции компания создает в приложении неограниченное количество актуальных тем, которые можно дополнить описанием или пояснением. Такое общение дает возможность максимально быстро решать возникающие трудности.</p>
                            <a class="to-slide" data-slide="6"><i class="glyphicon glyphicon-chevron-right"></i></a>
                        </div>
                    </div>
                    <div class="slide" id="slide6">
                        <?=Html::img('img/functions/bg/1.png')?>
                        <div class="capture">
                            <h2>Семинары</h2>
                            <h4>Ваша компания проводит обучающие семинары или вебинары? Мобильное приложение ФармБонус является отличной площадкой для информирования и продвижения таких мероприятий.</h4>
                            <p>Вы можете разместить информацию о своем обучающем семинаре,вебинаре с возможностью личной записи на них.</p>
                            <a class="to-slide" data-slide="7"><i class="glyphicon glyphicon-chevron-right"></i></a>
                        </div>
                    </div>
                    <div class="slide" id="slide7">
                        <?=Html::img('img/functions/bg/1.png')?>
                        <div class="capture">
                            <h2>Вакансии</h2>
                            <p>Одним из преимуществ использования этой функции является размещение вакансии в приложении, доступном только для работников фармацевтической сферы, что делает поиск сотрудников более оперативным и эффективным..</p>
                            <a class="to-slide" data-slide="8"><i class="glyphicon glyphicon-chevron-right"></i></a>
                        </div>
                    </div>
                    <div class="slide" id="slide8">
                        <?=Html::img('img/functions/bg/1.png')?>
                        <div class="capture">
                            <h2>Фармацевтические компании</h2>
                            <h4>В этом разделе каждая фармацевтическая компания может разместить полное описание о своей продукции и в самом развёрнутом виде поделиться своей историей создания и развития, своими достижениями.</h4>
                            <p>Это в значительной степени облегчит и ускорит работу фармацевтического работника, если ему нужно быстро найти информацию о продукте или компании, которая его производит.</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="achievements">
        <h1 class="wow fadeInDown">Наши достижения в цифрах</h1>
        <div class="container">
            <div class="col-md-4 wow fadeInLeft">
                <h3>Показано</h3>
                <p class="counter">17</p>
                <h2>Презентаций</h2>
            </div>
            <div class="col-md-4 wow fadeInUp">
                <h3>Проведено</h3>
                <p class="counter">23</p>
                <h2>Опроса</h2>
            </div>
            <div class="col-md-4 wow fadeInRight">
                <h3>Опубликовано</h3>
                <p class="counter">15</p>
                <h2>Статей</h2>
            </div>
        </div>
    </div>

    <div class="mobile-hr" id="mobile-hr">
        <h1 class="section-header wow fadeInDown">Мобильный HR</h1>
        <h4 class="section-sub-header wow fadeInDown">Быстрое оповещение о последних новостях компании и отрасли</h4>
        <div class="row task">
            <div class="col-md-7 wow fadeInLeft">
                <div class="problem clearfix">
                    <div>
                        Если в Вашей компании проблематично собрать всех сотрудников и обсудить с ними последние нововведения в фармацевтической сфере или события внутри компании, то Вам необходим <b>Мобильный HR.</b>
                    </div>
                </div>
            </div>
            <div class="col-md-5 solving wow fadeInRight">
                <div>
                    <h2>Мобильный HR</h2>
                    <p>- Это Ваш интерактивный канал общения с работниками и оперативный инструмент для профессионального обучения</p>
                </div>
            </div>
        </div>

        <div class="container steps">
            <div class="row">
                <div class="col-md-4 col-md-offset-1">
                    <?=Html::img('img/a.png', ['class' => 'wow fadeInDown']) ?>
                    <?=Html::img('img/g.png', ['class' => 'wow fadeInDown arrow', 'data-wow-delay' => '0.1s']) ?>
                    <?=Html::img('img/b.png', ['class' => 'wow fadeInDown', 'data-wow-delay' => '0.2s']) ?>
                    <?=Html::img('img/g.png', ['class' => 'wow fadeInDown arrow', 'data-wow-delay' => '0.3s']) ?>
                    <?=Html::img('img/c.png', ['class' => 'wow fadeInDown', 'data-wow-delay' => '0.4s']) ?>
                    <p class="wow fadeInUp">Ускоренная адаптация новых сотрудников</p>
                </div>
                <div class="col-md-6 col-md-offset-1">
                    <?=Html::img('img/d.png', ['class' => 'wow fadeInDown', 'data-wow-delay' => '0.5s']) ?>
                    <?=Html::img('img/g.png', ['class' => 'wow fadeInDown arrow', 'data-wow-delay' => '0.6s']) ?>
                    <?=Html::img('img/e.png', ['class' => 'wow fadeInDown', 'data-wow-delay' => '0.7s']) ?>
                    <?=Html::img('img/g.png', ['class' => 'wow fadeInDown arrow', 'data-wow-delay' => '0.8s']) ?>
                    <?=Html::img('img/f.png', ['class' => 'wow fadeInDown', 'data-wow-delay' => '0.9s']) ?>
                    <p class="wow fadeInUp">Лояльный и профессиональный<br />коллектив</p>
                </div>
            </div>
        </div>

        <div class="fast-notify">
            <div class="container">
                <h2 class="wow fadeInDown">Быстрое оповещение о послежних новостях компании<br />, для этого нужно только иметь:</h2>

                <div class="row">
                    <div class="col-md-4  wow fadeInLeft">
                        <?=Html::img('img/laptop.png') ?>
                        <p>Гаджет,<br />Ноутбук<br />или компьютер</p>
                    </div>
                    <div class="col-md-4 wow fadeInUp">
                        <?=Html::img('img/world.png') ?>
                        <p>Интернет</p>
                    </div>
                    <div class="col-md-4 wow fadeInRight">
                        <?=Html::img('img/app.png') ?>
                        <p>Мобильное приложение</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="install-app clearfix">
            <div class="container">
                <div class="col-lg-6 col-md-6">
                    <h1 class="wow fadeInLeft">Установите мобильное<br />приложение Фармбонус!</h1>
                    <p class="wow fadeInUp"><?=Html::a(Html::img('img/apple.png'), 'https://itunes.apple.com/ru/app/pharmbonus/id1062954210?l=en&mt=8', ['target' => '_blank'])?> <?=Html::a(Html::img('img/google.png'), 'https://play.google.com/store/apps/details?id=com.pharmbonus.by&hl=ru', ['target' => '_blank']) ?></p>
                </div>
                <div class="col-lg-5  col-md-6 wow fadeInRight">
                    <?= Html::img('img/phones.png') ?>
                </div>
            </div>
        </div>
        <div class="space-wrap">
        </div>
    </div>

    <div class="contacts" id="contacts">
        <div class="details">
            <div class="container">
                <div class="row">
                    <div class="col-md-5 col-lg-4">
                        <div class="partner wow fadeInDown">
                            <h2><b>PharmBonus</b></h2>
                            <h4>Ваш надежный<br />партнер в цифровом<br />Мире!</h4>
                        </div>
                    </div>
                    <div class="col-md-7 col-md-8">
                        <div class="row">
                            <div class="col-md-4 wow fadeInDown" data-wow-delay="0.2s">
                                <?=Html::img('img/contacts/email.png', ['width' => '40px', 'style' =>'margin-top: 24px;'])?>
                                <p><a href="mailto:info@pharbonus.by">info@pharbonus.by</a></p>
                            </div>
                            <div class="col-md-4 wow fadeInDown" data-wow-delay="0.4s">
                                <?=Html::img('img/contacts/phone.png', ['width' => '30px'])?>
                                <p><a href="tel:+375291953706">+375291953706</a></p>
                            </div>
                            <div class="col-md-4 wow fadeInDown" data-wow-delay="0.6s">
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
