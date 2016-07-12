<?php

namespace rest\versions\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\rest\Controller;

use common\models\Theme;
use common\models\theme\Reply;
use yii\web\UploadedFile;

class ThemeController extends Controller
{

    public function behaviors()
    {
        return [
            'authenticator' => [
                'class' => CompositeAuth::className(),
                'authMethods' => [
                    HttpBearerAuth::className(),
                    QueryParamAuth::className(),
                ],
            ],
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

    public function actionCompany($company_id)
    {
        return new ActiveDataProvider([
            'query' => Theme::find()->where(['company_id' => $company_id]),
        ]);
    }

    public function actionView($id)
    {
        return  Theme::find()->where(['id' => $id])->one();
    }


    public function actionSend()
    {
        $reply = new Reply();

        if($reply->load(Yii::$app->request->post(),'')) {
            $reply->image = UploadedFile::getInstance($reply, 'image');
            $reply->user_id = Yii::$app->user->id;
            if ($reply->validate()) {
                $reply->saveImage();
                $reply->save(false);
                return ['success'=>true];
            }
        }
        return $reply;
    }

}