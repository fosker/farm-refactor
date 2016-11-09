<?php

namespace admin\models;

use Yii;

/**
 * This is the model class for table "criterion".
 *
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property string $abbr
 * @property integer $type
 * @property integer $min
 * @property integer $max
 * @property integer $step
 * @property integer $cash_multiplier
 */
class Criterion extends \yii\db\ActiveRecord
{
    const TYPE_POSITIVE = 1;
    const TYPE_NEGATIVE = 2;
    const TYPE_DOUBLE = 3;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'criterion';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'description', 'abbr', 'type', 'min', 'max', 'step', 'cash_multiplier'], 'required'],
            [['description'], 'string'],
            [['type', 'min', 'max'], 'integer'],
            [['step', 'cash_multiplier'], 'number'],
            [['name'], 'string', 'max' => 100],
            [['abbr'], 'string', 'max' => 2],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'description' => 'Описание',
            'abbr' => 'Аббревиатура',
            'type' => 'Тип',
            'min' => 'Мин',
            'max' => 'Макс',
            'step' => 'Шаг',
            'cash_multiplier' => 'Денежный множитель'
        ];
    }

    public function getTypes()
    {
        $values = [
            self::TYPE_POSITIVE => '+',
            self::TYPE_NEGATIVE => '-',
            self::TYPE_DOUBLE => '+/-',
        ];
        if(isset($values[$this->type])) {
            return $values[$this->type];
        }
    }

    public function getCustomName()
    {
        if ($this->type == static::TYPE_POSITIVE){
            return $this->name . ' (+' . $this->cash_multiplier . ')';
        }
        if ($this->type == static::TYPE_NEGATIVE){
            return $this->name . ' (-' . $this->cash_multiplier . ')';
        }
        if ($this->type == static::TYPE_DOUBLE){
            return $this->name . ' (+/-' . $this->cash_multiplier . ')';
        }
    }

    public function afterDelete()
    {
        Rate::deleteAll(['criterion_id'=>$this->id]);
        parent::afterDelete();
    }
}
