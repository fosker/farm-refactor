<?php

namespace common\models\vacancy;

use Yii;

/**
 * This is the model class for table "vacancy_for_pharmacies".
 *
 * @property integer $pharmacy_id
 * @property integer $vacancy_id
 */
class Pharmacy extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'vacancy_for_pharmacies';
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
            'pharmacies' => 'Аптеки'

        ];
    }

    public function getPharmacy() {
        return $this->hasOne(\common\models\company\Pharmacy::className(),['id'=>'pharmacy_id']);
    }
}
