<?php

use yii\helpers\Html;
use yii\bootstrap\NavBar;
use backend\assets\AppAsset;


$this->title = $title;

AppAsset::register($this);

$this->beginPage() ?>
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
                'brandLabel' => 'Фарма',
                'brandUrl' => Yii::$app->homeUrl,
                'options' => [
                    'class' => 'navbar-inverse navbar-fixed-top',
                ],
            ]);
            NavBar::end();
        ?>

        <div class="container">
            <div class="row">
		        <div class="col-md-8 col-md-offset-2 text-center">
					<p><?=$message ?></p>
		        </div>
		    </div>
        </div>
    </div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>