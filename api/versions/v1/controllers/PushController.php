<?php

namespace rest\versions\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;

use common\models\Push;
use common\models\company\Users as CompanyUsers;
use common\models\pharmbonus\Users;

class PushController extends Controller
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

    public function actionIndex()
    {
        return new ActiveDataProvider([
            'query' => Push::getForCurrentUser(),
        ]);
    }

    public function actionView($id)
    {
        return Push::getOneForCurrentUser($id);
    }

    public function actionAddView()
    {
        if($id = Yii::$app->request->post('push_id')) {
            $push = Push::findOne($id);
            $push->views+=1;
            $push->save(false);
            if($push->companyPushes) {
                $pushForUser = CompanyUsers::findOne(['push_id' => $id, 'user_id' => Yii::$app->user->id]);
                $pushForUser->isViewed = true;
                $pushForUser->save(false);
            } elseif($push->pharmPushes) {
                $pushForUser = Users::findOne(['push_id' => $id, 'user_id' => Yii::$app->user->id]);
                $pushForUser->isViewed = true;
                $pushForUser->save(false);
            }
            return ['success' => true];
        } else {
            throw new BadRequestHttpException;
        }
    }

    public function actionRead()
    {
        if($id = Yii::$app->request->post('push_id')) {
            $push = Push::findOne($id);
            $push->save(false);
            if($push->companyPushes) {
                $pushForUser = CompanyUsers::findOne(['push_id' => $id, 'user_id' => Yii::$app->user->id]);
                $pushForUser->isRead = true;
                $pushForUser->save(false);
            } elseif($push->pharmPushes) {
                $pushForUser = Users::findOne(['push_id' => $id, 'user_id' => Yii::$app->user->id]);
                $pushForUser->isRead = true;
                $pushForUser->save(false);
            }
            return ['success' => true];
        } else {
            throw new BadRequestHttpException;
        }
    }

    public function actionDelete($id)
    {
        if($push = Push::findOne($id)) {
            if($push->companyPushes) {
                $pushForUser = CompanyUsers::findOne(['push_id' => $id, 'user_id' => Yii::$app->user->id]);
                $pushForUser->delete();
                return ['success' => 'true'];
            } elseif($push->pharmPushes) {
                $pushForUser = Users::findOne(['push_id' => $id, 'user_id' => Yii::$app->user->id]);
                $pushForUser->delete();
                return ['success' => 'true'];
            } else {
                throw new BadRequestHttpException;
            }
        }
    }
}