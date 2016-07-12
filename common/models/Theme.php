<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "themes".
 *
 * @property integer $id
 * @property integer $company_id
 * @property string $email
 * @property string $description
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
            [['company_id', 'email', 'description'], 'required'],
            [['company_id'], 'integer'],
            [['description', 'title'], 'string'],
            [['email'], 'string', 'max' => 255],
            ['email', 'email']
        ];
    }

    public function fields()
    {
        return [
            'id','title','description','company'
        ];
    }

    public function extraFields()
    {
        return [

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
        ];
    }

    public function getCompany()
    {
        return $this->hasOne(Company::className(),['id'=>'company_id']);
    }
}
