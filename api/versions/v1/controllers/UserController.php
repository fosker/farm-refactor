<?php

namespace rest\versions\v1\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\UploadedFile;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;

use common\models\profile\PharmacistUpdateRequest;
use common\models\profile\AgentUpdateRequest;
use common\models\User;
use common\models\ContactForm;
use rest\components\Controller;


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

    public function actionUpdateProfileAgent()
    {
        AgentUpdateRequest::deleteAll(['agent_id'=>$this->_user()->id]);
        $request = new AgentUpdateRequest();
        $request->loadCurrentAttributes($this->_user());
        if ($request->load(Yii::$app->request->bodyParams,'') && $request->save()) {
            return ['success'=>true];
        }
        return $request;
    }

    public function actionUpdateProfilePharmacist()
    {
        PharmacistUpdateRequest::deleteAll(['pharmacist_id'=>$this->_user()->id]);
        $request = new PharmacistUpdateRequest();
        $request->loadCurrentAttributes($this->_user());
        if ($request->load(Yii::$app->request->bodyParams,'') && $request->save()) {
            return ['success'=>true];
        }
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