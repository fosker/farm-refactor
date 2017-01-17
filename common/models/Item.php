<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\imagine\Image;
use yii\helpers\ArrayHelper;
use common\models\shop\Pharmacy as Item_Pharmacy;
use common\models\shop\Vendor;
use common\models\company\Pharmacy;
use common\models\shop\Desire;
use common\models\shop\Present;


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
 * @property integer $count
 * @property integer $status
 * @property integer $forList
 */
class Item extends ActiveRecord
{
    const VENDOR_SUSHI = 20;

    const PHARMSET1 = 38;
    const PHARMSET2 = 49;

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
            [['title', 'description', 'vendor_id', 'points', 'priority', 'count', 'forList'], 'required'],
            [['points', 'priority', 'count'], 'integer'],
            [['imageFile','thumbFile'], 'required', 'on' => 'create'],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['create'] = ['title', 'description', 'vendor_id', 'points', 'priority', 'imageFile','thumbFile', 'count', 'forList'];
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
            'count' => 'Количество',
            'forList' => 'Показывать списку'
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
            ->joinWith('pharmacies')
            ->andWhere([Item_Pharmacy::tableName().'.pharmacy_id'=>Yii::$app->user->identity->pharmacist->pharmacy_id])
            ->andWhere(['status'=>static::STATUS_ACTIVE])
            ->andWhere(['>', 'count', '0'])
            ->andFilterWhere(['like', 'forList', Yii::$app->user->identity->inList])
            ->orderBy(["priority"=>SORT_DESC,static::tableName().".id"=>SORT_DESC])
            ->groupBy(static::tableName().'.id');
    }

    public static function getOneForCurrentUser($id)
    {
        return static::getForCurrentUser()->andWhere([static::tableName().'.id'=>$id])->one();
    }

    public function getVendor()
    {
        return $this->hasOne(Vendor::className(),['id'=>'vendor_id']);
    }


    public function getPharmacies()
    {
        return $this->hasMany(Item_Pharmacy::className(),['item_id'=>'id']);
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

    public function getPharmaciesView($isFull = false)
    {
        $result = ArrayHelper::getColumn((Item_Pharmacy::find()
            ->select(new \yii\db\Expression("CONCAT(`name`, ' (', `address`,')') as name"))
            ->joinWith('pharmacy')
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

    public function getCompanyView($isFull = false)
    {
        $result = ArrayHelper::getColumn((Company::find()->select([
            Company::tableName().'.title'])
            ->from(Company::tableName())
            ->join('LEFT JOIN', Pharmacy::tableName(),
                Company::tableName().'.id = '.Pharmacy::tableName().'.company_id')
            ->join('LEFT JOIN', Item_Pharmacy::tableName(),
                Item_Pharmacy::tableName().'.pharmacy_id = '.Pharmacy::tableName().'.id')
            ->distinct()
            ->asArray()
            ->where(['item_id' => $this->id])
            ->all()),'title');

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

    public function approve()
    {
        $this->status = static::STATUS_ACTIVE;
        $this->save(false);
    }

    public function hide()
    {
        $this->status = static::STATUS_HIDDEN;
        $this->save(false);
    }

    public function beforeSave($insert)
    {
        if(parent::beforeSave($insert)) {
            $this->loadImage();
            $this->loadThumb();
            return true;
        } else return false;
    }

    public function afterDelete()
    {
        parent::afterDelete();
        Desire::deleteAll(['item_id'=>$this->id]);
        Present::deleteAll(['item_id'=>$this->id]);
        Item_Pharmacy::deleteAll(['item_id'=>$this->id]);
        if($this->image) @unlink(Yii::getAlias('@uploads/presents/'.$this->image));
        if($this->thumbnail) @unlink(Yii::getAlias('@uploads/presents/thumbs/'.$this->thumbnail));
    }

    public function loadPharmacies($pharmacies)
    {
        if($pharmacies) {
            for ($i = 0; $i < count($pharmacies); $i++) {
                $pharmacy = new Item_Pharmacy();
                $pharmacy->pharmacy_id = $pharmacies[$i];
                $pharmacy->item_id = $this->id;
                $pharmacy->save();
            }
        }
    }

    public function updatePharmacies($pharmacies)
    {
        Item_Pharmacy::deleteAll(['item_id' => $this->id]);
        if($pharmacies) {
            for ($i = 0; $i < count($pharmacies); $i++) {
                $pharmacy = new Item_Pharmacy();
                $pharmacy->pharmacy_id = $pharmacies[$i];
                $pharmacy->item_id = $this->id;
                $pharmacy->save();
            }
        }
    }

    public function deletePharmacies()
    {
        Item_Pharmacy::deleteAll(['item_id' => $this->id]);
    }

    public function loadImage()
    {
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

    public function loadThumb()
    {
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
