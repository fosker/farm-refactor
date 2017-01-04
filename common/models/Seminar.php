<?php

namespace common\models;

use Yii;
use yii\imagine\Image;
use yii\helpers\ArrayHelper;

use common\models\seminar\Pharmacy as Seminar_Pharmacy;
use common\models\seminar\Education as Seminar_Education;
use common\models\seminar\Type as Seminar_Type;
use common\models\seminar\Comment;
use common\models\company\Pharmacy;
use common\models\Factory;
use common\models\seminar\Entry;
use common\models\profile\Type;


/**
 * This is the model class for table "seminars".
 *
 * @property integer $id
 * @property string $image
 * @property string $thumbnail
 * @property integer $factory_id
 * @property string $title
 * @property string $description
 * @property string $email
 * @property integer $status
 * @property integer $forList
 */
class Seminar extends \yii\db\ActiveRecord
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
        return 'seminars';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title','description','email', 'factory_id', 'forList'], 'required'],
            [['imageFile','thumbFile'], 'required', 'on' => 'create'],
            [['title', 'description'], 'string'],
            ['email', 'email'],
        ];
    }

    /**
     * @inheritdoc
     */

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['create'] = ['title', 'description', 'email', 'imageFile','thumbFile', 'factory_id', 'forList'];
        return $scenarios;
    }


    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Название семинара',
            'email' => 'Email',
            'status' => 'Статус',
            'description' => 'Описание',
            'thumbnail' => 'Превью',
            'image' => 'Изображение',
            'imageFile' => 'Изображение',
            'thumbFile' => 'Превью',
            'factory_id' => 'Фабрика Автор',
            'forList' => 'Показывать списку'
        ];
    }

    public function fields() {
        return [
            'id','title', 'thumb'=>'thumbPath',
        ];
    }

    public function extraFields() {
        return [
            'description',
            'isSigned' => function () {
                return $isSigned = $this->isSignedByCurrentUser();
            },
            'image'=>'imagePath',
        ];
    }

    /**
     * @return \yii\db\Query
     */
    public static function getForCurrentUser()
    {
        if(Yii::$app->user->identity->type_id == Type::TYPE_PHARMACIST) {
            $education = Seminar_Education::find()->select('seminar_id')->andFilterWhere(['education_id' => Yii::$app->user->identity->pharmacist->education_id]);
            $types = Seminar_Type::find()->select('seminar_id')->andFilterWhere(['type_id' => Yii::$app->user->identity->type_id]);
            $pharmacies = Seminar_Pharmacy::find()->select('seminar_id')->andFilterWhere(['pharmacy_id' => Yii::$app->user->identity->pharmacist->pharmacy_id]);
            return static::find()
                ->andWhere(['status'=>static::STATUS_ACTIVE])
                ->andFilterWhere(['in', static::tableName().'.id', $education])
                ->andFilterWhere(['in', static::tableName().'.id', $types])
                ->andFilterWhere(['in', static::tableName().'.id', $pharmacies])
                ->andFilterWhere(['or', ['forList' => 1], ['and', ['forList' => 0], Yii::$app->user->identity->inList. '<> 1'],
                    ['and', ['forList' => 2], Yii::$app->user->identity->inList. '=2'],
                    ['and', ['forList' => 3], Yii::$app->user->identity->inList. '=1'],
                    ['and', ['forList' => 4], Yii::$app->user->identity->inList. '=0'],
                    ['and', ['forList' => 5], Yii::$app->user->identity->inList. '=3']
                ])
                ->orderBy(['id'=>SORT_DESC]);
        } elseif (Yii::$app->user->identity->type_id == Type::TYPE_AGENT) {
            return static::find()
                ->joinWith('types')
                ->where([
                    'factory_id'=>Yii::$app->user->identity->agent->factory_id,
                    Seminar_Type::tableName().'.type_id'=> Type::TYPE_PHARMACIST
                ])
                ->orWhere([
                    Seminar_Type::tableName().'.type_id'=> Type::TYPE_AGENT,
                    'factory_id'=>[Yii::$app->user->identity->agent->factory_id, '1']
                ])
                ->andFilterWhere(['or', ['forList' => 1], ['and', ['forList' => 0], Yii::$app->user->identity->inList. '<> 1'],
                    ['and', ['forList' => 2], Yii::$app->user->identity->inList. '=2'],
                    ['and', ['forList' => 3], Yii::$app->user->identity->inList. '=1'],
                    ['and', ['forList' => 4], Yii::$app->user->identity->inList. '=0']
                ])
                ->andWhere(['status'=>static::STATUS_ACTIVE])
                ->orderBy(['id'=>SORT_DESC])
                ->groupBy(static::tableName().'.id');
        }
    }

    public function getLists()
    {
        $values = array(
            0 => 'нейтральному и белому',
            1 => 'всем',
            2 => 'только белому',
            3 => 'только черному',
            4 => 'только нейтральному',
            5 => 'только серому'
        );
        if(isset($values[$this->forList])) {
            return $values[$this->forList];
        }
    }

    public static function getOneForCurrentUser($id)
    {
        return static::getForCurrentUser()->andWhere([static::tableName().'.id'=>$id])->one();
    }

    public function getComments()
    {
        return $this->hasMany(Comment::className(),['seminar_id'=>'id']);
    }

    public function getFactory()
    {
        return $this->hasOne(Factory::className(),['id'=>'factory_id']);
    }

    public function getPharmacies()
    {
        return $this->hasMany(Seminar_Pharmacy::className(),['seminar_id'=>'id']);
    }

    public function getTypes()
    {
        return $this->hasMany(Seminar_Type::className(),['seminar_id'=>'id']);
    }

    public function getEducation()
    {
        return $this->hasMany(Seminar_Education::className(),['seminar_id'=>'id']);
    }

    public function getImagePath()
    {
        return Yii::getAlias('@uploads_view/seminars/'.$this->image);
    }

    public function getThumbPath()
    {
        return Yii::getAlias('@uploads_view/seminars/thumbs/'.$this->thumbnail);
    }

    public function isSignedByCurrentUser()
    {
        return Entry::findOne(['seminar_id'=>$this->id, 'user_id'=>Yii::$app->user->id]) !== null;
    }

    public static function getStatusList()
    {
        return [static::STATUS_ACTIVE=>'активный',static::STATUS_HIDDEN=>'скрытый'];
    }

    public function getEducationsView($isFull = false)
    {
        $result = ArrayHelper::getColumn((Seminar_Education::find()
            ->select('name')
            ->joinWith('education')
            ->asArray()
            ->where(['seminar_id'=>$this->id])
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

    public function getPharmaciesView($isFull = false)
    {
        $result = ArrayHelper::getColumn((Seminar_Pharmacy::find()
            ->select(new \yii\db\Expression("CONCAT(`name`, ' (', `address`,')') as name"))
            ->joinWith('pharmacy')
            ->asArray()
            ->where(['seminar_id'=>$this->id])
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


    public function getTypesView($isFull = false)
    {
        $result = ArrayHelper::getColumn((Seminar_Type::find()
            ->select('name')
            ->joinWith('type')
            ->asArray()
            ->where(['seminar_id'=>$this->id])
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
            ->join('LEFT JOIN', Seminar_Pharmacy::tableName(),
                Seminar_Pharmacy::tableName().'.pharmacy_id = '.Pharmacy::tableName().'.id')
            ->distinct()
            ->asArray()
            ->where(['seminar_id' => $this->id])
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

    public function getSignsCount()
    {
        return Entry::find()->select('user_id')->where(['seminar_id'=>$this->id])->distinct()->count();
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
        Seminar_Education::deleteAll(['seminar_id'=>$this->id]);
        Seminar_Pharmacy::deleteAll(['seminar_id'=>$this->id]);
        Seminar_Type::deleteAll(['seminar_id'=>$this->id]);
        Comment::deleteAll(['seminar_id'=>$this->id]);
        Entry::deleteAll(['seminar_id'=>$this->id]);
        if($this->image) @unlink(Yii::getAlias('@uploads/seminars/'.$this->image));
        if($this->thumbnail) @unlink(Yii::getAlias('@uploads/seminars/thumbs/'.$this->thumbnail));
    }

    public function loadPharmacies($pharmacies)
    {
        if($pharmacies) {
            for ($i = 0; $i < count($pharmacies); $i++) {
                $pharmacy = new Seminar_Pharmacy();
                $pharmacy->pharmacy_id = $pharmacies[$i];
                $pharmacy->seminar_id = $this->id;
                $pharmacy->save();
            }
        }
    }

    public function loadEducation($educations)
    {
        if($educations) {
            for ($i = 0; $i < count($educations); $i++) {
                $education = new Seminar_Education();
                $education->education_id = $educations[$i];
                $education->seminar_id = $this->id;
                $education->save();
            }
        }
    }

    public function loadTypes($types)
    {
        if($types) {
            for ($i = 0; $i < count($types); $i++) {
                $type = new Seminar_Type();
                $type->type_id = $types[$i];
                $type->seminar_id = $this->id;
                $type->save();
            }
        }
    }

    public function updateEducation($educations)
    {
        Seminar_Education::deleteAll(['seminar_id' => $this->id]);
        if($educations) {
            for ($i = 0; $i < count($educations); $i++) {
                $education = new Seminar_Education();
                $education->education_id = $educations[$i];
                $education->seminar_id = $this->id;
                $education->save();
            }
        }
    }

    public function updatePharmacies($pharmacies)
    {
        Seminar_Pharmacy::deleteAll(['seminar_id' => $this->id]);
        if($pharmacies) {
            for ($i = 0; $i < count($pharmacies); $i++) {
                $pharmacy = new Seminar_Pharmacy();
                $pharmacy->pharmacy_id = $pharmacies[$i];
                $pharmacy->seminar_id = $this->id;
                $pharmacy->save();
            }
        }
    }

    public function deletePharmacies()
    {
        Seminar_Pharmacy::deleteAll(['seminar_id' => $this->id]);
    }

    public function updateTypes($types)
    {
        Seminar_Type::deleteAll(['seminar_id' => $this->id]);
        if($types) {
            for ($i = 0; $i < count($types); $i++) {
                $type = new Seminar_Type();
                $type->type_id = $types[$i];
                $type->seminar_id = $this->id;
                $type->save();
            }
        }
    }

    public function loadImage()
    {
        if($this->imageFile) {
            $path = Yii::getAlias('@uploads/seminars/');
            if($this->image && file_exists($path . $this->image))
                @unlink($path . $this->image);
            $filename = Yii::$app->getSecurity()->generateRandomString() . time() . '.' . $this->imageFile->extension;
            $path = $path . $filename;
            $this->imageFile->saveAs($path);
            $this->image = $filename;
            Image::thumbnail($path, 1000, 500)
                ->save(Yii::getAlias('@uploads/seminars/').$this->image, ['quality' => 80]);
        }
    }

    public function loadThumb()
    {
        if($this->thumbFile) {
            $path = Yii::getAlias('@uploads/seminars/thumbs/');
            if($this->thumbnail && file_exists($path . $this->thumbnail))
                @unlink($path . $this->thumbnail);
            $filename = Yii::$app->getSecurity()->generateRandomString() . time() . '.' . $this->thumbFile->extension;
            $path = $path . $filename;
            $this->thumbFile->saveAs($path);
            $this->thumbnail = $filename;
            Image::thumbnail($path, 200, 300)
                ->save(Yii::getAlias('@uploads/seminars/thumbs/').$this->thumbnail, ['quality' => 80]);
        }
    }

}
