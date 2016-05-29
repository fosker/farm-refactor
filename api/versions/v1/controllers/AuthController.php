<?php
namespace rest\versions\v1\controllers;

use Yii;
use yii\rest\Controller;

use backend\models\Param;
use common\models\profile\Device;
use common\models\User;
use common\models\user\Agent;
use common\models\user\Pharmacist;
use common\models\profile\LoginForm;
use common\models\profile\Type;

class AuthController extends Controller
{

    public function actionOptions()
    {
        Yii::$app->getResponse()->getHeaders()->set('Allow', implode(', ', ['GET', 'POST', 'PUT' ,'HEAD', 'OPTIONS','PATCH']));
    }

    /**
     * @api {post} /register-device Register new device
     * @apiName PostRegisterDevice
     * @apiGroup User
     * @apiPermission none
     *
     * @apiParam {Number=1,2} type Type of the device, 1 for Android and 2 for IOS
     *
     * @apiParam {String} [push_token] Push token for push-notifications
     *
     * @apiSuccess {String} device_id The id of device in the system
     *
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "device_id": "sdgsdfg1shsdhsdhs2dhsdgsd2fgshsdhs4dhsdh"
     *     }
     *
     * @apiError {json} ValidationFailed Wrong data entry.
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 422 Validation failed
     *     {
     *       "field": "type",
     *       "message": "Значение type неверно"
     *     }
     *
     */
    public function actionRegisterDevice() {
        $device = new Device();

        if($device->load(Yii::$app->request->post(),'') && $device->save()) {
            return ['device_id'=>$device->id];
        } else return $device;
    }

    /**
     * @api {post} /login Login to the system
     * @apiName PostLogin
     * @apiGroup User
     */
    public function actionLogin()
    {
        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post(),'') && $model->login()) {
            return [
                'access_token'=>Yii::$app->user->identity->getAccessTokenByDevice($model->device_id),
                'type_id'=>User::findOne(['login' => $model->login])->type_id
            ];
        }
        else return $model;
    }

    public function actionJoinPharmacist()
    {
        $model = new User(['scenario'=>'join']);
        $model->type_id = Type::TYPE_PHARMACIST;
        $user = new Pharmacist();
        if ($model->load(Yii::$app->request->post(),'') && $model->validate()) {
            if($user->load(Yii::$app->request->post(),'') && $user->validate()) {
                $model->register();
                $user->id = $model->id;
                $user->save(false);
                Yii::$app->mailer->compose('@common/mail/user-register-info', [
                    'user' => $model,
                ])
                    ->setFrom('pharmbonus@gmail.com')
                    ->setTo($model->email)
                    ->setSubject('Вы зарегистрировались в PharmBonus')
                    ->send();
                return ['success'=>true];
            } else return $user;
        } else return $model;
    }

    public function actionJoinAgent()
    {
        $model = new User(['scenario'=>'join']);
        $model->type_id = Type::TYPE_AGENT;
        $user = new Agent();
        if ($model->load(Yii::$app->request->post(),'') && $model->validate()) {
            if($user->load(Yii::$app->request->post(),'') && $user->validate()) {
                $model->register();
                $user->id = $model->id;
                $user->save(false);
                Yii::$app->mailer->compose('@common/mail/user-register-info', [
                    'user' => $model,
                ])
                    ->setFrom('pharmbonus@gmail.com')
                    ->setTo($model->email)
                    ->setSubject('Вы зарегистрировались в PharmBonus')
                    ->send();
                return ['success'=>true];
            } else return $user;
        } else return $model;
    }


    public function actionResetPassword()
    {
        $model = new User(['scenario'=>'reset-password']);

        if($model->load(Yii::$app->getRequest()->getBodyParams(),'') && $model->validate()) {
            return ['success'=>$model->resetPassword()];
        } else return $model;
    }

    public function actionSendResetToken()
    {
        if(!$user = User::FindByEmail(Yii::$app->getRequest()->getBodyParams()['email']))
            throw new \yii\web\NotFoundHttpException('Пользователь с такой почтой не существует.');

        $user->generatePasswordResetToken();
        $user->save(false);

        Yii::$app->mailer->compose('@common/mail/repair-user-code', [
            'token'=>$user->reset_token,
        ])
            ->setFrom("pharmbonus@gmail.com")
            ->setTo($user->email)
            ->setSubject("Восстановление доступа")
            ->send();
        return ['success'=>true];
    }

    public function actionCheckResetToken($reset_token)
    {
        return ['valid'=>(bool)User::findByPasswordResetToken($reset_token)];
    }

}