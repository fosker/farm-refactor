<?php

namespace factory\controllers;

use Yii;
use yii\web\Controller;
use factory\models\Admin;
use yii\filters\AccessControl;
use yii\web\UploadedFile;

class ProfileController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $model = Admin::findOne(Yii::$app->user->id);
        $model->scenario = 'update';
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->image = UploadedFile::getInstance($model, 'image');
            $model->saveImage();
            $model->save(false);
            return $this->redirect(['index']);
        } else {
            return $this->render('index', [
                'model' => $model,
            ]);
        }
    }

    public function actionUpdatePassword()
    {
        $model = new Admin();
        $model->scenario = 'update-password';

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $user = Admin::findOne(Yii::$app->user->id);
            $user->setPassword($model->password);
            $user->save(false);
            return $this->redirect(['/profile']);
        } else {
            return $this->render('update-password', [
                'model' => $model,
            ]);
        }
    }

}