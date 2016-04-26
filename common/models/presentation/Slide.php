<?php

namespace common\models\presentation;

use common\models\Presentation;
use Yii;
use yii\imagine\Image;

/**
 * This is the model class for table "presentation_slides".
 *
 * @property integer $id
 * @property integer $presentation_id
 * @property string $image
 * @property string $description
 * @property integer $order_index
 */
class Slide extends \yii\db\ActiveRecord
{

    public $imageFile;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'presentation_slides';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_index'], 'required'],
            [['order_index'], 'integer'],
            ['imageFile', 'required', 'on' => 'create'],
       ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['create'] = ['description', 'order_index', 'imageFile'];
        return $scenarios;
    }

    public function attributeLabels()
    {
        return [
            'description' => 'Описание',
            'order_index' => 'Порядковый номер',
            'imageFile' => 'Изображение',
        ];
    }

    public function fields() {
        return [
            'id','image'=>'imagePath','description','order'=>'order_index'
        ];
    }

    public function getImagePath() {
        $file = Yii::getAlias('@uploads_view/presentations/slides/'.$this->image);
        return $this->image ? $file : null;
    }

    public function loadImage() {
        if($this->imageFile) {
            $path = Yii::getAlias('@uploads/presentations/slides/');
            if ($this->image && file_exists($path . $this->image))
                @unlink($path . $this->image);
            $filename = Yii::$app->getSecurity()->generateRandomString() . time() . '.' . $this->imageFile->extension;
            $path = $path . $filename;
            $this->imageFile->saveAs($path);
            $this->image = $filename;

            move_uploaded_file($this->image, Yii::getAlias('@uploads/presentations/slides/'));
            //Image::thumbnail($path, 1000, 500)
                //->save(Yii::getAlias('@uploads/presentations/slides/') . $this->image, ['quality' => 80]);
        }
    }

    public function getPresentation()
    {
        return $this->hasOne(Presentation::className(),['id'=>'presentation_id']);
    }

    public function afterDelete() {
        if($this->image) @unlink(Yii::getAlias('@uploads/presentations/slides/'.$this->image));
        parent::afterDelete();
    }

}
