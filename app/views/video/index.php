<?php

use yii\helpers\Html;
use yii\widgets\ListView;

$this->title = "Новости";
?>

<div class="site-videos">

    <h1 class="text-center"><?= Html::encode($this->title) ?></h1>

    <?php echo ListView::widget([
        'dataProvider' => $dataProvider,
        'itemView' => 'video_item.php',
    ]) ?>

</div>
