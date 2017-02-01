<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "themes".
 *
 * @property integer $id
 * @property integer $factory_id
 * @property string $email
 * @property string $description
 * @property string $title
 * @property string $form_id
 * @property string $forList
 * @property integer $status
 */
class Theme extends \yii\db\ActiveRecord
{
    const STATUS_HIDDEN = 0;
    const STATUS_AVAILABLE = 1;
    const STATUS_NOT_AVAILABLE = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'themes';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['factory_id', 'email', 'description','title', 'status'], 'required'],
            [['factory_id', 'form_id', 'status'], 'integer'],
            [['description', 'title', 'forList'], 'string'],
            [['email'], 'string', 'max' => 255],
            ['email', 'email']
        ];
    }

    public function fields()
    {
        return [
            'id',
            'title',
            'factory',
            'status'
        ];
    }

    public function extraFields()
    {
        return [
            'description',
            'form'
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Заголовок',
            'factory_id' => 'Компания Автор',
            'email' => 'Email',
            'description' => 'Описание',
            'form_id' => 'Форма',
            'forList' => 'Показывать списку',
            'status' => 'Статус'
        ];
    }

    public static function getTypesList()
    {
        $free = [0 => 'Свободная тема'];
        $array = array_merge($free, ArrayHelper::map(Form::find()->all(), 'id', 'title'));
        return $array;
    }

    public function getFactory()
    {
        return $this->hasOne(Factory::className(),['id'=>'factory_id']);
    }

    public function getForm()
    {
        return $this->hasOne(Form::className(),['id'=>'form_id']);
    }

    public static function getStatusList()
    {
        return [static::STATUS_AVAILABLE=>'доступна',static::STATUS_NOT_AVAILABLE=>'не доступна',static::STATUS_HIDDEN=>'не видна'];
    }
}
