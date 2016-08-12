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

    public static function sendVerificationMailToUser($user, $verified)
    {
        Yii::$app->mailer->compose($verified ? '@common/mail/user-status-verified' : '@common/mail/user-status-banned', [
            'user' => $user
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

}