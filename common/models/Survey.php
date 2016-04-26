<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\imagine\Image;

use common\models\survey\View;
use common\models\survey\Question;
use common\models\survey\City;
use common\models\location\City as Region_City;
use common\models\agency\Pharmacy as P;
use common\models\agency\Firm;
use common\models\survey\Pharmacy;
use yii\helpers\ArrayHelper;
use common\models\survey\Education;
use common\models\profile\Education as E;

/**
 * This is the model class for table "surveys".
 *
 * @property integer $id
 * @property string $title
 * @property string $description
 * @property integer $points
 * @property string $image
 * @property string $thumbnail
 * @property integer $status
 */
class Survey extends ActiveRecord
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
        return 'surveys';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['points'], 'integer'],
            [['title', 'description', 'points'], 'required'],
            [['imageFile', 'thumbFile'], 'required', 'on' => 'create']

        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['create'] = ['title', 'description', 'points', 'imageFile', 'thumbFile'];
        return $scenarios;
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Название анкеты',
            'points' => 'Количество баллов',
            'status' => 'Статус',
            'description' => 'Описание',
            'imageFile' => 'Изображение',
            'thumbFile' => 'Превью'
        ];
    }

    public function fields() {
        return [
            'id',
            'title',
            'points',
            'thumb'=>'thumbPath',
        ];
    }

    public function extraFields() {
        return [
            'description',
            'questions',
            'image'=>'imagePath',
        ];
    }

    /**
     * @return \yii\db\Query
     */
    public static function getForCurrentUser()
    {
        return static::find()
            ->joinWith('questions')
            ->joinWith('cities')
            ->joinWith('pharmacies')
            ->joinWith('education')
            ->andWhere([City::tableName().'.city_id'=>Yii::$app->user->identity->pharmacy->city_id])
            ->andWhere([Education::tableName().'.education_id'=>Yii::$app->user->identity->education_id])
            ->andWhere([Pharmacy::tableName().'.pharmacy_id'=>Yii::$app->user->identity->pharmacy_id])
            ->andWhere(['status'=>static::STATUS_ACTIVE])
            ->andWhere([
                'not exists',
                View::findByCurrentUser()
                    ->andWhere(View::tableName().'.survey_id='.static::tableName().'.id')
            ])
            ->orderBy(['id'=>SORT_DESC])
            ->groupBy(static::tableName().'.id');
    }

    public static function getOneForCurrentUser($id)
    {
        return static::getForCurrentUser()->andWhere([static::tableName().'.id'=>$id])->one();
    }

    public function getQuestions() {
        return $this->hasMany(Question::className(), ['survey_id' => 'id']);
    }

    public function getEducation() {
        return $this->hasMany(Education::className(),['survey_id'=>'id']);
    }

    public function getCities() {
        return $this->hasMany(City::className(), ['survey_id' => 'id']);
    }

    public function getPharmacies() {
        return $this->hasMany(Pharmacy::className(), ['survey_id' => 'id']);
    }

    public function getImagePath() {
        return Yii::getAlias('@uploads_view/surveys/'.$this->image);
    }

    public function getThumbPath() {
        return Yii::getAlias('@uploads_view/surveys/thumbs/'.$this->thumbnail);
    }

    public static function isAnsweredByCurrentUser($id)
    {
        return View::findByCurrentUser()->andWhere(['survey_id'=>$id])->exists();
    }

    public static function getStatusList()
    {
        return [static::STATUS_ACTIVE=>'активный',static::STATUS_HIDDEN=>'скрытый'];
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

    public function getCitiesView($isFull = false)
    {
        $result = ArrayHelper::getColumn((City::find()
            ->select(Region_City::tableName().'.name')
            ->joinWith('city')
            ->asArray()
            ->where(['survey_id'=>$this->id])
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
            ->where(['survey_id' => $this->id])
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

    public function getEducationsView($isFull = false) {
        $result = ArrayHelper::getColumn((Education::find()
            ->select(E::tableName().'.name')
            ->joinWith('education')
            ->asArray()
            ->where(['survey_id'=>$this->id])
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

    public function getAnswersCount()
    {
        return View::find()
            ->where(['survey_id'=>$this->id])
            ->count();
    }

    public function loadImage() {
        if($this->imageFile) {
            $path = Yii::getAlias('@uploads/surveys/');
            if($this->image && file_exists($path . $this->image))
                @unlink($path . $this->image);
            $filename = Yii::$app->getSecurity()->generateRandomString() . time() . '.' . $this->imageFile->extension;
            $path = $path . $filename;
            $this->imageFile->saveAs($path);
            $this->image = $filename;
            Image::thumbnail($path, 1000, 500)
                ->save(Yii::getAlias('@uploads/surveys/').$this->image, ['quality' => 80]);
        }
    }

    public function loadThumb() {
        if($this->thumbFile) {
            $path = Yii::getAlias('@uploads/surveys/thumbs/');
            if($this->thumbnail && file_exists($path . $this->thumbnail))
                @unlink($path . $this->thumbnail);
            $filename = Yii::$app->getSecurity()->generateRandomString() . time() . '.' . $this->thumbFile->extension;
            $path = $path . $filename;
            $this->thumbFile->saveAs($path);
            $this->thumbnail = $filename;
            Image::thumbnail($path, 200, 300)
                ->save(Yii::getAlias('@uploads/surveys/thumbs/').$this->thumbnail, ['quality' => 80]);
        }
    }

    public function beforeSave($insert) {
        if(parent::beforeSave($insert)) {
            $this->loadImage();
            $this->loadThumb();
            return true;
        } else return false;
    }

    public function getViews()
    {
        return $this->hasMany(View::className(), ['survey_id' => 'id']);
    }

    public function afterDelete() {
        foreach($this->questions as $question)
            $question->delete();
        foreach($this->views as $view)
            $view->delete();
        City::deleteAll(['survey_id'=>$this->id]);
        Education::deleteAll(['survey_id'=>$this->id]);
        Pharmacy::deleteAll(['survey_id'=>$this->id]);
        if($this->image) @unlink(Yii::getAlias('@uploads/surveys/'.$this->image));
        if($this->thumbnail) @unlink(Yii::getAlias('@uploads/surveys/thumbs/'.$this->thumbnail));
        parent::afterDelete();
    }

    public function loadCities($cities)
    {
        if($cities) {
            for ($i = 0; $i < count($cities); $i++) {
                $city = new City();
                $city->city_id = $cities[$i];
                $city->survey_id = $this->id;
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
                $pharmacy->survey_id = $this->id;
                $pharmacy->save();
            }
        }
    }

    public function loadEducation($educations)
    {
        if($educations) {
            for ($i = 0; $i < count($educations); $i++) {
                $education = new Education();
                $education->education_id = $educations[$i];
                $education->survey_id = $this->id;
                $education->save();
            }
        }
    }

    public function updateEducation($educations)
    {
        Education::deleteAll(['survey_id' => $this->id]);
        if($educations) {
            for ($i = 0; $i < count($educations); $i++) {
                $education = new Education();
                $education->education_id = $educations[$i];
                $education->survey_id = $this->id;
                $education->save();
            }
        }
    }

    public function updateCities($cities)
    {
        City::deleteAll(['survey_id' => $this->id]);
        if($cities) {
            for ($i = 0; $i < count($cities); $i++) {
                $city = new City();
                $city->city_id = $cities[$i];
                $city->survey_id = $this->id;
                $city->save();
            }
        }
    }

    public function updatePharmacies($pharmacies)
    {
        Pharmacy::deleteAll(['survey_id' => $this->id]);
        if($pharmacies) {
            for ($i = 0; $i < count($pharmacies); $i++) {
                $pharmacy = new Pharmacy();
                $pharmacy->pharmacy_id = $pharmacies[$i];
                $pharmacy->survey_id = $this->id;
                $pharmacy->save();
            }
        }
    }
}
