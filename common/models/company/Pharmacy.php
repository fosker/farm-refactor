<?php

namespace common\models\company;

use Yii;
use common\models\Banner;
use common\models\location\City;
use common\models\User;
use common\models\Company;
use common\models\banner\Pharmacy as Banner_Pharmacy;
use common\models\news\Pharmacy as News_Pharmacy;
use common\models\survey\Pharmacy as Survey_Pharmacy;
use common\models\presentation\Pharmacy as Presentation_Pharmacy;
use common\models\stock\Pharmacy as Stock_Pharmacy;
use common\models\seminar\Pharmacy as Seminar_Pharmacy;
use common\models\shop\Pharmacy as Item_Pharmacy;
use common\models\vacancy\Pharmacy as Vacancy_Pharmacy;
use common\models\user\Pharmacist;
use common\models\profile\PharmacistUpdateRequest;

/**
 * This is the model class for table "company_pharmacies".
 *
 * @property integer $id
 * @property string $address
 * @property string $name
 * @property integer $company_id
 * @property integer $city_id
 * @property integer $phone
 * @property integer $date_visit
 * @property integer $comment
 */

class Pharmacy extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'company_pharmacies';
    }

    public function rules()
    {
        return [
            [['company_id', 'city_id', 'name'], 'required'],
            [['address', 'phone', 'date_visit', 'comment'], 'string']
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название аптеки',
            'address' => 'Адрес',
            'company_id' => 'Организация',
            'city_id' => 'Город',
            'phone' => 'Телефон',
            'date_visit' => 'Дата посещения',
            'comment' => 'Комментарий'
        ];
    }

    public function fields()
    {
        return [
            'id', 'name', 'address', 'city_id', 'company_id'
        ];
    }

    public function getUserCount()
    {
        return Pharmacist::find()->joinWith('pharmacy')
            ->andWhere([Pharmacist::tableName().'.pharmacy_id' => $this->id])
            ->count();
    }

    public function getCompany()
    {
        return $this->hasOne(Company::className(), ['id' => 'company_id']);
    }

    public function getBannerPharmacies()
    {
        return $this->hasMany(Banner_Pharmacy::className(), ['pharmacy_id' => 'id']);
    }

    public function getNewsPharmacies()
    {
        return $this->hasMany(News_Pharmacy::className(), ['pharmacy_id' => 'id']);
    }

    public function getSurveyPharmacies()
    {
        return $this->hasMany(Survey_Pharmacy::className(), ['pharmacy_id' => 'id']);
    }

    public function getPresentationPharmacies()
    {
        return $this->hasMany(Presentation_Pharmacy::className(), ['pharmacy_id' => 'id']);
    }

    public function getStockPharmacies()
    {
        return $this->hasMany(Stock_Pharmacy::className(), ['pharmacy_id' => 'id']);
    }

    public function getSeminarPharmacies()
    {
        return $this->hasMany(Seminar_Pharmacy::className(), ['pharmacy_id' => 'id']);
    }

    public function getItemPharmacies()
    {
        return $this->hasMany(Item_Pharmacy::className(), ['pharmacy_id' => 'id']);
    }

    public function getVacancyPharmacies()
    {
        return $this->hasMany(Vacancy_Pharmacy::className(), ['pharmacy_id' => 'id']);
    }

    public function getCity()
    {
        return $this->hasOne(City::className(), ['id' => 'city_id']);
    }

    public function getPharmacists()
    {
        return $this->hasMany(Pharmacist::className(), ['pharmacy_id' => 'id']);
    }


    public function afterDelete()
    {
        parent::afterDelete();
        Banner_Pharmacy::deleteAll(['pharmacy_id' => $this->id]);
        News_Pharmacy::deleteAll(['pharmacy_id' => $this->id]);
        Survey_Pharmacy::deleteAll(['pharmacy_id' => $this->id]);
        Presentation_Pharmacy::deleteAll(['pharmacy_id' => $this->id]);
        Stock_Pharmacy::deleteAll(['pharmacy_id' => $this->id]);
        Seminar_Pharmacy::deleteAll(['pharmacy_id' => $this->id]);
        Item_Pharmacy::deleteAll(['pharmacy_id' => $this->id]);
        Vacancy_Pharmacy::deleteAll(['pharmacy_id' => $this->id]);
        foreach($this->pharmacists as $pharmacist)
            $pharmacist->delete();
        PharmacistUpdateRequest::deleteAll(['pharmacy_id' => $this->id]);
    }

    public static function getPharmacyList($company_id, $city_id)
    {
        return Pharmacy::find()
            ->select(['id', new \yii\db\Expression("CONCAT(`name`, ' (', `address`,')') as name")])
            ->where(['city_id' => $city_id])
            ->andWhere(['company_id'=>$company_id])
            ->asArray()
            ->all();
    }
}
