<?php

namespace test\controllers;

use Yii;
use yii\base\Controller;


class MainController extends Controller
{
    public function actionIndex()
    {
        return $this->renderPartial('index');
    }
}