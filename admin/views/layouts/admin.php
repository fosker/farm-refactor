<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use backend\assets\AppAsset;
use yii\widgets\Breadcrumbs;
use backend\models\admin\Right;
use backend\models\Admin;
use common\models\profile\UpdateRequest;
use kartik\nav\NavX;
/**
 * @var $content string
 */

AppAsset::register($this);
$count = UpdateRequest::find()->count();
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
    <div class="wrap">
        <?php
            NavBar::begin([
                'options' => [
                    'class' => 'navbar-inverse navbar-fixed-top',
                ],
            ]);
        echo NavX::widget([
                'encodeLabels' => false,
                'options' => ['class' => 'navbar-nav'],
                'items' => [
                    [

                        'label'=> 'Главная',
                        'items'=> [
                            [
                                'label' => 'Главная',
                                'url' => ['/main/index'],
                                'visible' => Right::HasAdmin(Yii::$app->admin->id, 'main')
                            ],
                            [
                                'label' => 'Администраторы',
                                'url' => ['/admin'],
                                'visible' => Right::HasAdmin(Yii::$app->admin->id, 'admin')
                            ],
                            [
                                'label' => 'Обратная связь',
                                'url' => ['/contact-form'],
                                'visible' => Right::HasAdmin(Yii::$app->admin->id, 'contact-form')
                            ],
                        ],
                        'visible' => Admin::showMain(Yii::$app->admin->id)
                    ],
                    [
                        'label' => 'Списки',
                        'items' => [
                            [
                                'label'=>'Города' ,
                                'url'=>['/city'],
                                'visible' => Right::HasAdmin(Yii::$app->admin->id, 'city')
                            ],
                            [
                                'label'=>'Фирмы',
                                'url'=>['/firm'],
                                'visible' => Right::HasAdmin(Yii::$app->admin->id, 'firm')
                            ],
                            [
                                'label'=>'Аптеки',
                                'url'=>['/pharmacy'],
                                'visible' => Right::HasAdmin(Yii::$app->admin->id, 'pharmacy')
                            ],
                            [
                                'label'=>'Образование',
                                'url'=>['/education'],
                                'visible' => Right::HasAdmin(Yii::$app->admin->id, 'education')
                            ],
                            [
                                'label'=>'Должности',
                                'url'=>['/position'],
                                'visible' => Right::HasAdmin(Yii::$app->admin->id, 'position')
                            ],
                            [
                                'label' => 'Баннеры',
                                'url' => ['/banner'],
                                'visible' => Right::HasAdmin(Yii::$app->admin->id, 'banner')
                            ],
                            [
                                'label' => 'Вещества',
                                'url' => ['/substance'],
                                'visible' => Right::HasAdmin(Yii::$app->admin->id, 'substance')
                            ],
                            [
                                'label' => 'Запросы',
                                'url' => ['/substances/request'],
                                'visible' => Right::HasAdmin(Yii::$app->admin->id, 'substances/request')
                            ],
                        ],
                        'visible' => Admin::showLists(Yii::$app->admin->id)
                    ],
                    ['label' => 'Пользователи',
                        'items'=>[
                            [
                                'label' => 'Пользователи',
                                'url' => ['/user'],
                                'visible' => Right::HasAdmin(Yii::$app->admin->id, 'user')
                            ],
                            [
                                'label' => 'Ожидают обновления ' . Html::tag('span', $count, ['class' => 'badge']),
                                'url' => ['/users/update-request'],
                                'visible' => Right::HasAdmin(Yii::$app->admin->id, 'users/update-request')
                            ],
                            [
                                'label' => 'Подарки',
                                'url' => ['/users/present'],
                                'visible' => Right::HasAdmin(Yii::$app->admin->id, 'users/present')
                            ],
                            [
                                'label' => 'Оповещения',
                                'items' => [
                                    [
                                        'label' => 'Оповещения группам',
                                        'url' => ['/users/push-groups'],
                                        'visible' => Right::HasAdmin(Yii::$app->admin->id, 'users/push-groups')
                                    ],
                                    [
                                        'label' => 'Оповещения пользователям',
                                        'url' => ['/users/push-users'],
                                        'visible' => Right::HasAdmin(Yii::$app->admin->id, 'users/push-users')
                                    ],
                                ]
                            ],
                        ],
                        'visible' => Admin::showUser(Yii::$app->admin->id)
                    ],
                    ['label' => 'Основное меню',
                        'items' => [
                            ['label' => 'Страницы',
                                'items'=>[
                                    [
                                        'label' => 'Страницы',
                                        'url' => ['/block'],
                                        'visible' => Right::HasAdmin(Yii::$app->admin->id, 'block')
                                    ],
                                    [
                                        'label' => 'Комментарии',
                                        'url' => ['/blocks/comment'],
                                        'visible' => Right::HasAdmin(Yii::$app->admin->id, 'blocks/comment')
                                    ],
                                    [
                                        'label' => 'Оценки',
                                        'url' => ['/blocks/mark'],
                                        'visible' => Right::HasAdmin(Yii::$app->admin->id, 'blocks/mark')
                                    ],
                                ],
                                'visible' => Admin::showBlock(Yii::$app->admin->id)
                            ],
                            ['label' => 'Новости',
                                'items'=>[
                                    [
                                        'label' => 'Новости',
                                        'url' => ['/news'],
                                        'visible' => Right::HasAdmin(Yii::$app->admin->id, 'news')
                                    ],
                                    [
                                        'label' => 'Комментарии',
                                        'url' => ['/newss/comment'],
                                        'visible' => Right::HasAdmin(Yii::$app->admin->id, 'newss/comment')
                                    ],
                                ],
                                'visible' => Admin::showNews(Yii::$app->admin->id)
                            ],
                            ['label' => 'Видео',
                                'items'=>[
                                    [
                                        'label' => 'Видео',
                                        'url' => ['/video'],
                                        'visible' => Right::HasAdmin(Yii::$app->admin->id, 'video')
                                    ],
                                    [
                                        'label' => 'Комментарии',
                                        'url' => ['/videos/comment'],
                                        'visible' => Right::HasAdmin(Yii::$app->admin->id, 'videos/comment')
                                    ],
                                ],
                                'visible' => Admin::showVideo(Yii::$app->admin->id)
                            ],
                            ['label'=>'Анкеты',
                                'items'=>[
                                    [
                                        'label'=>'Анкеты',
                                        'url'=>['/survey'],
                                        'visible' => Right::HasAdmin(Yii::$app->admin->id, 'survey')
                                    ],
                                    [
                                        'label'=>'Ответы',
                                        'url'=>['/surveys/answer'],
                                        'visible' => Right::HasAdmin(Yii::$app->admin->id, 'surveys/answer')
                                    ],
                                ],
                                'visible' => Admin::showSurvey(Yii::$app->admin->id)
                            ],
                            ['label'=>'Презентации',
                                'items'=>[
                                    [
                                        'label'=>'Презентации',
                                        'url'=>['/presentation'],
                                        'visible' => Right::HasAdmin(Yii::$app->admin->id, 'presentation')
                                    ],
                                    [
                                        'label'=>'Комментарии',
                                        'url'=>['/presentations/comment'],
                                        'visible' => Right::HasAdmin(Yii::$app->admin->id, 'presentations/comment')
                                    ],
                                    [
                                        'label'=>'Ответы',
                                        'url'=>['/presentations/answer'],
                                        'visible' => Right::HasAdmin(Yii::$app->admin->id, 'presentations/answer')
                                    ],
                                ],
                                'visible' => Admin::showPresentation(Yii::$app->admin->id)
                            ],
                            ['label'=>'Фабрики',
                                'items'=>[
                                    [
                                        'label'=>'Фабрики',
                                        'url'=>['/factory'],
                                        'visible' => Right::HasAdmin(Yii::$app->admin->id, 'factory')
                                    ],
                                    [
                                        'label'=>'Акции',
                                        'url'=>['/factories/stock'],
                                        'visible' => Right::HasAdmin(Yii::$app->admin->id, 'factories/stock')
                                    ],
                                    [
                                        'label'=>'Продукты',
                                        'url'=>['/factories/product'],
                                        'visible' => Right::HasAdmin(Yii::$app->admin->id, 'factories/product')
                                    ],
                                    [
                                        'label'=>'Ответы',
                                        'url'=>['/factories/stocks/answer'],
                                        'visible' => Right::HasAdmin(Yii::$app->admin->id, 'factories/stocks/answer')
                                    ],
                                ],
                                'visible' => Admin::showFactory(Yii::$app->admin->id)
                            ],

                            ['label'=>'Семинары',
                                'items'=>[
                                    [
                                        'label'=>'Семинары',
                                        'url'=>['/seminar'],
                                        'visible' => Right::HasAdmin(Yii::$app->admin->id, 'seminar')
                                    ],
                                    [
                                        'label'=>'Записи',
                                        'url'=>['/seminars/sign'],
                                        'visible' => Right::HasAdmin(Yii::$app->admin->id, 'seminars/sign')
                                    ],
                                    [
                                        'label'=>'Комментарии',
                                        'url'=>['/seminars/comment'],
                                        'visible' => Right::HasAdmin(Yii::$app->admin->id, 'seminars/comment')
                                    ],
                                ],
                                'visible' => Admin::showSeminar(Yii::$app->admin->id)
                            ],

                            ['label'=>'Подарки',
                                'items'=>[
                                    [
                                        'label'=>'Подарки',
                                        'url'=>['/present'],
                                        'visible' => Right::HasAdmin(Yii::$app->admin->id, 'present')
                                    ],
                                    [
                                        'label'=>'Поставщики',
                                        'url'=>['/presents/vendor'],
                                        'visible' => Right::HasAdmin(Yii::$app->admin->id, 'presents/vendor')
                                    ],
                                ],
                                'visible' => Admin::showPresent(Yii::$app->admin->id)
                            ],
                                    ],
                        'visible' => Admin::showContent(Yii::$app->admin->id)
                    ],
                    ['label'=>'Вакансии',
                        'items'=>[
                            [
                                'label'=>'Вакансии',
                                'url'=>['/vacancy'],
                                'visible' => Right::HasAdmin(Yii::$app->admin->id, 'vacancy')
                            ],
                            [
                                'label'=>'Комментарии',
                                'url'=>['/vacancies/comment'],
                                'visible' => Right::HasAdmin(Yii::$app->admin->id, 'vacancies/comment')
                            ],
                            [
                                'label'=>'Записи',
                                'url'=>['/vacancies/sign'],
                                'visible' => Right::HasAdmin(Yii::$app->admin->id, 'vacancies/sign')
                            ],
                        ],
                        'visible' => Admin::showVacancy(Yii::$app->admin->id)
                    ],
                    ['label' => 'Выход', 'url' => ['/auth/logout']],
                ],
            ]);
            NavBar::end();
        ?>

        <div class="container">
        <?=
        Breadcrumbs::widget([
            'homeLink' => [
                'label' => 'Презентации',
                'url' => ['/presentation'],
            ],
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]);
        ?>
            <?= $content ?>
        </div>
    </div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
