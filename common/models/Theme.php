<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "themes".
 *
 * @property integer $id
 * @property integer $company_id
 * @property string $email
 * @property string $description
 * @property string $title
 * @property string $form_id
 */
class Theme extends \yii\db\ActiveRecord
{

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
            [['company_id', 'email', 'description','title'], 'required'],
            [['company_id', 'form_id'], 'integer'],
            [['description', 'title'], 'string'],
            [['email'], 'string', 'max' => 255],
            ['email', 'email']
        ];
    }

    public function fields()
    {
        return [
            'id',
            'title',
            'company',
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
            'company_id' => 'Компания Автор',
            'email' => 'Email',
            'description' => 'Описание',
            'form_id' => 'Форма'
        ];
    }

    public static function getTypesList()
    {
        $free = [0 => 'Свободная тема'];
        $array = array_merge($free, ArrayHelper::map(Form::find()->all(), 'id', 'title'));
        return $array;
    }

    public function getCompany()
    {
        return $this->hasOne(Company::className(),['id'=>'company_id']);
    }

    public function getForm()
    {
        return $this->hasOne(Form::className(),['id'=>'form_id']);
    }
}
