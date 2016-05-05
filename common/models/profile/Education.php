<?php

namespace common\models\profile;

use Yii;

use common\models\banner\Education as Banner_education;
use common\models\news\Education as News_education;
use common\models\presentation\Education as Presentation_education;
use common\models\survey\Education as Survey_education;
use common\models\seminar\Education as Seminar_education;
use common\models\stock\Education as Stock_education;
use common\models\user\Pharmacist;

/**
 * This is the model class for table "user_education".
 *
 * @property integer $id
 * @property string $name
 */
class Education extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'user_education';
    }

    public function rules()
    {
        return [
            ['name', 'required']
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название образования',
        ];
    }

    public function getPharmacists() {
        return $this->hasMany(Pharmacist::className(), ['education_id' => 'id']);
    }

    public function afterDelete()
    {
        parent::afterDelete();
        Banner_education::deleteAll(['education_id' => $this->id]);
        Survey_education::deleteAll(['education_id' => $this->id]);
        Seminar_education::deleteAll(['education_id' => $this->id]);
        Presentation_education::deleteAll(['education_id' => $this->id]);
        Stock_education::deleteAll(['education_id' => $this->id]);
        News_education::deleteAll(['education_id' => $this->id]);
    }
}
