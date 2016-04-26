<?php

namespace common\models\agency;
use common\models\agency\Pharmacy;
use Yii;

/**
 * This is the model class for table "firms".
 *
 * @property integer $id
 * @property string $name
 * @property boolean $producer
 */
class Firm extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'firms';
    }

    public function rules()
    {
        return [
            ['name', 'required'],
            ['producer', 'integer']
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Название фирмы',
            'id' => 'ID',
            'producer' => 'Производитель'
        ];
    }

    public function getPharmacies()
    {
        return $this->hasMany(Pharmacy::className(),['firm_id'=>'id']);
    }

    public function afterDelete()
    {
        parent::afterDelete();
        foreach($this->pharmacies as $pharmacy)
            $pharmacy->delete();
    }

    public static function getFirmList($city_id)
    {
        return Firm::find()->select([Firm::tableName().'.id',Firm::tableName().'.name'])
            ->join('LEFT JOIN', Pharmacy::tableName(),
                Firm::tableName().'.id = '.Pharmacy::tableName().'.firm_id')
            ->where(['city_id'=>$city_id])
            ->asArray()
            ->all();
    }
}
