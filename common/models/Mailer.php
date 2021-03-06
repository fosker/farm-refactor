<?php

namespace common\models;

use Yii;

class Mailer
{

    public static function sendRegisterMail($user)
    {
        Yii::$app->mailer->compose($user->type_id == 1 ? '@common/mail/user-register-pharmacist' : '@common/mail/user-register-agent', [
            'user'=>$user,
        ])
            ->setFrom("info@pharmbonus.by")
            ->setTo('pharmbonus@gmail.com')
            ->setSubject('Новый пользователь!')
            ->send();
    }

    public static function sendVerificationMailToUser($user, $password)
    {
        Yii::$app->mailer->compose('@common/mail/user-status-verified', [
            'user' => $user,
            'password' => $password
        ])
            ->setFrom('info@pharmbonus.by')
            ->setTo($user->email)
            ->setSubject('Регистрация в мобильном приложении ФармБонус')
            ->send();
    }

    public static function sendRepairCode($user)
    {
        Yii::$app->mailer->compose('@common/mail/repair-user-code', [
            'token'=>$user->reset_token,
        ])
            ->setFrom("info@pharmbonus.by")
            ->setTo($user->email)
            ->setSubject("Восстановление доступа")
            ->send();
    }

    public static function sendThemeAnswer($attach, $email)
    {
        Yii::$app->mailer->compose('@common/mail/theme-answer')
            ->setFrom("feedback@pharmbonus.by")
            ->setTo($email)
            ->setSubject("Новый ответ")
            ->attach($attach)
            ->send();
    }

    public static function sendStockReply($attach, $reply)
    {
        Yii::$app->mailer->compose('@common/mail/stock-reply', [
            'reply' => $reply
        ])
            ->setFrom("feedback@pharmbonus.by")
            ->setTo($reply->stock->email)
            ->setSubject("Новый ответ на акцию")
            ->attach($attach)
            ->send();
    }

    public static function sendPresent($user, $item, $present)
    {
        Yii::$app->mailer->compose('@common/mail/buy-present', [
            'user'=>$user,
            'item'=>$item,
            'present'=>$present
        ])
            ->setFrom("bonmarket@pharmbonus.by")
            ->setTo(["pharmbonus@gmail.com", $item->vendor->email])
            ->setSubject('Новая покупка!')
            ->send();
    }

    public static function sendVacancy($user, $vacancy, $entry)
    {
        Yii::$app->mailer->compose('@common/mail/sign-up-vacancy', [
            'vacancy'=>$vacancy,
            'user'=>$user,
            'entry'=>$entry,
        ])
            ->setFrom("hr@pharmbonus.by")
            ->setTo($vacancy->email)
            ->setSubject('Новая заявка на вакансию!')
            ->send();
    }

    public static function sendSeminar($user, $seminar, $entry)
    {
        Yii::$app->mailer->compose('@common/mail/sign-up-seminar', [
            'user'=>$user,
            'seminar'=>$seminar,
            'entry'=> $entry,
        ])
            ->setFrom("event@pharmbonus.by")
            ->setTo($seminar->email)
            ->setSubject('Новая запись на семинар!')
            ->send();
    }

    public static function sendCallback($user)
    {
        Yii::$app->mailer->compose('@common/mail/user-callback', [
            'user'=>$user,
        ])
            ->setFrom("info@pharmbonus.by")
            ->setTo("pharmbonus@gmail.com")
            ->setSubject('Новая заявка')
            ->send();
    }

}