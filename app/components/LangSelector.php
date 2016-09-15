<?php

namespace app\components;

use Yii;

use yii\web\Cookie;
use yii\base\Component;

/**
 * Component to select language of the site
 * Algorithm: use 'lang' value in url. If not set - use cookie 'lang' value. If not set - using auto-detect language. If can't - use default language 'ru'.
 * If final language value not isset in language map - use default language 'ru'
 */


class LangSelector extends Component
{


    public function init()
    {

        $languageMap = [
            'ru' => 'ru-RU',
            'en' => 'en-US',
        ];

        $request = Yii::$app->request;

        $browserLanguage = explode("-",explode(",",$_SERVER['HTTP_ACCEPT_LANGUAGE'])[0])[0];

        $language = isset($languageMap[$request->get('lang')]) ? $request->get('lang') :
            (isset($request->cookies['lang']) ? $request->cookies['lang']->value :
                (isset($browserLanguage) ? $browserLanguage : 'ru')
            );

        Yii::$app->response->cookies->add(new Cookie([
            'name' => 'lang',
            'value' => $language,
        ]));

        Yii::$app->language = $languageMap[$language];

        parent::init();
    }

}