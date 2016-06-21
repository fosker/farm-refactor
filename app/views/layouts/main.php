<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
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
    <div class="zagluska">
        <?= Html::img(Yii::getAlias('@images/zaglush.png'), ['class' => 'img-responsive']);?>

        <?php if(!Yii::$app->user->isGuest) {
            echo "<div class='col-md-3'>";
            $this->beginContent('../web/views/layouts/sidebar.php');
            $this->endContent();
            echo "</div>";
        }?>
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?php if(!Yii::$app->user->isGuest) {
            echo "<div class='col-md-9'>";
            echo $content;
            echo "</div>";
        } else
            echo $content;?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left"><?=Html::a("Пользовательское соглашение", ['/terms'])?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
