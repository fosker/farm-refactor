<?php

$this->title = 'ФармБонус';

use yii\bootstrap\Carousel;
use yii\bootstrap\Collapse;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;

$this->registerJsFile('js/wow.js');
$this->registerJsFile('js/landing.js', ['depends' => 'yii\web\JqueryAsset']);
$this->registerJsFile('js/slimscroll.min.js', ['depends' => 'yii\web\JqueryAsset']);
$this->registerJsFile('js/countUp.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('js/counters.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('js/jquery.touch.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);


?>
<div class="landing">
    <header>
        <div class="info">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 text-left col-md-4 visible-md-block visible-lg-block">
                        <?=Html::img('img/info/email.png', ['width' => '18px'])?>
                        <a href="mailto:info@pharbonus.by">info@pharbonus.by</a>
                        <span class="phone">
                        <?=Html::img('img/info/phone.png', ['width' => '10px'])?>
                            <a href="tel:+375291953706">+375 29 195 37 06</a>
                        </span>
                    </div>
                    <div class="col-lg-5 col-md-6 col-sm-9 col-xs-12 clearfix">
                        <?=Html::a('Pharm<span class="hidden-xs">aceutical</span><span class="visible-xs-ilnine">.</span> company, Med<span class="visible-xs-inline">.</span><span class="hidden-xs">ical</span> representative', '#', ['class' => 'active']) ?> <span class="hidden-xs">/</span> <?=Html::a('Pharmacy specialist', '#') ?>
                    </div>
                    <div class="col-lg-2 col-lg-offset-1 col-md-2 col-sm-3 text-right visible-sm-block visible-md-block visible-lg-block">
                        <?=Html::img('img/info/earth.png', ['width' => '14px'])?> <?=Html::a('EN', '?lang=en', ['class' => Yii::$app->language == 'en-US' ? 'active' : '']) ?> / <?=Html::a('RU', '?lang=ru', ['class' => Yii::$app->language == 'ru-RU' ? 'active' : '']) ?>
                    </div>
                </div>
            </div>
        </div>
        <?php NavBar::begin([
            'options' => [
                'class' => 'navbar',
            ],
            'brandLabel' => Html::img('img/logo.png'),
        ]);

        echo Nav::widget([
            'options' => ['class' => 'navbar-nav navbar-right'],
            'encodeLabels' => false,
            'items' => [
                [
                    'label' => 'Home',
                    'url' => '#home',
                    'options' => ['id' => 'link-home'],
                    'linkOptions' => ['class' => 'active'],
                ],
                [
                    'label' => 'Advantages',
                    'url' => '#advantages',
                    'options' => ['id' => 'link-advantages'],
                ],
                [
                    'label' => 'Features',
                    'url' => '#functions',
                    'options' => ['id' => 'link-functions'],
                ],
                [
                    'label' => 'Digital HR',
                    'url' => '#mobile-hr',
                    'options' => ['id' => 'link-mobile-hr'],

                ],
                [
                    'label' => 'Contact',
                    'url' => '#contacts',
                    'options' => ['id' => 'link-contacts'],
                ],
                [
                    'label' => 'Send a request',
                    'url' => 'callback',
                    'linkOptions' => ['class' => 'btn btn-custom'],
                ],
            ],
        ]);
        NavBar::end();?>

        <a href="?lang=ru">
            <button type="button" class="navbar-toggle collapsed lang-picker-toggle">
                RU<i class="glyphicon glyphicon-chevron-right"></i>
            </button>
        </a>
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
                    'caption' => '<h2>Mobile application</h2><h1 class="text-uppercase"><b>Pharmbonus</b></h1><h3><i>Multifunctional tool that can quickly<br />and at minimum cost<br />help to achieve significant results for brand promotion</i></h3>',
                ],
                [
                    'content' => Html::img('img/slider/slide3.jpg'),
                    'caption' => '<h2>Mobile application</h2><h1 class="text-uppercase"><b>Pharmbonus</b></h1><h3><i>Effectively increases brand awareness and loyalty<br /> of pharmacists to the pharmace utical company,<br /> simplifies interaction between them</i></h3>',
                ],
                [
                    'content' => Html::img('img/slider/slide1.jpg'),
                    'caption' => '<h2>Mobile application</h2><h1 class="text-uppercase"><b>Pharmbonus</b></h1><h3><i>For easy communication<br />within the company.</i></h3>',
                ],
            ]
        ]); ?>
    </div>

    <div class="advantages" id="advantages">
        <div class="container">
            <h1 class="section-header wow fadeInDown"><b>Advantages</b></h1>
            <h4 class="section-sub-header wow fadeInDown">Effective brand promotion at a minimal cost</h4>

            <div class="row infographics">
                <div class="col-xs-8 col-xs-offset-2 visible-xs-block clearfix">
                    <?= Html::img('img/infographics.png', ['class' => 'info-background  wow fadeInDown']) ?>
                    <?= Html::img('img/logo.png', ['class' => 'logo  wow fadeInUp']) ?>
                </div>
                <div class="col-sm-4 col-xs-12">
                    <div class="row advantage wow fadeInLeft">
                        <div class="col-md-2 col-sm-12 col-xs-2">
                            <span class="number">01</span>
                        </div>
                        <div class="col-md-10 col-sm-12 col-xs-10">
                            <h4>Multifunctionality</h4>
                            <p>Application can be effectively used to promote the brand to increase the loyalty of the target audience informing pharmacists of updates as well as for easy communication within the company</p>
                        </div>
                    </div>
                    <div class="row advantage wow fadeInLeft">
                        <div class="col-md-2 col-sm-12 col-xs-2">
                            <span class="number">02</span>
                        </div>
                        <div class="col-md-10 col-sm-12 col-xs-10">
                            <h4>Promotion cost minimization</h4>
                            <p>Application allows you to quickly achieve goals while reducing promotion costs</p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 visible-sm-block visible-md-block visible-lg-block">
                    <?= Html::img('img/infographics.png', ['class' => 'info-background  wow fadeInDown']) ?>
                    <?= Html::img('img/logo.png', ['class' => 'logo  wow fadeInUp']) ?>
                </div>
                <div class="col-sm-4 col-xs-12">
                    <div class="row advantage wow fadeInRight">
                        <div class="col-md-2 col-sm-12 col-xs-2">
                            <span class="number">03</span>
                        </div>
                        <div class="col-md-10 col-sm-12 col-xs-10">
                            <h4>Experience</h4>
                            <p>Team of professionals with years of experience in the pharmaceutical field that was involved in creating applications</p>
                        </div>
                    </div>
                    <div class="row advantage wow fadeInRight">
                        <div class="col-md-2 col-sm-12 col-xs-2">
                            <span class="number">04</span>
                        </div>
                        <div class="col-md-10 col-sm-12 col-xs-10">
                            <h4>Pharmacy staff only</h4>
                            <p>Information about the profile of each user education is tested before it is accessed by the app</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="functions" id="functions">
        <h1 class="section-header wow fadeInDown">Features</h1>
        <h4 class="section-sub-header wow fadeInDown">Usability and functionality for each client</h4>

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
                                        <h3>Presentations</h3>
                                        <p>Tell about your products using visual presentations.</p>
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
                                        <h3>Questionary</h3>
                                        <p>Create and conduct surveys.</p>
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
                                        <h3>News</h3>
                                        <p>Share your news with the target audience of pharmaceutical company shares.</p>
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
                                        <h3>Company shares</h3>
                                        <p>ОAn excellent opportunity to attract additional attention to your product.</p>
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
                                        <h3>Feedback</h3>
                                        <p>Create relevant topics and communicate effectively with pharmacists, pharmacists.</p>
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
                                        <h3>Seminars and Webinars</h3>
                                        <p>Inform a wide audience about your events.</p>
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
                                        <h3>Vacancies</h3>
                                        <p>Solve personnel issue effectively, post a job offers in a mobile application.</p>
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
                                        <h3>Pharmaceutical companies</h3>
                                        <p>Get closer to pharmacists – tell about your company in mobile app.</p>
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
                            <h2>Presentation</h2>
                            <h4>Why is it necessary to create and publish your company's presentation in the mobile app <b>PharmBonus</b>?</h4>
                            <p>Because pharmacists can view it at any time of the day: during a trip in transport or having a cup of tea in the kitchen. And to this day Presentation are a major tool for increasing brand value. Well-written presentation is able to inform in detail, focus on the key benefits and advantages of your products. A decoration and has the ability to affect the aesthetic and emotional country pharmacist, a pharmacist.</p>
                            <a class="to-slide right" data-slide="2"><i class="glyphicon glyphicon-chevron-right"></i></a>
                        </div>
                    </div>
                    <div class="slide" id="slide2">
                        <?=Html::img('img/functions/bg/1.png')?>
                        <div class="capture">
                            <a class="to-slide left" data-slide="1"><i class="glyphicon glyphicon-chevron-left"></i></a>
                            <h2>Questionnaires</h2>
                            <h4>For the active promotion of new products to the pharmaceutical market, and to update information about an existing product, in a mobile application PharmBonus have the opportunity to conduct interviews or questionnaires.</h4>
                            <p>The strong side of these surveys is the ability to specify the local respondents, for example, you can configure a survey so that it will appear only pharmacists in Minsk, in the October district, Brest region pharmacists, Mikashevichi or pharmacists, pharmacists g.p.Zelva, Gomel region.</p>
                            <a class="to-slide right" data-slide="3"><i class="glyphicon glyphicon-chevron-right"></i></a>
                        </div>
                    </div>
                    <div class="slide" id="slide3">
                        <?=Html::img('img/functions/bg/1.png')?>
                        <div class="capture">
                            <a class="to-slide left" data-slide="2"><i class="glyphicon glyphicon-chevron-left"></i></a>
                            <h2>News</h2>
                            <h4>Your company held a grand study in a particular area of pharmacology? Has developed a new and unique product successfully passed all stages of the registration of a new product? Or there were changes in the company itself, which everyone should know the pharmaceutical field worker?</h4>
                            <p>Exactly for news of your company, this section is created. Here you can specify all of the most relevant and important company news.</p>
                            <a class="to-slide right" data-slide="4"><i class="glyphicon glyphicon-chevron-right"></i></a>
                        </div>
                    </div>
                    <div class="slide" id="slide4">
                        <?=Html::img('img/functions/bg/1.png')?>
                        <div class="capture">
                            <a class="to-slide left" data-slide="3"><i class="glyphicon glyphicon-chevron-left"></i></a>
                            <h2>Company shares</h2>
                            <h4>As a rule, shares are an excellent opportunity to attract additional attention to the product, correctly composed shares might work better than advertising.</h4>
                            <p>The mobile application PharmBonus have all the necessary tools to alert pharmacists about ongoing promotions, as well as a convenient collection of the necessary data on them.</p>
                            <a class="to-slide right" data-slide="5"><i class="glyphicon glyphicon-chevron-right"></i></a>
                        </div>
                    </div>
                    <div class="slide" id="slide5">
                        <?=Html::img('img/functions/bg/1.png')?>
                        <div class="capture">
                            <a class="to-slide left" data-slide="4"><i class="glyphicon glyphicon-chevron-left"></i></a>
                            <h2>Feedback</h2>
                            <h4>The timely feedback between pharmaceutical companies and pharmacists - the key to a successful and long-term cooperation.</h4>
                            <p>With this feature, the company is creating in the application an unlimited number of relevant topics, which can be supplemented by a description or explanation. This communication makes it possible to quickly address emerging challenges.</p>
                            <a class="to-slide right" data-slide="6"><i class="glyphicon glyphicon-chevron-right"></i></a>
                        </div>
                    </div>
                    <div class="slide" id="slide6">
                        <?=Html::img('img/functions/bg/1.png')?>
                        <div class="capture">
                            <a class="to-slide left" data-slide="5"><i class="glyphicon glyphicon-chevron-left"></i></a>
                            <h2>Seminars</h2>
                            <h4>Does your company conduct training seminars, or webinars? PharmBonus mobile application is an excellent platform to inform and promote of such events.</h4>
                            <p>You can place information about your training seminar, webinar, with the possibility of personal records on them from pharmacists.</p>
                            <a class="to-slide right" data-slide="7"><i class="glyphicon glyphicon-chevron-right"></i></a>
                        </div>
                    </div>
                    <div class="slide" id="slide7">
                        <?=Html::img('img/functions/bg/1.png')?>
                        <div class="capture">
                            <a class="to-slide left" data-slide="6"><i class="glyphicon glyphicon-chevron-left"></i></a>
                            <h2>Vacancies</h2>
                            <p>One advantage of using this feature is the placement of job applications available only for the pharmaceutical sector employees, which makes the search for employees more quickly and efficiently.</p>
                            <a class="to-slide right" data-slide="8"><i class="glyphicon glyphicon-chevron-right"></i></a>
                        </div>
                    </div>
                    <div class="slide" id="slide8">
                        <?=Html::img('img/functions/bg/1.png')?>
                        <div class="capture">
                            <a class="to-slide left" data-slide="7"><i class="glyphicon glyphicon-chevron-left"></i></a>
                            <h2>Pharmaceutical companies</h2>
                            <h4>In this section, each pharmaceutical company may place a full description of its products and in the expanded form to share your story of creation and development, your achievement.</h4>
                            <p>This will greatly facilitate and speed up the work of the pharmaceutical worker, if he needs to quickly find information about a product or company that produces it.</p>
                        </div>
                    </div>

                </div>
            </div>

            <?= Collapse::widget([
                'items' => [
                    [
                        'label' => '<p>
                                        '.Html::img("img/functions/1.png").'
                                    </p>
                                    <h3>Presentations</h3>
                                    <p>Tell about your products using visual presentations.</p>
                                    <div class="arrow">
                                        '.Html::img("img//arrow.png").'
                                    </div>
                                    ',
                        'content' => '<h4>Почему стоит создавать и размещать презентации вашей компании в мобильном приложении ФармБонус?</h4>
                            <p>Because pharmacists can view it at any time of the day: during a trip in transport or having a cup of tea in the kitchen. And to this day Presentation are a major tool for increasing brand value. Well-written presentation is able to inform in detail, focus on the key benefits and advantages of your products. A decoration and has the ability to affect the aesthetic and emotional country pharmacist, a pharmacist.</p>',
                        'encode' => false,
                    ],
                    [
                        'label' => '<p>
                                        '.Html::img("img/functions/2.png").'
                                    </p>
                                    <h3>Questionnaires</h3>
                                    <p>Create and conduct surveys.</p>
                                    <div class="arrow">
                                        '.Html::img("img//arrow.png").'
                                    </div>
                                    ',
                        'content' => '<h4>For the active promotion of new products to the pharmaceutical market, and to update information about an existing product, in a mobile application PharmBonus have the opportunity to conduct interviews or questionnaires.</h4>
                            <p>СThe strong side of these surveys is the ability to specify the local respondents, for example, you can configure a survey so that it will appear only pharmacists in Minsk, in the October district, Brest region pharmacists, Mikashevichi or pharmacists, pharmacists g.p.Zelva, Gomel region.</p>',
                        'encode' => false,
                    ],
                    [
                        'label' => '<p>
                                        '.Html::img("img/functions/3.png").'
                                    </p>
                                    <h3>News</h3>
                                    <p>Share your news with the pharmaceutical target audience.</p>
                                    <div class="arrow">
                                        '.Html::img("img//arrow.png").'
                                    </div>
                                    ',
                        'content' => '<h4>Your company held a grand study in a particular area of pharmacology? Has developed a new and unique product successfully passed all stages of the registration of a new product? Or there were changes in the company itself, which everyone should know the pharmaceutical field worker?</h4>
                            <p>Exactly for news of your company, this section is created. Here you can specify all of the most relevant and important company news.</p>',
                        'encode' => false,
                    ],
                    [
                        'label' => '<p>
                                        '.Html::img("img/functions/4.png").'
                                    </p>
                                    <h3>Company shares</h3>
                                    <p>An excellent opportunity to attract additional attention to your product.</p>
                                    <div class="arrow">
                                        '.Html::img("img//arrow.png").'
                                    </div>
                                    ',
                        'content' => '<h4>As a rule, shares are an excellent opportunity to attract additional attention to the product, correctly composed shares might work better than advertising.</h4>
                            <p>The mobile application PharmBonus have all the necessary tools to alert pharmacists about ongoing promotions, as well as a convenient collection of the necessary data on them.</p>',
                        'encode' => false,
                    ],
                    [
                        'label' => '<p>
                                        '.Html::img("img/functions/5.png").'
                                    </p>
                                    <h3>Feedback</h3>
                                    <p>Create relevant topics and communicate effectively with pharmacists, pharmacists.</p>
                                    <div class="arrow">
                                        '.Html::img("img//arrow.png").'
                                    </div>
                                    ',
                        'content' => '<h4>The timely feedback between pharmaceutical companies and pharmacists - the key to a successful and long-term cooperation.</h4>
                            <p>With this feature, the company is creating in the application an unlimited number of relevant topics, which can be supplemented by a description or explanation. This communication makes it possible to quickly address emerging challenges.</p>',
                        'encode' => false,
                    ],
                    [
                        'label' => '<p>
                                        '.Html::img("img/functions/6.png").'
                                    </p>
                                    <h3>Seminars and Webinars</h3>
                                    <p>Inform a wide audience about your events.</p>
                                    <div class="arrow">
                                        '.Html::img("img//arrow.png").'
                                    </div>
                                    ',
                        'content' => '<h4>Does your company conduct training seminars, or webinars? PharmBonus mobile application is an excellent platform to inform and promote of such events.</h4>
                            <p>You can place information about your training seminar, webinar, with the possibility of personal records on them from pharmacists.</p>',
                        'encode' => false,
                    ],
                    [
                        'label' => '<p>
                                        '.Html::img("img/functions/7.png").'
                                    </p>
                                    <h3>Vacancies</h3>
                                    <p>Solve personnel issue effectively, post a job offers in a mobile application</p>
                                    <div class="arrow">
                                        '.Html::img("img//arrow.png").'
                                    </div>
                                    ',
                        'content' => '<p>One advantage of using this feature is the placement of job applications available only for the pharmaceutical sector employees, which makes the search for employees more quickly and efficiently.</p>',
                        'encode' => false,
                    ],
                    [
                        'label' => '<p>
                                        '.Html::img("img/functions/8.png").'
                                    </p>
                                    <h3>Pharmaceutical companies</h3>
                                    <p>Get closer to pharmacists – tell about your company in mobile app.</p>
                                    <div class="arrow">
                                        '.Html::img("img//arrow.png").'
                                    </div>
                                    ',
                        'content' => '<h4>In this section, each pharmaceutical company may place a full description of its products and in the expanded form to share your story of creation and development, your achievement.</h4>
                            <p>This will greatly facilitate and speed up the work of the pharmaceutical worker, if he needs to quickly find information about a product or company that produces it.</p>',
                        'encode' => false,
                    ],
                ]
            ]);?>
        </div>
    </div>

    <div class="achievements">
        <h1 class="wow fadeInDown">Our achievements in numbers</h1>
        <div class="container">
            <div class="row">
                <div class="col-xs-4 wow fadeInLeft">
                    <h3>It is shown</h3>
                    <p class="counter">17</p>
                    <h2>Presentations</h2>
                </div>
                <div class="col-xs-4 wow fadeInUp">
                    <h3>It is conduct</h3>
                    <p class="counter">23</p>
                    <h2>surveys</h2>
                </div>
                <div class="col-xs-4 wow fadeInRight">
                    <h3>It is published</h3>
                    <p class="counter">25</p>
                    <h2>articles</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="mobile-hr" id="mobile-hr">
        <h1 class="section-header wow fadeInDown">Digital HR</h1>
        <h4 class="section-sub-header wow fadeInDown">Rapid alert about the latest news of the company and the industry</h4>
        <div class="row task">
            <div class="col-md-7 col-sm-6 wow fadeInLeft">
                <div class="problem clearfix">
                    <div>
                        If your company is problematic to collect all the employees and discuss with them the latest innovations in the pharmaceutical field or events within the company, then you need a <b>Digital HR.</b>
                    </div>
                </div>
            </div>
            <div class="col-md-5 col-sm-6 solving wow fadeInRight">
                <div>
                    <h2>Digital HR</h2>
                    <p>- It is your interactive channel of communication with employees and operational tool for vocational training</p>
                </div>
            </div>
        </div>

        <div class="container steps">
            <div class="row">
                <div class="col-md-4 col-sm-6 col-sm-offset-0 col-md-offset-1">
                    <?=Html::img('img/a.png', ['class' => 'wow fadeInDown']) ?>
                    <?=Html::img('img/g.png', ['class' => 'wow fadeInDown arrow', 'data-wow-delay' => '0.1s']) ?>
                    <?=Html::img('img/b.png', ['class' => 'wow fadeInDown', 'data-wow-delay' => '0.2s']) ?>
                    <?=Html::img('img/g.png', ['class' => 'wow fadeInDown arrow', 'data-wow-delay' => '0.3s']) ?>
                    <?=Html::img('img/c.png', ['class' => 'wow fadeInDown', 'data-wow-delay' => '0.4s']) ?>
                    <p class="wow fadeInUp">Fast adaptation of new employees</p>
                </div>
                <div class="col-md-6 col-sm-6 col-sm-offset-0 col-md-offset-1">
                    <?=Html::img('img/d.png', ['class' => 'wow fadeInDown', 'data-wow-delay' => '0.5s']) ?>
                    <?=Html::img('img/g.png', ['class' => 'wow fadeInDown arrow', 'data-wow-delay' => '0.6s']) ?>
                    <?=Html::img('img/e.png', ['class' => 'wow fadeInDown', 'data-wow-delay' => '0.7s']) ?>
                    <?=Html::img('img/g.png', ['class' => 'wow fadeInDown arrow', 'data-wow-delay' => '0.8s']) ?>
                    <?=Html::img('img/f.png', ['class' => 'wow fadeInDown', 'data-wow-delay' => '0.9s']) ?>
                    <p class="wow fadeInUp">Loyal and professional <br />collective</p>
                </div>
            </div>
        </div>

        <div class="fast-notify">
            <div class="container">
                <h2 class="wow fadeInDown">Quick alert about the latest news of the company,<br /> you need to have:</h2>

                <div class="row">
                    <div class="col-xs-4  wow fadeInLeft">
                        <?=Html::img('img/laptop.png') ?>
                        <p>Gadget,<br />a laptop<br />or computer</p>
                    </div>
                    <div class="col-xs-4 wow fadeInUp">
                        <?=Html::img('img/world.png') ?>
                        <p>The Internet</p>
                    </div>
                    <div class="col-xs-4 wow fadeInRight">
                        <?=Html::img('img/app.png') ?>
                        <p>Mobile app</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="install-app clearfix">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-7 col-xs-12">
                        <h1 class="wow fadeInLeft">GET THE APP <br />Pharmbonus!</h1>
                        <p class="wow fadeInUp"><?=Html::a(Html::img('img/apple.png'), 'https://itunes.apple.com/ru/app/pharmbonus/id1062954210?l=en&mt=8', ['target' => '_blank'])?> <?=Html::a(Html::img('img/google.png'), 'https://play.google.com/store/apps/details?id=com.pharmbonus.by&hl=ru', ['target' => '_blank']) ?></p>
                    </div>
                    <div class="col-lg-5  col-md-6 col-sm-5 wow fadeInRight visible-sm-block visible-md-block visible-lg-block">
                        <?= Html::img('img/phones.png') ?>
                    </div>
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
                    <div class="col-md-5 col-lg-4 visible-md-block visible-lg-block">
                        <div class="partner wow fadeInDown">
                            <h2><b>PharmBonus</b></h2>
                            <h4>is Your reliable<br />partner in the digital<br />world!</h4>
                        </div>
                    </div>
                    <div class="col-md-7 col-md-8 col-xs-12">
                        <div class="row">
                            <div class="col-xs-4 clearfix wow fadeInDown" data-wow-delay="0.2s">
                                <p><?=Html::img('img/contacts/email.png', ['width' => '40px', 'style' =>'margin-top: 24px;'])?></p>
                                <p><a href="mailto:info@pharbonus.by">info@pharbonus.by</a></p>
                            </div>
                            <div class="col-xs-4 clearfix wow fadeInDown" data-wow-delay="0.4s">
                                <p><?=Html::img('img/contacts/phone.png', ['width' => '30px'])?></p>
                                <p><a href="tel:+375291953706">+375291953706</a></p>
                            </div>
                            <div class="col-xs-4 clearfix wow fadeInDown" data-wow-delay="0.6s">
                                <p><?=Html::img('img/contacts/marker.png', ['width' => '30px'])?></p>
                                <p>Minsk, Korzhenevskogo lane, 28-115, room 2</p>
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
