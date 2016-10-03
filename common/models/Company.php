<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

use common\models\company\Pharmacy;
use yii\imagine\Image;
use common\models\user\Pharmacist;

/**
 * This is the model class for table "companies".
 *
 * @property integer $id
 * @property string $title
 * @property string $description
 * @property string $image
 * @property string $logo
 */
class Company extends ActiveRecord
{
    public $imageFile;
    public $logoFile;

    public static function tableName()
    {
        return 'companies';
    }

    public function rules()
    {
        return [
            [['title', 'description'], 'required'],
            [['imageFile','logoFile'], 'required', 'on' => 'create'],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['create'] = ['title', 'description', 'imageFile','logoFile'];
        return $scenarios;
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Название компании',
            'description' => 'Описание',
            'image' => 'Изображение',
            'logo' => 'Лого',
            'logoFile' => 'Лого',
            'imageFile' => 'Изображение'
        ];
    }

    public function getUserCount()
    {
        return Pharmacist::find()->joinWith('pharmacy')
            ->andWhere([Pharmacy::tableName().'.company_id' => $this->id])
            ->count();
    }

    public function fields() {
        return [
            'id','title','logo'=>'logoPath'
        ];
    }

    public function extraFields() {
        return [
            'description','image'=>'imagePath'
        ];
    }

    public function beforeSave($insert)
    {
        if(parent::beforeSave($insert)) {
            $this->loadImage();
            $this->loadLogo();
            return true;
        } else return false;
    }

    public function afterDelete()
    {
        foreach($this->pharmacies as $pharmacy)
            $pharmacy->delete();
        if($this->image) @unlink(Yii::getAlias('@uploads/companies/'.$this->image));
        if($this->logo) @unlink(Yii::getAlias('@uploads/companies/logos'.$this->logo));
        parent::afterDelete();
    }

    public function loadImage()
    {
        if($this->imageFile) {
            $path = Yii::getAlias('@uploads/companies/');
            if($this->image && file_exists($path . $this->image))
                @unlink($path . $this->image);
            $filename = Yii::$app->getSecurity()->generateRandomString() . time() . '.' . $this->imageFile->extension;
            $path = $path . $filename;
            $this->imageFile->saveAs($path);
            $this->image = $filename;
            Image::thumbnail($path, 1000, 500)
                ->save(Yii::getAlias('@uploads/companies/').$this->image, ['quality' => 80]);
        }
    }

    public function loadLogo()
    {
        if($this->logoFile) {
            $path = Yii::getAlias('@uploads/companies/logos/');
            if($this->logo && file_exists($path . $this->logo))
                @unlink($path . $this->logo);
            $filename = Yii::$app->getSecurity()->generateRandomString() . time() . '.' . $this->logoFile->extension;
            $path = $path . $filename;
            $this->logoFile->saveAs($path);
            $this->logo = $filename;
            Image::thumbnail($path, 300, 300)
                ->save(Yii::getAlias('@uploads/companies/logos/').$this->logo, ['quality' => 80]);
        }
    }

    public function getPharmacies()
    {
        return $this->hasMany(Pharmacy::className(),['company_id'=>'id']);
    }

    public function getPharmaciesCount()
    {
        $pharmacyCount = Pharmacist::find()
            ->from([Pharmacist::tableName(), Pharmacy::tableName(), Company::tableName()])
            ->select('count('.Pharmacist::tableName().'.id'.') as count, pharmacy_id')
            ->where(Pharmacist::tableName().'.pharmacy_id = '.Pharmacy::tableName().'.id')
            ->andWhere(Pharmacy::tableName().'.company_id ='.Company::tableName().'.id')
            ->groupBy('pharmacy_id');
        return $this->hasMany(Pharmacy::className(), ['company_id' => 'id'])
            ->leftJoin(['pharmacyCount' => $pharmacyCount], 'pharmacyCount.pharmacy_id = id')
            ->orderBy(['pharmacyCount.count' => SORT_DESC]);
    }

    public function getImagePath()
    {
        return Yii::getAlias('@uploads_view/companies/'.$this->image);
    }

    public function getLogoPath()
    {
        return Yii::getAlias('@uploads_view/companies/logos/'.$this->logo);
    }

    /**
     * @return \yii\db\Query
     */
    public static function getForCurrentUser()
    {
        return static::find()
            ->where(['id'=>Stock::getForCurrentUser()->select('factory_id')]);
    }

    /**
     * @return Factory|null
     */
    public static function getOneForCurrentUser($id)
    {
        return static::getForCurrentUser()->andWhere([static::tableName().'.id'=>$id])->one();
    }

}
