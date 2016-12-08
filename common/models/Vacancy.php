<?php

namespace common\models;

use Yii;
use yii\imagine\Image;
use yii\helpers\ArrayHelper;

use common\models\vacancy\Comment;
use common\models\vacancy\Entry;
use common\models\vacancy\Pharmacy as Vacancy_Pharmacy;
use common\models\company\Pharmacy;
use common\models\Factory;

/**
 * This is the model class for table "vacancies".
 *
 * @property integer $id
 * @property string $image
 * @property string $thumbnail
 * @property string $title
 * @property string $description
 * @property string $email
 * @property integer $status
 * @property integer $forList
 */
class Vacancy extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_HIDDEN = 0;

    public $imageFile;
    public $thumbFile;


    public static function tableName()
    {
        return 'vacancies';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title','description','email', 'forList'], 'required'],
            [['imageFile','thumbFile'], 'required', 'on' => 'create'],
            [['title', 'description'], 'string'],
            ['email', 'email'],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['create'] = ['title', 'description', 'email', 'imageFile','thumbFile', 'forList'];
        return $scenarios;
    }


    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Название вакансии',
            'email' => 'Email',
            'status' => 'Статус',
            'description' => 'Описание',
            'thumbnail' => 'Превью',
            'image' => 'Изображение',
            'imageFile' => 'Изображение',
            'thumbFile' => 'Превью',
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
        return static::find()
            ->joinWith('pharmacies')
            ->andWhere([Vacancy_Pharmacy::tableName().'.pharmacy_id'=>Yii::$app->user->identity->pharmacist->pharmacy_id])
            ->andWhere(['status'=>static::STATUS_ACTIVE])
            ->andFilterWhere(['or', ['forList' => 1], ['and', ['forList' => 0], Yii::$app->user->identity->inList. '<> 1'],
                ['and', ['forList' => 2], Yii::$app->user->identity->inList. '=2'],
                ['and', ['forList' => 3], Yii::$app->user->identity->inList. '=1'],
                ['and', ['forList' => 4], Yii::$app->user->identity->inList. '=0']
            ])
            ->orderBy(['id'=>SORT_DESC])
            ->groupBy(static::tableName().'.id');
    }

    public function getLists()
    {
        $values = array(
            0 => 'серому и белому',
            1 => 'всем',
            2 => 'только белому',
            3 => 'только черному',
            4 => 'только серому'
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
        return $this->hasMany(Comment::className(),['vacancy_id'=>'id']);
    }

    public function getPharmacies()
    {
        return $this->hasMany(Vacancy_Pharmacy::className(),['vacancy_id'=>'id']);
    }

    public function getImagePath()
    {
        return Yii::getAlias('@uploads_view/vacancies/'.$this->image);
    }

    public function getThumbPath()
    {
        return Yii::getAlias('@uploads_view/vacancies/thumbs/'.$this->thumbnail);
    }

    public function isSignedByCurrentUser()
    {
        return Entry::findOne(['vacancy_id'=>$this->id, 'user_id'=>Yii::$app->user->id]) !== null;
    }

    public static function getStatusList()
    {
        return [static::STATUS_ACTIVE=>'активный',static::STATUS_HIDDEN=>'скрытый'];
    }

    public function getPharmaciesView($isFull = false)
    {
        $result = ArrayHelper::getColumn((Vacancy_Pharmacy::find()
            ->select(new \yii\db\Expression("CONCAT(`name`, ' (', `address`,')') as name"))
            ->joinWith('pharmacy')
            ->asArray()
            ->where(['vacancy_id'=>$this->id])
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
            ->join('LEFT JOIN', Vacancy_Pharmacy::tableName(),
                Vacancy_Pharmacy::tableName().'.pharmacy_id = '.Pharmacy::tableName().'.id')
            ->distinct()
            ->asArray()
            ->where(['vacancy_id' => $this->id])
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
        return Entry::find()->select('user_id')->where(['vacancy_id'=>$this->id])->distinct()->count();
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
        Comment::deleteAll(['vacancy_id'=>$this->id]);
        Entry::deleteAll(['vacancy_id'=>$this->id]);
        Vacancy_Pharmacy::deleteAll(['vacancy_id'=>$this->id]);
        if($this->image) @unlink(Yii::getAlias('@uploads/vacancies/'.$this->image));
        if($this->thumbnail) @unlink(Yii::getAlias('@uploads/vacancies/thumbs/'.$this->thumbnail));
    }

    public function loadPharmacies($pharmacies)
    {
        if($pharmacies) {
            for ($i = 0; $i < count($pharmacies); $i++) {
                $pharmacy = new Vacancy_Pharmacy();
                $pharmacy->pharmacy_id = $pharmacies[$i];
                $pharmacy->vacancy_id = $this->id;
                $pharmacy->save();
            }
        }
    }

    public function updatePharmacies($pharmacies)
    {
        Vacancy_Pharmacy::deleteAll(['vacancy_id' => $this->id]);
        if($pharmacies) {
            for ($i = 0; $i < count($pharmacies); $i++) {
                $pharmacy = new Vacancy_Pharmacy();
                $pharmacy->pharmacy_id = $pharmacies[$i];
                $pharmacy->vacancy_id = $this->id;
                $pharmacy->save();
            }
        }
    }

    public function loadImage() {
        if($this->imageFile) {
            $path = Yii::getAlias('@uploads/vacancies/');
            if($this->image && file_exists($path . $this->image))
                @unlink($path . $this->image);
            $filename = Yii::$app->getSecurity()->generateRandomString() . time() . '.' . $this->imageFile->extension;
            $path = $path . $filename;
            $this->imageFile->saveAs($path);
            $this->image = $filename;
            Image::thumbnail($path, 1000, 500)
                ->save(Yii::getAlias($path, ['quality' => 80]));
        }
    }

    public function loadThumb() {
        if($this->thumbFile) {
            $path = Yii::getAlias('@uploads/vacancies/thumbs/');
            if($this->thumbnail && file_exists($path . $this->thumbnail))
                @unlink($path . $this->thumbnail);
            $filename = Yii::$app->getSecurity()->generateRandomString() . time() . '.' . $this->thumbFile->extension;
            $path = $path . $filename;
            $this->thumbFile->saveAs($path);
            $this->thumbnail = $filename;
            Image::thumbnail($path, 200, 300)
                ->save(Yii::getAlias('@uploads/vacancies/thumbs/').$this->thumbnail, ['quality' => 80]);
        }
    }
}
