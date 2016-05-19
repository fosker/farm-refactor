<?php

namespace common\models\stock;

use Yii;
use yii\db\ActiveRecord;
use yii\imagine\Image;
use common\models\User;
use common\models\Stock;

/**
 * This is the model class for table "stock_replies".
 *
 * @property integer $id
 * @property integer $stock_id
 * @property integer $user_id
 * @property string $photo
 * @property string $date_add
 * @property string $downloaded
 */
class Reply extends ActiveRecord
{

    public $image;

    const DOWNLOADED = true;
    const NOT_DOWNLOADED = false;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'stock_replies';
    }

    /**
     * @inheritdoc
     */

    public function rules()
    {
        return [
            [['stock_id', 'user_id', 'image'], 'required'],
            [['stock_id'],function($model,$attr) {
                if (!$this->hasErrors()) {
                    if (!Stock::getOneForCurrentUser($this->stock_id)) {
                        $this->addError('stock_id', 'Вы не можете участвовать в этой акции');
                    }
                }
            }],
            [['image'],'file',
                'extensions' => 'png, jpg, jpeg',
                'checkExtensionByMimeType'=>false,
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'stock_id' => 'Акция',
            'user_id' => 'Пользователь',
            'photo' => 'Фото',
            'date_add' => 'Дата добавления',
        ];
    }

    public function getImagePath()
    {
        return Yii::getAlias('@uploads_view/stock-replies/'.$this->photo);
    }

    public function afterDelete()
    {
        if($this->photo)
            @unlink(Yii::getAlias('@uploads/stock-replies/'.$this->photo));
        parent::afterDelete();
    }

    public function downloaded()
    {
        $this->downloaded = static::DOWNLOADED;
        $this->save(false);
    }

    public function notDownloaded()
    {
        $this->downloaded = static::NOT_DOWNLOADED;
        $this->save(false);
    }

    public function saveImage()
    {

        if($this->image) {
            $path = Yii::getAlias('@uploads/stock-replies/');
            if($this->photo && file_exists($path . $this->photo))
                @unlink($path . $this->photo);
            $filename = Yii::$app->getSecurity()->generateRandomString() . time() . '.' . $this->image->extension;
            $path = $path . $filename;
            $this->image->saveAs($path);
            $this->photo = $filename;
            move_uploaded_file($this->photo, Yii::getAlias('@uploads/stock-replies/'));
        }
    }

    public function fields()
    {
        return [
            'stock_id','image'
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(),['id'=>'user_id']);
    }

    public function getStock()
    {
        return $this->hasOne(Stock::className(),['id'=>'stock_id']);
    }

}
