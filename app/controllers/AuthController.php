<?php

namespace app\controllers;

use Yii;

use yii\web\Controller;
use common\models\User;
use yii\helpers\ArrayHelper;
use common\models\location\City;
use common\models\location\Region;
use common\models\profile\Education;
use common\models\profile\Position;

class AuthController extends Controller
{

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            Yii::$app->user->logout();
        }

        $model = new User();
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

    public function actionSignupAgent()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new User();
        $model->scenario = 'signup-agent';

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->details = "Имя: $model->name; Образование: представитель; Телефон: $model->phone; Email: $model->email;
                               Регион: $model->region_agent; Фирма: $model->firm_agent;";
            $model->name = 'agent';
            $model->phone = 'agent';
            $model->sex = 'male';
            $model->pharmacy_id = '1';
            $model->education_id = '4';
            $model->region_id = '1';
            $model->city_id = '1';
            $model->firm_id = '1';

            if($model->save()) {
                return $this->goHome();
            }
        } else {
            return $this->render('signup-agent', [
                'model' => $model,
                'firms' => ArrayHelper::map(Firm::find()->asArray()->all(), 'id','name'),
                'regions' => ArrayHelper::map(Region::find()->asArray()->all(), 'id','name'),
                'cities' => ArrayHelper::map(City::find()->asArray()->all(), 'id','name'),
            ]);
        }

    }

    public function actionSignup()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new User();
        $model->scenario = 'signup';

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if($model->register()) {
                return $this->goHome();
            }
        } else {
            return $this->render('signup', [
                'model' => $model,
                'firms' => ArrayHelper::map(Firm::find()->asArray()->all(), 'id','name'),
                'regions' => ArrayHelper::map(Region::find()->asArray()->all(), 'id','name'),
                'cities' => ArrayHelper::map(City::find()->asArray()->all(), 'id','name'),
                'education' => ArrayHelper::map(Education::find()->asArray()->all(), 'id','name'),
                'pharmacies' => ArrayHelper::map(Pharmacy::find()
                    ->select(['id', new \yii\db\Expression("CONCAT(`name`, ' (', `address`,')') as name")])
                    ->asArray()->all(), 'id','name'),
                'positions' => ArrayHelper::map(Position::find()->asArray()->all(), 'id','name'),
            ]);
        }

    }

    public function actionResetPassword($key=null)
    {
        if (!Yii::$app->user->isGuest)
            return $this->goHome();

        $reset = new User(['scenario'=>'reset-password']);
        if(Yii::$app->request->post('email')) {
            $user = User::findByEmail(Yii::$app->request->post('email'));
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
            $user = User::findByPasswordResetToken($key);
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