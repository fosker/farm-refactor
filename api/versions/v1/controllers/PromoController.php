<?php

namespace rest\versions\v1\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

use backend\models\Param;
use common\models\shop\Present;

class PromoController extends Controller
{

    const ALLOW_IP = ['127.0.0.1'];
    const SHA_SALT = 'sakj0-asf';
    const CRON_TOKEN = 'asdj-khitv_g948-[4893';

    public function actionUsePromo($promo=null, $token=null) {
        if(!$present = Present::findByPromo($promo)){
            $message = 'Код уже был использован';
        }
        elseif($this->isValidToken($present, $token)) {
            $present->usePromo();
            $message = 'Код успешно использован';
        } else {
            $message = 'Системная ошибка.';
        }
        return $this->renderPartial('index', ['message'=>$message]);
    }

    protected function isValidToken($present, $token) {
        $validToken = sha1(
            $present->id.static::SHA_SALT.
            $present->user_id.static::SHA_SALT.
            $present->item_id.static::SHA_SALT.
            $present->count.static::SHA_SALT.
            $present->date_buy.static::SHA_SALT
        );
        return $validToken === $token;
    }

    public function actionCron($key) {
        if(($_SERVER['REMOTE_ADDR'] == $_SERVER['SERVER_ADDR'] || $_SERVER['REMOTE_ADDR'] == "127.0.0.1") && $key === static::CRON_TOKEN) {

            $presents = Present::findBoughtToday()->with('item.vendor')->asArray()->all();
            if(empty($presents)) return;
            $list = [];

            foreach($presents as $present) {

                $token = sha1(
                    $present['id'].static::SHA_SALT.
                    $present['user_id'].static::SHA_SALT.
                    $present['item_id'].static::SHA_SALT.
                    $present['count'].static::SHA_SALT.
                    $present['date_buy'].static::SHA_SALT
                );
                $list[$present['item']['vendor']['email']][] = ['code'=>$present['promo'],'token'=>$token];
            }

            foreach($list as $email=>$vendor) {

                Yii::$app->mailer->compose('@common/mail/promo-list', [
                    'vendor'=>$vendor,
                ])
                    ->setFrom(Param::getParam('email'))
                    ->setTo($email)
                    ->setSubject("Список промо-кодов за ".date("j.n.Y"))
                    ->send();
            }
        } else {
            throw new NotFoundHttpException();
        }
    }

}