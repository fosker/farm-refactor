<?php

namespace common\models\factory;

use Yii;
use yii\db\ActiveRecord;
use yii\imagine\Image;
use common\models\Factory;


/**
 * This is the model class for table "factory_products".
 *
 * @property integer $id
 * @property string $title
 * @property string $description
 * @property string $image
 * @property string $thumbnail
 * @property integer $factory_id
 * @property integer $status
 * @property integer $priority
 */
class Product extends ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_HIDDEN = 0;

    public $imageFile;
    public $thumbFile;

    public static function tableName()
    {
        return 'factory_products';
    }

    public function rules()
    {
        return [
            [['title', 'description', 'factory_id', 'priority'], 'required'],
            [['priority'], 'integer'],
            [['imageFile', 'thumbFile'], 'required', 'on' => 'create'],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['create'] = ['title', 'description', 'factory_id', 'imageFile', 'thumbFile', 'priority'];
        return $scenarios;
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Название продукта',
            'description' => 'Описание',
            'image' => 'Изображение',
            'thumbnail' => 'Превью',
            'imageFile' => 'Изображение',
            'thumbFile' => 'Превью',
            'factory_id' => 'Компания',
            'status' => 'Статус',
            'priority' => 'Приоритет'
        ];
    }

    public function fields()
    {
        return [
            'id', 'title', 'thumbnail' => 'thumbPath'
        ];
    }

    public function extraFields()
    {
        return [
            'description', 'image' => 'imagePath'
        ];
    }

    public function getImagePath()
    {
        return Yii::getAlias('@uploads_view/factories/products/' . $this->image);
    }

    public function getThumbPath()
    {
        return Yii::getAlias('@uploads_view/factories/products/thumbs/' . $this->thumbnail);
    }

    public function getFactory()
    {
        return $this->hasOne(Factory::className(), ['id' => 'factory_id']);
    }

    public static function getStatusList()
    {
        return [static::STATUS_ACTIVE => 'активный', static::STATUS_HIDDEN => 'скрытый'];
    }

    public function loadImage()
    {
        if ($this->imageFile) {
            $path = Yii::getAlias('@uploads/factories/products/');
            if ($this->image && file_exists($path . $this->image))
                @unlink($path . $this->image);
            $filename = Yii::$app->getSecurity()->generateRandomString() . time() . '.' . $this->imageFile->extension;
            $path = $path . $filename;
            $this->imageFile->saveAs($path);
            $this->image = $filename;
            Image::thumbnail($path, 1000, 500)
                ->save(Yii::getAlias('@uploads/factories/products/') . $this->image, ['quality' => 80]);
        }
    }

    public function loadThumb()
    {
        if ($this->thumbFile) {
            $path = Yii::getAlias('@uploads/factories/products/thumbs/');
            if ($this->thumbnail && file_exists($path . $this->thumbnail))
                @unlink($path . $this->thumbnail);
            $filename = Yii::$app->getSecurity()->generateRandomString() . time() . '.' . $this->thumbFile->extension;
            $path = $path . $filename;
            $this->thumbFile->saveAs($path);
            $this->thumbnail = $filename;
            Image::thumbnail($path, 200, 300)
                ->save(Yii::getAlias('@uploads/factories/products/thumbs/') . $this->thumbnail, ['quality' => 80]);
        }
    }

    public function hide()
    {
        $this->status = static::STATUS_HIDDEN;
        $this->save(false);
    }

    public function approve()
    {
        $this->status = static::STATUS_ACTIVE;
        $this->save(false);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->loadImage();
            $this->loadThumb();
            return true;
        } else return false;
    }

    public function afterDelete()
    {
        if ($this->image) @unlink(Yii::getAlias('@uploads/factories/products/' . $this->image));
        if ($this->thumbnail) @unlink(Yii::getAlias('@uploads/factories/products/thumbs/' . $this->thumbnail));
        parent::afterDelete();
    }
}
