<?php

namespace rest\versions\v1\controllers;

use Yii;
use yii\base\Model;
use yii\filters\AccessControl;
use yii\web\UploadedFile;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;

use common\models\profile\UpdateRequest;
use common\models\profile\SetNotification;
use common\models\User;
use common\models\profile\Notification;
use rest\components\Controller;
use common\models\ContactForm;

class UserController extends Controller
{

    private $_user = null;

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
        return $this->_user();
    }

    public function actionUpdateProfile()
    {
        UpdateRequest::deleteAll(['user_id'=>$this->_user()->id]);

        $request = new UpdateRequest();
        $request->loadCurrentAttributes($this->_user());
        if ($request->load(Yii::$app->request->bodyParams,'') && $request->save())
            return ['success'=>true];
        return $request;
    }

    public function actionUpdatePhoto() {
        $model = $this->_user();
        $model->scenario = 'update-photo';

        if ($model->load(Yii::$app->getRequest()->getBodyParams(),'')) {
            $model->image = UploadedFile::getInstance($model, 'image');
            if ($model->validate()) {
                $model->saveImage();
                $model->save(false);
                return ['success'=>true];
            }
        }
        return $model;
    }

    public function actionUpdatePassword() {

        $model = $this->_user();
        $model->scenario = 'update-password';
        $model->password = '';

        if($model->load(Yii::$app->getRequest()->getBodyParams(),'') && $model->validate()) {
            // GENERATE PASSWORD HASH
            $model->setPassword($model->password);
            $model->save(false);
            return ['success'=>true];
        } else return $model;
    }

    public function actionLogout() {
        $this->_user()->logout();
    }

    /**
     * @return null|User
     */
    private function _user()
    {
        if($this->_user === null)
            $this->_user = User::findOne(Yii::$app->user->id);
        return $this->_user;
    }

    public function actionNotifications() {
        $notifications = SetNotification::find()
            ->select(['id','notification_id','value'])
            ->where(['user_id'=>Yii::$app->user->id])
            ->indexBy('id')
            ->all();

        if (Model::loadMultiple($notifications, Yii::$app->request->queryParams) && Model::validateMultiple($notifications)) {
            foreach ($notifications as $notify) {
                $notify->save(false);
            }
            return ['success'=>true];
        } else return $notifications;
    }

    public function actionGetNotifications() {
        return Notification::find()->asArray()->all();
    }

    public function actionSendMessage() {

        $message = new ContactForm();

        if ($message->load(Yii::$app->request->bodyParams,'')) {
            $message->user_id = $this->_user()->id;
            if($message->save()) {
                return ['success'=>true];
            }
        }
        return $message;

    }

}