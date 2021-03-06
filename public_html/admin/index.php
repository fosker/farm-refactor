<?php
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

error_reporting(E_ERROR);


require(__DIR__ . '/../../vendor/autoload.php');
require(__DIR__ . '/../../vendor/yiisoft/yii2/Yii.php');
require(__DIR__ . '/../../common/config/bootstrap.php');
require(__DIR__ . '/../../admin/config/bootstrap.php');

$config = yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../../common/config/main.php'),
    require(__DIR__ . '/../../admin/config/main.php')
);

Yii::$classMap['pPie'] = __DIR__ . '/../../admin/components/pChart/class/pPie.class.php';
Yii::$classMap['pData'] = __DIR__ . '/../../admin/components/pChart/class/pData.class.php';
Yii::$classMap['pDraw'] = __DIR__ . '/../../admin/components/pChart/class/pDraw.class.php';
Yii::$classMap['pImage'] = __DIR__ . '/../../admin/components/pChart/class/pImage.class.php';


$application = new yii\web\Application($config);
$application->run();
