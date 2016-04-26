<?php

namespace common\models;

use Yii;

use common\models\block\Mark;
use common\models\block\Comment;
use yii\imagine\Image;

/**
 * This is the model class for table "blocks".
 *
 * @property integer $id
 * @property string $title
 * @property string $description
 * @property string $image
 */
class Block extends \yii\db\ActiveRecord
{

    public $imageFile;

    public static function tableName()
    {
        return 'blocks';
    }

    public function rules()
    {
        return [
            [['title', 'description'], 'required'],
            ['imageFile', 'required', 'on' => 'create']

        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['create'] = ['title', 'description', 'imageFile'];
        return $scenarios;
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Название страницы',
            'description' => 'Описание',
            'imageFile' => 'Изображение',
        ];
    }

    public function fields()
    {
        return [
            'id',
            'image'=>'imagePath',
            'title',
            'description',
            'countComments' => function($model) {
                return Comment::CountForBlock($model->id);
            },
            'mark' => function($model) {
                return Mark::calculateForBlock($model->id);
            }
        ];
    }

    public function getImagePath()
    {
        return Yii::getAlias('@uploads_view/blocks/'.$this->image);
    }

    public function beforeSave($insert)
    {
        if(parent::beforeSave($insert)) {
            $this->loadImage();
            return true;
        } else return false;
    }

    public function loadImage()
    {
        if($this->imageFile) {
            $path = Yii::getAlias('@uploads/blocks/');
            if($this->image && file_exists($path . $this->image))
                @unlink($path . $this->image);
            $filename = Yii::$app->getSecurity()->generateRandomString() . time() . '.' . $this->imageFile->extension;
            $path = $path . $filename;
            $this->imageFile->saveAs($path);
            $this->image = $filename;
            Image::thumbnail($path, 1000, 500)
                ->save(Yii::getAlias('@uploads/blocks/').$this->image, ['quality' => 80]);
        }
    }

    public function getComments()
    {
        return $this->hasMany(Comment::className(), ['block_id' => 'id'])->count();
    }

    public function getMark()
    {
        $marks = Mark::find()->where(['block_id'=>$this->id]);
        try{
            return round($marks->sum('mark')/$marks->count(), 1);
        } catch(\yii\base\ErrorException $e) {
            return 0;
        }
    }

    public function afterDelete()
    {
        if($this->image)
            @unlink(Yii::getAlias('@uploads/blocks/'.$this->image));
        Comment::deleteAll(['block_id'=>$this->id]);
        Mark::deleteAll(['block_id'=>$this->id]);
        parent::afterDelete();
    }

}
