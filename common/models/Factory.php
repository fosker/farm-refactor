<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

use common\models\factory\Product;
use common\models\factory\Stock;
use yii\imagine\Image;

/**
 * This is the model class for table "factories".
 *
 * @property integer $id
 * @property string $title
 * @property string $description
 * @property string $image
 * @property string $logo
 */
class Factory extends ActiveRecord
{
    public $imageFile;
    public $logoFile;

    public static function tableName()
    {
        return 'factories';
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
            'title' => 'Название фабрики',
            'description' => 'Описание',
            'image' => 'Изображение',
            'logo' => 'Лого',
            'logoFile' => 'Лого',
            'imageFile' => 'Изображение'
        ];
    }

    public function fields() {
        return [
            'id','title','logo'=>'logoPath',
        ];
    }

    public function extraFields() {
        return [
            'description','image'=>'imagePath','products','stocks'
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
        foreach($this->stocks as $stock)
            $stock->delete();
        Product::deleteAll(['factory_id'=>$this->id]);
        if($this->image) @unlink(Yii::getAlias('@uploads/factories/'.$this->image));
        if($this->logo) @unlink(Yii::getAlias('@uploads/factories/logos'.$this->logo));
        parent::afterDelete();
    }

    public function loadImage()
    {
        if($this->imageFile) {
            $path = Yii::getAlias('@uploads/factories/');
            if($this->image && file_exists($path . $this->image))
                @unlink($path . $this->image);
            $filename = Yii::$app->getSecurity()->generateRandomString() . time() . '.' . $this->imageFile->extension;
            $path = $path . $filename;
            $this->imageFile->saveAs($path);
            $this->image = $filename;
            Image::thumbnail($path, 1000, 500)
                ->save(Yii::getAlias('@uploads/factories/').$this->image, ['quality' => 80]);
        }
    }

    public function loadLogo()
    {
        if($this->logoFile) {
            $path = Yii::getAlias('@uploads/factories/logos/');
            if($this->logo && file_exists($path . $this->logo))
                @unlink($path . $this->logo);
            $filename = Yii::$app->getSecurity()->generateRandomString() . time() . '.' . $this->logoFile->extension;
            $path = $path . $filename;
            $this->logoFile->saveAs($path);
            $this->logo = $filename;
            Image::thumbnail($path, 300, 300)
                ->save(Yii::getAlias('@uploads/factories/logos/').$this->logo, ['quality' => 80]);
        }
    }

    public function getProducts()
    {
        return $this->hasMany(Product::className(),['factory_id'=>'id']);
    }

    public function getImagePath()
    {
        return Yii::getAlias('@uploads_view/factories/'.$this->image);
    }

    public function getLogoPath()
    {
        return Yii::getAlias('@uploads_view/factories/logos/'.$this->logo);
    }

    public function getStocks()
    {
        return $this->hasMany(Stock::className(),['factory_id'=>'id']);
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
