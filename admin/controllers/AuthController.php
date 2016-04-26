<?php

namespace backend\controllers;

use Yii;

use backend\models\Admin;
use yii\web\Controller;

class AuthController extends Controller
{

    public function actionLogin() {
        if (!Yii::$app->admin->isGuest) {
            Yii::$app->admin->logout();
        }
        $model = new Admin(['scenario'=>'login']);
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect(['/main']);
        } else {
            return $this->renderPartial('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionLogout() {
        Yii::$app->admin->logout();
        return $this->goHome();
    }

    public function actionResetPassword($key=null) {
        if (!Yii::$app->admin->isGuest)
            return $this->redirect(['/main']);

        $reset  = new Admin(['scenario'=>'reset_password']);
        if($_POST['email']) {
            $admin = Admin::findByEmail(Yii::$app->request->post('email'));
            if($admin) {
                $admin->generatePasswordResetToken();
                return $this->renderPartial('confirm', 
                    [
                        'title'=>'Фарма | Восстановление пароля',
                        'message'=>'Проверьте почту, мы выслали вам письмо со ссылкой на восстановление пароля.
                        Письмо должно прийти в течении 5 минут.',
                    ]
                );
            } else {
                $error = 'Пользователя с такой почтой не существует.';
            }
        } elseif($reset->load(Yii::$app->request->post()) && $reset->validate() && $key) {
            $admin = Admin::findByPasswordResetToken($key);
            if($admin) {
                $admin->setPassword($reset->password);
                $admin->removePasswordResetToken();
                $admin->save(false);
                Yii::$app->session->setFlash('LoginAdminMessage', 'Пароль успешно изменен.');
                return $this->redirect(['/auth/login']);
            } else {
                throw new \yii\web\ServerErrorHttpException;
            }
        } elseif($key) {
            $admin = Admin::findByPasswordResetToken($key);
            if($admin) {
                return $this->renderPartial('reset', [
                    'model'=>$reset
                ]);
            } else {
                throw new \yii\web\ServerErrorHttpException;
            }
        }
        return $this->renderPartial('enterEmail', [
            'error'=>$error
        ]);
    }
}