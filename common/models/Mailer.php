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
            ->setFrom("info@pharmbonus.by")
            ->setTo('pharmbonus@gmail.com')
            ->setSubject('Новый пользователь!')
            ->send();
    }

    public static function sendVerificationMailToUser($user, $verified)
    {
        Yii::$app->mailer->compose($verified ? '@common/mail/user-status-verified' : '@common/mail/user-status-banned', [
        ])
            ->setFrom('info@pharmbonus.by')
            ->setTo($user->email)
            ->setSubject($verified ? 'Поздравляем Вас с прохождением верификации в ФармБонус!' :
            'К сожалению, нам не удалось верифицировать Ваш аккаунт.')
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

    public static function sendThemeAnswer($attach, $email)
    {
        Yii::$app->mailer->compose('@common/mail/theme-answer')
            ->setFrom("pharmbonus@gmail.com")
            ->setTo($email)
            ->setSubject("Новый ответ")
            ->attach($attach)
            ->send();
    }

}