<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;

use common\models\pharmbonus\Callback;
use common\models\Mailer;

class MainController extends Controller
{

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionCallback()
    {
        if(!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new Callback();

        if($model->load(Yii::$app->request->post()) && $model->validate()) {
            Mailer::sendCallback($model);
            Yii::$app->session->setFlash('success', 'Сообщение успешно отправлено! С вами скоро свяжутся');
            return $this->refresh();
        } else {
            return $this->render('callback', [
                'model' => $model,
            ]);
        }
    }

    public function actionTerms()
    {
        return $this->render('terms');
    }

}
