<?php

namespace common\models;

use Yii;
use yii\imagine\Image;

use common\models\vacancy\Comment;
use common\models\vacancy\Entry;
use common\models\vacancy\City;
use common\models\vacancy\Pharmacy;
use common\models\location\City as Region_City;
use common\models\agency\Pharmacy as P;
use common\models\agency\Firm;
use yii\helpers\ArrayHelper;
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
            [['title','description','email'], 'required'],
            [['imageFile','thumbFile'], 'required', 'on' => 'create'],
            [['title', 'description'], 'string'],
            ['email', 'email'],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['create'] = ['title', 'description', 'email', 'imageFile','thumbFile'];
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
            ->joinWith('cities')
            ->joinWith('pharmacies')
            ->andWhere([City::tableName().'.city_id'=>Yii::$app->user->identity->pharmacy->city_id])
            ->andWhere([Pharmacy::tableName().'.pharmacy_id'=>Yii::$app->user->identity->pharmacy_id])
            ->andWhere(['status'=>static::STATUS_ACTIVE])
            ->orderBy(['id'=>SORT_DESC])
            ->groupBy(static::tableName().'.id');
    }

    public static function getOneForCurrentUser($id)
    {
        return static::getForCurrentUser()->andWhere([Pharmacy::tableName().'.vacancy_id'=>$id])->one();
    }

    public function getCities() {
        return $this->hasMany(City::className(), ['vacancy_id' => 'id']);
    }

    public function getPharmacies() {
        return $this->hasMany(Pharmacy::className(), ['vacancy_id' => 'id']);
    }

    public function getImagePath() {
        return Yii::getAlias('@uploads_view/vacancies/'.$this->image);
    }

    public function getThumbPath() {
        return Yii::getAlias('@uploads_view/vacancies/thumbs/'.$this->thumbnail);
    }

    public function isSignedByCurrentUser() {
        return Entry::findOne(['vacancy_id'=>$this->id, 'user_id'=>Yii::$app->user->id]) !== null;
    }

    public static function getStatusList()
    {
        return [static::STATUS_ACTIVE=>'активный',static::STATUS_HIDDEN=>'скрытый'];
    }

    public function getCitiesView($isFull = false) {
        $result = ArrayHelper::getColumn((City::find()
            ->select(Region_City::tableName().'.name')
            ->joinWith('city')
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
            ->where(['vacancy_id' => $this->id])
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
        City::deleteAll(['vacancy_id'=>$this->id]);
        Pharmacy::deleteAll(['vacancy_id'=>$this->id]);
        if($this->image) @unlink(Yii::getAlias('@uploads/vacancies/'.$this->image));
        if($this->thumbnail) @unlink(Yii::getAlias('@uploads/vacancies/thumbs/'.$this->thumbnail));
    }

    public function loadCities($cities)
    {
        if($cities) {
            for ($i = 0; $i < count($cities); $i++) {
                $city = new City();
                $city->city_id = $cities[$i];
                $city->vacancy_id = $this->id;
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
                $pharmacy->vacancy_id = $this->id;
                $pharmacy->save();
            }
        }
    }

    public function updateCities($cities)
    {
        City::deleteAll(['vacancy_id' => $this->id]);
        if($cities) {
            for ($i = 0; $i < count($cities); $i++) {
                $city = new City();
                $city->city_id = $cities[$i];
                $city->vacancy_id = $this->id;
                $city->save();
            }
        }
    }

    public function updatePharmacies($pharmacies)
    {
        Pharmacy::deleteAll(['vacancy_id' => $this->id]);
        if($pharmacies) {
            for ($i = 0; $i < count($pharmacies); $i++) {
                $pharmacy = new Pharmacy();
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
