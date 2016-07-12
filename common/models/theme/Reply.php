<?php

namespace common\models\theme;

use Yii;
use common\models\User;
use common\models\Theme;

/**
 * This is the model class for table "themes_replies".
 *
 * @property integer $id
 * @property integer $theme_id
 * @property integer $user_id
 * @property string $photo
 * @property string $date_added
 * @property string $text
 */
class Reply extends \yii\db\ActiveRecord
{

    public $image;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'themes_replies';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['theme_id', 'user_id', 'text'], 'required'],
            [['theme_id', 'user_id'], 'integer'],
            [['date_added', 'text'], 'string'],
            [['image'],'file',
                'extensions' => 'png, jpg, jpeg',
                'checkExtensionByMimeType'=>false,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'theme_id' => 'Тема',
            'user_id' => 'Пользователь',
            'photo' => 'Фото',
            'date_added' => 'Дата добавления',
            'text' => 'Сообщение'
        ];
    }

    public function getImagePath()
    {
        return Yii::getAlias('@uploads_view/themes-replies/'.$this->photo);
    }

    public function afterDelete()
    {
        if($this->photo)
            @unlink(Yii::getAlias('@uploads/themes-replies/'.$this->photo));
        parent::afterDelete();
    }

    public function saveImage()
    {

        if($this->image) {
            $path = Yii::getAlias('@uploads/themes-replies/');
            if($this->photo && file_exists($path . $this->photo))
                @unlink($path . $this->photo);
            $filename = Yii::$app->getSecurity()->generateRandomString() . time() . '.' . $this->image->extension;
            $path = $path . $filename;
            $this->image->saveAs($path);
            $this->photo = $filename;
            move_uploaded_file($this->photo, Yii::getAlias('@uploads/themes-replies/'));
        }
    }

    public function getUser()
    {
        return $this->hasOne(User::className(),['id'=>'user_id']);
    }

    public function getTheme()
    {
        return $this->hasOne(Theme::className(),['id'=>'theme_id']);
    }
}
