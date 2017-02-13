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

        $appAndroidVersion = Yii::$app->request->get('app-android-version');
        $appAndroidLastVersion = Android::find()->orderBy(['version' => SORT_DESC])->one();

        $appIosVersion = Yii::$app->request->get('app-ios-version');
        $appIosLastVersion = Ios::find()->orderBy(['version' => SORT_DESC])->one();

        if ($device = Device::findOne(['access_token' => Yii::$app->request->get('access-token')])) {
            if ($device->type == Device::TYPE_ANDROID && $appAndroidVersion) {
                $device->version = $appAndroidVersion;
                $device->save(false);
            }
            if ($device->type == Device::TYPE_IOS && $appIosVersion) {
                $device->version = $appIosVersion;
                $device->save(false);
            }
        }
        if ($appAndroidVersion) {
            if ($appAndroidLastVersion->version == $appAndroidVersion) {
                $upToDate = true;
            }
            $forceUpdate = Android::find()
                ->where(['>', Android::tableName().'.version', $appAndroidVersion])
                ->andWhere(['is_forced' => 1])
                ->exists();
            $message = $appAndroidLastVersion->message;
        }
        if ($appIosVersion) {
            if ($appIosLastVersion->version == $appIosVersion) {
                $upToDate = true;
            }
            $forceUpdate = Ios::find()
                ->where(['>', Ios::tableName().'.version', $appIosVersion])
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
