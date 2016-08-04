<?php

namespace common\models\location;
use Yii;
use common\models\user\Pharmacist;
use common\models\location\City;
use common\models\company\Pharmacy;
/**
 * This is the model class for table "regions".
 *
 * @property integer $id
 * @property string $name
 */

class Region extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'regions';
    }

    /**
     * @inheritdoc
     */
    public function rules()
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
            'name' => 'Название региона'
        ];
    }

    public function getCities()
    {
        return $this->hasMany(City::className(), ['region_id' => 'id'])->orderBy('name');
    }

    public function getUserCount()
    {
        return Pharmacist::find()->joinWith('pharmacy')
            ->join('LEFT JOIN', City::tableName(),
                Pharmacy::tableName().'.city_id = '.City::tableName().'.id')
            ->join('LEFT JOIN', static::tableName(),
                static::tableName().'.id = '.City::tableName().'.region_id')
            ->andWhere([static::tableName().'.id' => $this->id])
            ->count();
    }

    public function afterDelete()
    {
        parent::afterDelete();
        City::deleteAll(['id'=>$this->id]);
    }

}
