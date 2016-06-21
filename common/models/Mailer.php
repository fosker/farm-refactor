<?php

namespace common\models;

use Yii;

class Mailer
{

    public static function sendRegisterMail($user)
    {
        Yii::$app->mailer->compose('@common/mail/user-register', [
            'name'=>$user->name,
            'login'=>$user->login,
            'email'=>$user->email,
        ])
            ->setFrom("pharmbonus@gmail.com")
            ->setTo("pharmbonus@gmail.com")
            ->setSubject('Новый пользователь!')
            ->send();
    }

    public static function sendRegisterMailToUser($user)
    {
        Yii::$app->mailer->compose('@common/mail/user-register-info', [
            'user' => $user,
        ])
            ->setFrom('pharmbonus@gmail.com')
            ->setTo($user->email)
            ->setSubject('Вы зарегистрировались в PharmBonus')
            ->send();
    }

    public static function sendRepairCode($user)
    {
        Yii::$app->mailer->compose('@common/mail/repair-user-code', [
            'token'=>$user->reset_token,
        ])
            ->setFrom("pharmbonus@gmail.com")
            ->setTo($user->email)
            ->setSubject("Восстановление доступа")
            ->send();
    }

}