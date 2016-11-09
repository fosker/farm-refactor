<?php

namespace admin\models;

use Yii;

/**
 * This is the model class for table "employee".
 *
 * @property integer $id
 * @property string $name
 * @property string $surname
 * @property string $phone
 * @property string $photo
 * @property integer $department_id
 * @property integer $position_id
 */
class Employee extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'employee';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'surname', 'phone', 'department_id', 'position_id'], 'required'],
            [['department_id', 'position_id'], 'integer'],
            [['name', 'surname'], 'string', 'max' => 100],
            [['phone'], 'string', 'max' => 15],
            [['photo'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Имя',
            'surname' => 'Фамилия',
            'phone' => 'Телефон',
            'photo' => 'Фото',
            'department_id' => 'Отдел',
            'position_id' => 'Должность',
            'fullname' => 'Сотрудник'
        ];
    }

    public function getDepartment()
    {
        return $this->hasOne(Department::className(),['id'=>'department_id']);
    }

    public function getPosition()
    {
        return $this->hasOne(Position::className(),['id'=>'position_id']);
    }

    public function getFullName()
    {
        return $this->name . ' ' . $this->surname;
    }

    public function afterDelete()
    {
        Rate::deleteAll(['employee_id'=>$this->id]);
        parent::afterDelete();
    }
}
