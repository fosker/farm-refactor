<?php

namespace company\controllers;

use Yii;

use yii\web\Controller;
use company\models\Admin;

class AuthController extends Controller
{
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            Yii::$app->user->logout();
        }

        $model = new Admin();
        $model->scenario = 'login';

        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goHome();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }


    public function actionResetPassword($key=null)
    {
        if (!Yii::$app->user->isGuest)
            return $this->goHome();

        $reset = new Admin(['scenario'=>'reset-password']);
        if(Yii::$app->request->post('email')) {
            $user = Admin::findByEmail(Yii::$app->request->post('email'));
            if($user) {
                $user->generatePasswordResetToken();
                return $this->render('confirm',
                    [
                        'title'=>'Фарма | Восстановление пароля',
                        'message'=>'Проверьте почту, мы выслали вам письмо со ссылкой на восстановление пароля.
                        Письмо должно прийти в течении 5 минут.',
                    ]
                );
            } else {
                $error = 'Пользователя с такой почтой не существует.';
            }
        }
        if($key)
        {
            $user = Admin::findByPasswordResetToken($key);
            if(!$user) {
                throw new \yii\web\ServerErrorHttpException;
            }
            if($reset->load(Yii::$app->request->post()) && $reset->validate()) {
                $user->setPassword($reset->password);
                $user->removePasswordResetToken();
                $user->save(false);
                return $this->redirect(['/login']);
            } else {
                return $this->render('reset', [
                    'model'=>$reset
                ]);
            }
        }
        return $this->render('enterEmail', [
            'error'=>$error
        ]);
    }
}