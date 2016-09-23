<?php

Yii::setAlias('common', dirname(__DIR__));
Yii::setAlias('backend', dirname(dirname(__DIR__)) . '/admin');
Yii::setAlias('rest', dirname(dirname(__DIR__)) . '/api');
Yii::setAlias('factory', dirname(dirname(__DIR__)) . '/factory');
Yii::setAlias('company', dirname(dirname(__DIR__)) . '/company');
Yii::setAlias('app', dirname(dirname(__DIR__)) . '/app');

Yii::setAlias('uploads', dirname(dirname(__DIR__)) . '/public_html/uploads');

Yii::setAlias('uploads_view', 'http://pharmbonus.by/uploads');

Yii::setAlias('temp', 'http://farm.loc/admin/temp');

Yii::setAlias('images', 'http://pharmbonus.by/img');
Yii::setAlias('admin', 'http://admin.pharmbonus.by');
