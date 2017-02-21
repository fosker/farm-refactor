<?php

namespace rest\versions\v1\controllers;

use Yii;
use yii\rest\Controller;

use common\models\profile\Device;
use common\models\app\Android;
use common\models\app\Ios;


class VersionController extends Controller
{
    public function actionIndex()
    {
        $upToDate = false;
        $forceUpdate = false;

        $clientAndroidVersion = Yii::$app->request->get('app-android-version');
        $appAndroidLastVersion = Android::find()->orderBy(['version' => SORT_DESC])->one();

        $clientIosVersion = Yii::$app->request->get('app-ios-version');
        $appIosLastVersion = Ios::find()->orderBy(['version' => SORT_DESC])->one();

        if ($device = Device::findOne(['access_token' => Yii::$app->request->get('access-token')])) {
            if ($device->type == Device::TYPE_ANDROID && $clientAndroidVersion) {
                $device->version = $clientAndroidVersion;
                $device->save(false);
            }
            if ($device->type == Device::TYPE_IOS && $clientIosVersion) {
                $device->version = $clientIosVersion;
                $device->save(false);
            }
        }
        if ($clientAndroidVersion) {
            if ($appAndroidLastVersion->version <= $clientAndroidVersion) {
                $upToDate = true;
            }
            $forceUpdate = Android::find()
                ->where(['>', Android::tableName().'.version', $clientAndroidVersion])
                ->andWhere(['is_forced' => 1])
                ->exists();
            $message = $appAndroidLastVersion->message;
        }
        if ($clientIosVersion) {
            if ($appIosLastVersion->version <= $clientIosVersion) {
                $upToDate = true;
            }
            $forceUpdate = Ios::find()
                ->where(['>', Ios::tableName().'.version', $clientIosVersion])
                ->andWhere(['is_forced' => 1])
                ->exists();
            $message = $appIosLastVersion->message;
        }

        return [
            'message' => $message,
            'up_to_date' => $upToDate,
            'force_update' => $forceUpdate
        ];
    }
}
