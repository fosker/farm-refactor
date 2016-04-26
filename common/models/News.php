<?php

namespace common\models;

use Yii;
use common\models\news\Comment;
use common\models\news\View;
use yii\imagine\Image;
use common\models\news\City;
use common\models\news\Pharmacy;
use common\models\news\Education;
use common\models\location\City as Region_City;
use common\models\agency\Pharmacy as P;
use common\models\profile\Education as E;
use yii\helpers\ArrayHelper;
use common\models\agency\Firm;
/**
 * This is the model class for table "news".
 *
 * @property integer $id
 * @property string $title
 * @property string $text
 * @property string $image
 * @property string $thumbnail
 * @property string $date
 * @property integer $views_added
 */
class News extends \yii\db\ActiveRecord
{

    public $imageFile;
    public $thumbFile;

    public $views;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'news';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'text'], 'required'],
            [['views_added'], 'integer'],
            [['imageFile','thumbFile'], 'required', 'on' => 'create'],
            [['title', 'text', 'date'], 'string'],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['create'] = ['title', 'text', 'imageFile', 'thumbFile'];
        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Заголовок',
            'text' => 'Текст',
            'thumbnail' => 'Превью',
            'image' => 'Изображение',
            'imageFile' => 'Изображение',
            'thumbFile' => 'Превью',
            'date' => 'Дата публикации',
            'views' => 'Количество уникальных просмотров',
            'views_added' => 'Добавленные просмотры'
        ];
    }

    public function fields() {
        return [
            'id', 'title', 'thumb'=>'thumbPath',
        ];
    }

    public function extraFields() {
        return [
            'text',
            'views' => function () {
                return $this->countUniqueViews();
            },
            'image'=>'imagePath',
            'date'=>function($model) {
                return strtotime($model->date);
            }
        ];
    }

    public function getCities() {
        return $this->hasMany(City::className(), ['news_id' => 'id']);
    }

    public function getPharmacies() {
        return $this->hasMany(Pharmacy::className(), ['news_id' => 'id']);
    }

    public function getEducation() {
        return $this->hasMany(Education::className(),['news_id'=>'id']);
    }

    public static function getForCurrentUser()
    {
        return static::find()
            ->joinWith('cities')
            ->joinWith('pharmacies')
            ->joinWith('education')
            ->andWhere([Education::tableName().'.education_id'=>Yii::$app->user->identity->education_id])
            ->andWhere([City::tableName().'.city_id'=>Yii::$app->user->identity->pharmacy->city_id])
            ->andWhere([Pharmacy::tableName().'.pharmacy_id'=>Yii::$app->user->identity->pharmacy_id])
            ->orderBy(['id'=>SORT_DESC])
            ->groupBy(static::tableName().'.id');
    }

    public static function getOneForCurrentUser($id)
    {
        return static::find()->where(['id' => $id])->one();
    }

    public function countUniqueViews() {
        $this->views = View::find()->select('user_id')->
            distinct()->where(['news_id' => $this->id])->count() + $this->views_added;
        return $this->views;
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
        Comment::deleteAll(['news_id'=>$this->id]);
        City::deleteAll(['news_id'=>$this->id]);
        Pharmacy::deleteAll(['news_id'=>$this->id]);
        Education::deleteAll(['news_id'=>$this->id]);
        View::deleteAll(['news_id'=>$this->id]);
        if($this->image) @unlink(Yii::getAlias('@uploads/news/'.$this->image));
        if($this->thumbnail) @unlink(Yii::getAlias('@uploads/news/thumbs/'.$this->thumbnail));
    }

    public function loadImage() {
        if($this->imageFile) {
            $path = Yii::getAlias('@uploads/news/');
            if($this->image && file_exists($path . $this->image))
                @unlink($path . $this->image);
            $filename = Yii::$app->getSecurity()->generateRandomString() . time() . '.' . $this->imageFile->extension;
            $path = $path . $filename;
            $this->imageFile->saveAs($path);
            $this->image = $filename;
            Image::thumbnail($path, 1000, 500)
                ->save(Yii::getAlias('@uploads/news/').$this->image, ['quality' => 80]);
        }
    }

    public function loadThumb() {
        if($this->thumbFile) {
            $path = Yii::getAlias('@uploads/news/thumbs/');
            if($this->thumbnail && file_exists($path . $this->thumbnail))
                @unlink($path . $this->thumbnail);
            $filename = Yii::$app->getSecurity()->generateRandomString() . time() . '.' . $this->thumbFile->extension;
            $path = $path . $filename;
            $this->thumbFile->saveAs($path);
            $this->thumbnail = $filename;
            Image::thumbnail($path, 200, 300)
                ->save(Yii::getAlias('@uploads/news/thumbs/').$this->thumbnail, ['quality' => 80]);
        }
    }

    public function getImagePath() {
        return Yii::getAlias('@uploads_view/news/'.$this->image);
    }

    public function getThumbPath() {
        return Yii::getAlias('@uploads_view/news/thumbs/'.$this->thumbnail);
    }

    public function getCitiesView($isFull = false) {
        $result = ArrayHelper::getColumn((City::find()
            ->select(Region_City::tableName().'.name')
            ->joinWith('city')
            ->asArray()
            ->where(['news_id'=>$this->id])
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
            ->where(['news_id' => $this->id])
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
            ->where(['news_id'=>$this->id])
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

    public function loadCities($cities)
    {
        if($cities) {
            for ($i = 0; $i < count($cities); $i++) {
                $city = new City();
                $city->city_id = $cities[$i];
                $city->news_id = $this->id;
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
                $pharmacy->news_id = $this->id;
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
                $education->news_id = $this->id;
                $education->save();
            }
        }
    }

    public function updateEducation($educations)
    {
        Education::deleteAll(['news_id' => $this->id]);
        if($educations) {
            for ($i = 0; $i < count($educations); $i++) {
                $education = new Education();
                $education->education_id = $educations[$i];
                $education->news_id = $this->id;
                $education->save();
            }
        }
    }

    public function updateCities($cities)
    {
        City::deleteAll(['news_id' => $this->id]);
        if($cities) {
            for ($i = 0; $i < count($cities); $i++) {
                $city = new City();
                $city->city_id = $cities[$i];
                $city->news_id = $this->id;
                $city->save();
            }
        }
    }

    public function updatePharmacies($pharmacies)
    {
        Pharmacy::deleteAll(['news_id' => $this->id]);
        if($pharmacies) {
            for ($i = 0; $i < count($pharmacies); $i++) {
                $pharmacy = new Pharmacy();
                $pharmacy->pharmacy_id = $pharmacies[$i];
                $pharmacy->news_id = $this->id;
                $pharmacy->save();
            }
        }
    }
}
