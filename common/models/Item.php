<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\imagine\Image;
use common\models\shop\City;
use common\models\shop\Pharmacy;
use common\models\shop\Vendor;
use common\models\location\City as Region_city;
use common\models\agency\Pharmacy as P;
use common\models\agency\Firm;
use common\models\shop\Desire;
use common\models\shop\Present;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "shop_items".
 *
 * @property integer $id
 * @property string $title
 * @property string $image
 * @property string $thumbnail
 * @property integer $vendor_id
 * @property integer $points
 * @property string $description
 * @property integer $priority
 * @property integer $status
 */
class Item extends ActiveRecord
{

    const STATUS_ACTIVE = 1;
    const STATUS_HIDDEN = 0;

    public $imageFile;
    public $thumbFile;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_items';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'description', 'vendor_id', 'points', 'priority'], 'required'],
            [['points', 'priority'], 'integer'],
            [['imageFile','thumbFile'], 'required', 'on' => 'create'],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['create'] = ['title', 'description', 'vendor_id', 'points', 'priority', 'imageFile','thumbFile'];
        return $scenarios;
    }

    /**
     * @inheritdoc
     */

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Название подарка',
            'image' => 'Изображение',
            'thumbnail' => 'Превью',
            'vendor_id' => 'Поставщик',
            'points' => 'Баллы',
            'description' => 'Описание',
            'priority' => 'Приоритет',
            'status' => 'Статус',
            'thumbFile' => 'Превью',
            'imageFile' => 'Изображение',
        ];
    }

    public function fields() {
        return [
            'id','title','points','priority','thumb'=>'thumbPath',
        ];
    }

    public function extraFields() {
        return [
            'description','vendor','image'=>'imagePath',
        ];
    }

    /**
     * @return \yii\db\Query
     */
    public static function getForCurrentUser() {
        return static::find()
            ->joinWith('cities')
            ->joinWith('pharmacies')
            ->andWhere([City::tableName().'.city_id'=>Yii::$app->user->identity->pharmacy->city_id])
            ->andWhere([Pharmacy::tableName().'.pharmacy_id'=>Yii::$app->user->identity->pharmacy_id])
            ->andWhere(['status'=>static::STATUS_ACTIVE])
            ->orderBy(["priority"=>SORT_DESC,static::tableName().".id"=>SORT_DESC])
            ->groupBy(static::tableName().'.id');
    }

    public static function getOneForCurrentUser($id)
    {
        return static::getForCurrentUser()->andWhere([static::tableName().'.id'=>$id])->one();
    }

    public function getVendor() {
        return $this->hasOne(Vendor::className(),['id'=>'vendor_id']);
    }

    public function getCities() {
        return $this->hasMany(City::className(),['item_id'=>'id']);
    }

    public function getPharmacies() {
        return $this->hasMany(Pharmacy::className(),['item_id'=>'id']);
    }

    public function getImagePath() {
        return Yii::getAlias('@uploads_view/presents/'.$this->image);
    }

    public function getThumbPath() {
        return Yii::getAlias('@uploads_view/presents/thumbs/'.$this->thumbnail);
    }

    public static function getStatusList()
    {
        return [static::STATUS_ACTIVE=>'активный',static::STATUS_HIDDEN=>'скрытый'];
    }

    public function getCitiesView($isFull = false) {
        $result = ArrayHelper::getColumn((City::find()
            ->select(Region_city::tableName().'.name')
            ->joinWith('city')
            ->asArray()
            ->where(['item_id'=>$this->id])
            ->all()),'name');
        $string = "";
        if(!$isFull) {
            $limit = 5;
            if (count($result) > $limit) {
                for ($i = 0; $i < $limit; $i++) {
                    $string .= $result[$i].", ";
                }
                $string .= "и ещё (".(count($result)-$limit).")";
            } else
                $string = implode(", ", $result);
        } else
            $string = implode(", ", $result);

        return $string;
    }

    public function getFirmsView($isFull = false) {
        $result = ArrayHelper::getColumn((Firm::find()->select([
            'firms.name'])
            ->from(Firm::tableName())
            ->join('LEFT JOIN', P::tableName(),
                Firm::tableName().'.id = '.P::tableName().'.firm_id')
            ->join('LEFT JOIN', Pharmacy::tableName(),
                Pharmacy::tableName().'.pharmacy_id = '.P::tableName().'.id')
            ->distinct()
            ->asArray()
            ->where(['item_id' => $this->id])
            ->all()),'name');
        $string = "";
        if(!$isFull) {
            $limit = 5;
            if (count($result) > $limit) {
                for ($i = 0; $i < $limit; $i++) {
                    $string .= $result[$i].", ";
                }
                $string .= "и ещё (".(count($result)-$limit).")";
            } else
                $string = implode(", ", $result);
        } else
            $string = implode(", ", $result);

        return $string;
    }

    public function approve() {
        $this->status = static::STATUS_ACTIVE;
        $this->save(false);
    }

    public function hide() {
        $this->status = static::STATUS_HIDDEN;
        $this->save(false);
    }

    public function beforeSave($insert) {
        if(parent::beforeSave($insert)) {
            $this->loadImage();
            $this->loadThumb();
            return true;
        } else return false;
    }

    public function afterDelete() {
        parent::afterDelete();
        Desire::deleteAll(['item_id'=>$this->id]);
        Present::deleteAll(['item_id'=>$this->id]);
        City::deleteAll(['item_id'=>$this->id]);
        Pharmacy::deleteAll(['item_id'=>$this->id]);
        if($this->image) @unlink(Yii::getAlias('@uploads/presents/'.$this->image));
        if($this->thumbnail) @unlink(Yii::getAlias('@uploads/presents/thumbs/'.$this->thumbnail));
    }

    public function loadCities($cities)
    {
        if($cities) {
            for ($i = 0; $i < count($cities); $i++) {
                $city = new City();
                $city->city_id = $cities[$i];
                $city->item_id = $this->id;
                $city->save();
            }
        }
    }

    public function loadPharmacies($pharmacies)
    {
        if($pharmacies) {
            for ($i = 0; $i < count($pharmacies); $i++) {
                $pharmacy = new Pharmacy();
                $pharmacy->pharmacy_id = $pharmacies[$i];
                $pharmacy->item_id = $this->id;
                $pharmacy->save();
            }
        }
    }

    public function updateCities($cities)
    {
        City::deleteAll(['item_id' => $this->id]);
        if($cities) {
            for ($i = 0; $i < count($cities); $i++) {
                $city = new City();
                $city->city_id = $cities[$i];
                $city->item_id = $this->id;
                $city->save();
            }
        }
    }

    public function updatePharmacies($pharmacies)
    {
        Pharmacy::deleteAll(['item_id' => $this->id]);
        if($pharmacies) {
            for ($i = 0; $i < count($pharmacies); $i++) {
                $pharmacy = new Pharmacy();
                $pharmacy->pharmacy_id = $pharmacies[$i];
                $pharmacy->item_id = $this->id;
                $pharmacy->save();
            }
        }
    }

    public function loadImage() {
        if($this->imageFile) {
            $path = Yii::getAlias('@uploads/presents/');
            if($this->image && file_exists($path . $this->image))
                @unlink($path . $this->image);
            $filename = Yii::$app->getSecurity()->generateRandomString() . time() . '.' . $this->imageFile->extension;
            $path = $path . $filename;
            $this->imageFile->saveAs($path);
            $this->image = $filename;
            Image::thumbnail($path, 1000, 500)
                ->save(Yii::getAlias('@uploads/presents/').$this->image, ['quality' => 80]);
        }
    }

    public function loadThumb() {
        if($this->thumbFile) {
            $path = Yii::getAlias('@uploads/presents/thumbs/');
            if($this->thumbnail && file_exists($path . $this->thumbnail))
                @unlink($path . $this->thumbnail);
            $filename = Yii::$app->getSecurity()->generateRandomString() . time() . '.' . $this->thumbFile->extension;
            $path = $path . $filename;
            $this->thumbFile->saveAs($path);
            $this->thumbnail = $filename;
            Image::thumbnail($path, 200, 300)
                ->save(Yii::getAlias('@uploads/presents/thumbs/').$this->thumbnail, ['quality' => 80]);
        }
    }
}
