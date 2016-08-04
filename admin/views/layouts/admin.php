<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use backend\assets\AppAsset;
use yii\widgets\Breadcrumbs;
use backend\models\admin\Right;
use backend\models\Admin;
use common\models\profile\AgentUpdateRequest;
use common\models\profile\PharmacistUpdateRequest;
use kartik\nav\NavX;
/**
 * @var $content string
 */

AppAsset::register($this);

$count_agents = AgentUpdateRequest::find()->count();
$count_pharmacists = PharmacistUpdateRequest::find()->count();
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
                                'label' => 'Компании',
                                'items' => [
                                    [
                                        'label' => 'Компании',
                                        'url' => ['/company'],
                                        'visible' => Right::HasAdmin(Yii::$app->admin->id, 'company')
                                    ],
                                    [
                                        'label' => 'Аптеки',
                                        'url' => ['/pharmacy'],
                                        'visible' => Right::HasAdmin(Yii::$app->admin->id, 'pharmacy')
                                    ],
                                ]
                            ],
                            [
                                'label' => 'Фабрики',
                                'items' => [
                                    [
                                        'label' => 'Фабрики',
                                        'url' => ['/factory'],
                                        'visible' => Right::HasAdmin(Yii::$app->admin->id, 'factory')
                                    ],
                                    [
                                        'label' => 'Продукты',
                                        'url' => ['/factories/product'],
                                        'visible' => Right::HasAdmin(Yii::$app->admin->id, 'factories/product')
                                    ],
                                ]
                            ],
                            [
                                'label'=>'Образование',
                                'url'=>['/education'],
                                'visible' => Right::HasAdmin(Yii::$app->admin->id, 'education')
                            ],
                            [
                                'label'=>'Типы пользователей',
                                'url'=>['/type'],
                                'visible' => Right::HasAdmin(Yii::$app->admin->id, 'type')
                            ],
                            [
                                'label'=>'Должности',
                                'url'=>['/position'],
                                'visible' => Right::HasAdmin(Yii::$app->admin->id, 'position')
                            ],
                            [
                                'label'=>'Поставщики',
                                'url'=>['/presents/vendor'],
                                'visible' => Right::HasAdmin(Yii::$app->admin->id, 'presents/vendor')
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
                                'label' => 'Представители',
                                'items' => [
                                    [
                                        'label' => 'Представители',
                                        'url' => ['/user/agents'],
                                    ],
                                    [
                                        'label' => 'Ожидают обновления' . Html::tag('span', $count_agents, ['class' => 'badge']),
                                        'url' => ['/users/agent/update-request'],
                                    ],
                                ],
                                'visible' => Right::HasAdmin(Yii::$app->admin->id, 'user')
                            ],
                            [
                                'label' => 'Фармацевты',
                                'items' => [
                                    [
                                        'label' => 'Фармацевты',
                                        'url' => ['/user/pharmacists'],
                                    ],
                                    [
                                        'label' => 'Ожидают обновления' . Html::tag('span', $count_pharmacists, ['class' => 'badge']),
                                        'url' => ['/users/pharmacist/update-request'],
                                    ],
                                ],
                                'visible' => Right::HasAdmin(Yii::$app->admin->id, 'user')
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
                                        'label' => 'Все оповещения',
                                        'url' => ['/users/pushes'],
                                        'visible' => Right::HasAdmin(Yii::$app->admin->id, 'users/push-groups') ||
                                            Right::HasAdmin(Yii::$app->admin->id, 'users/push-users')
                                    ],
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
                            [
                                'label' => 'Администраторы производителей',
                                'url' => ['/users/factory-admin'],
                                'visible' => Right::HasAdmin(Yii::$app->admin->id, 'users/factory-admin')
                            ],
                            [
                                'label' => 'Администраторы компаний',
                                'url' => ['/users/company-admin'],
                                'visible' => Right::HasAdmin(Yii::$app->admin->id, 'users/company-admin')
                            ],
                        ],
                        'visible' => Admin::showUser(Yii::$app->admin->id)
                    ],
                    ['label' => 'Основное меню',
                        'items' => [
                            [
                                'label' => 'Баннеры',
                                'url' => ['/banner'],
                                'visible' => Right::HasAdmin(Yii::$app->admin->id, 'banner')
                            ],
                            [
                                'label' => 'Новости',
                                'url' => ['/news'],
                                'visible' => Right::HasAdmin(Yii::$app->admin->id, 'news')
                            ],
                            [
                                'label' => 'Видео',
                                'url' => ['/video'],
                                'visible' => Right::HasAdmin(Yii::$app->admin->id, 'video')
                            ],
                            ['label'=>'Темы',
                                'items'=>[
                                    [
                                        'label'=>'Темы',
                                        'url'=>['/theme'],
                                        'visible' => Right::HasAdmin(Yii::$app->admin->id, 'theme')
                                    ],
                                    [
                                        'label'=>'Формы',
                                        'url'=>['/form'],
                                        'visible' => Right::HasAdmin(Yii::$app->admin->id, 'form')
                                    ],
                                ],
                                'visible' => Admin::showTheme(Yii::$app->admin->id)
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
                                        'label'=>'Ответы',
                                        'url'=>['/presentations/answer'],
                                        'visible' => Right::HasAdmin(Yii::$app->admin->id, 'presentations/answer')
                                    ],
                                ],
                                'visible' => Admin::showPresentation(Yii::$app->admin->id)
                            ],
                            ['label'=>'Акции',
                                'items'=>[
                                    [
                                        'label'=>'Акции',
                                        'url'=>['/stock'],
                                        'visible' => Right::HasAdmin(Yii::$app->admin->id, 'stock')
                                    ],
                                    [
                                        'label'=>'Ответы',
                                        'url'=>['/stocks/answer'],
                                        'visible' => Right::HasAdmin(Yii::$app->admin->id, 'stocks/answer')
                                    ],
                                ],
                                'visible' => Admin::showStock(Yii::$app->admin->id)
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
                                ],
                                'visible' => Admin::showSeminar(Yii::$app->admin->id)
                            ],

                                [
                                    'label'=>'Подарки',
                                    'url'=>['/present'],
                                    'visible' => Right::HasAdmin(Yii::$app->admin->id, 'present')
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
                                'label'=>'Записи',
                                'url'=>['/vacancies/sign'],
                                'visible' => Right::HasAdmin(Yii::$app->admin->id, 'vacancies/sign')
                            ],
                        ],
                        'visible' => Admin::showVacancy(Yii::$app->admin->id)
                    ],
                    ['label'=>'Статистика',
                        'items'=>[
                            [
                                'label'=>'Пользователи в городах',
                                'url'=>['/statistics/in-cities'],
                                'visible' => Right::HasAdmin(Yii::$app->admin->id, 'statistics')
                            ],
                            [
                                'label'=>'Пользователи в аптеках',
                                'url'=>['/statistics/in-pharmacies'],
                                'visible' => Right::HasAdmin(Yii::$app->admin->id, 'statistics')
                            ],
                        ],
                        'visible' => Right::HasAdmin(Yii::$app->admin->id, 'statistics')
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
