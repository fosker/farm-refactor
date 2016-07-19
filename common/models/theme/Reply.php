<?php

namespace common\models\theme;

use Yii;
use yii\base\Model;
use common\models\User;
use common\models\Theme;


class Reply extends Model
{

    public $image;
    public $theme_id;
    public $user_id;
    public $text;
    public $photo;


    public function rules()
    {
        return [
            [['theme_id', 'user_id', 'text'], 'required'],
            [['theme_id'], 'integer'],
            [['text'], 'string'],
            [['image'],'file',
                'extensions' => 'png, jpg, jpeg',
                'checkExtensionByMimeType'=>false,
            ],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['free'] = ['theme_id', 'user_id', 'text', 'image'];
        $scenarios['form'] = ['theme_id', 'user_id'];
        return $scenarios;
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
            'text' => 'Сообщение'
        ];
    }

    public function getImagePath()
    {
        return Yii::getAlias('@uploads_view/themes-replies/'.$this->photo);
    }

    public function saveImage()
    {
        if($this->image) {
            $path = Yii::getAlias('@uploads/themes-replies/');
            $filename = Yii::$app->getSecurity()->generateRandomString() . time() . '.' . $this->image->extension;
            $path = $path . $filename;
            $this->image->saveAs($path);
            $this->photo = $filename;
            move_uploaded_file($this->photo, Yii::getAlias('@uploads/themes-replies/'));
        }
    }
}
