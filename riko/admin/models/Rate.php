<?php

namespace admin\models;

use Yii;

/**
 * This is the model class for table "employee_rate".
 *
 * @property integer $id
 * @property integer $employee_id
 * @property integer $criterion_id
 * @property integer $rate
 * @property string $date
 * @property string $comment
 */
class Rate extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'employee_rate';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['employee_id', 'criterion_id', 'rate'], 'required'],
            [['employee_id', 'criterion_id'], 'integer'],
            [['date'], 'safe'],
            [['rate'], 'number'],
            [['comment'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'employee_id' => 'Сотрудник',
            'criterion_id' => 'Критерий',
            'rate' => 'Оценка',
            'date' => 'Дата оценки',
            'comment' => 'Комментарий',
        ];
    }

    public function getCriterion()
    {
        return $this->hasOne(Criterion::className(),['id'=>'criterion_id']);
    }

    public function getEmployee()
    {
        return $this->hasOne(Employee::className(),['id'=>'employee_id']);
    }
}
