<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\imagine\Image;
use yii\helpers\ArrayHelper;
use common\models\banner\City;
use common\models\banner\Pharmacy;
use common\models\banner\Education;
use common\models\profile\Education as E;
use common\models\location\City as C;
use common\models\agency\Pharmacy as P;
use common\models\agency\Firm;
use common\models\factory\Stock;

/**
 * This is the model class for table "banners".
 *
 * @property integer $id
 * @property string $image
 * @property integer $position
 * @property string $title
 * @property string $link
 * @property string $status
 */
class Banner extends ActiveRecord
{

    const STATUS_ACTIVE = 1;
    const STATUS_HIDDEN = 0;

    public $imageFile;

    public static function tableName()
    {
        return 'banners';
    }

    public function rules()
    {
        return [
            [['title', 'position', 'link'], 'required'],
            ['imageFile', 'required', 'on' => 'create']
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['create'] = ['title', 'position', 'link', 'imageFile'];
        return $scenarios;
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Название баннера',
            'link' => 'Для категории',
            'status' => 'Статус',
            'imageFile' => 'Изображение',
            'image' => 'Изображение',
            'position' => 'Позиция'
        ];
    }

    public function fields()
    {
        return [
            'id', 'image' => 'imagePath', 'title', 'link', 'position',
        ];
    }

    public static function getForCurrentUser()
    {
        $base = static::find()
            ->andWhere(['status'=>static::STATUS_ACTIVE])
            ->joinWith('cities')
            ->joinWith('pharmacies')
            ->joinWith('education')
            ->andWhere([Education::tableName().'.education_id'=>Yii::$app->user->identity->education_id])
            ->andWhere([City::tableName().'.city_id'=>Yii::$app->user->identity->pharmacy->city_id])
            ->andWhere([Pharmacy::tableName().'.pharmacy_id'=>Yii::$app->user->identity->pharmacy_id])
            ->groupBy(static::tableName().'.id');

        $banners = clone $base;
        $slider = clone $base;

        $slider->andWhere(['position'=>1]);
        $banners->andWhere('position!=1')->groupBy(['position'])->orderBy(['position'=>SORT_ASC]);
        return $slider->union($banners);
    }

    public static function getOneForCurrentUser($id)
    {
        return static::getForCurrentUser()->andWhere([static::tableName().'.id'=>$id])->one();
    }

    public function getCities()
    {
        return $this->hasMany(City::className(),['banner_id'=>'id']);
    }

    public function getPharmacies()
    {
        return $this->hasMany(Pharmacy::className(),['banner_id'=>'id']);
    }

    public function getEducation() {
        return $this->hasMany(Education::className(),['banner_id'=>'id']);
    }

    public function getImagePath()
    {
        return Yii::getAlias('@uploads_view/banners/'.$this->image);
    }

    public static function pages()
    {
        return [
            'block'=>'Страницы',
            'present'=>'Подарки',
            'survey'=>'Анкеты',
            'seminar'=>'Семинары',
            'stock'=>'Акции',
            'presentation'=>'Презентации',
        ];
    }

    public static function positions()
    {
        return [
            1=>'Слайдер(2:1)',
            2=>'Первый ряд первый баннер(2:1)',
            3=>'Первый ряд второй баннер(2:1)',
            4=>'Второй ряд первый баннер(4:1)',
            5=>'Третий ряд первый баннер(1:1)',
            6=>'Третий ряд второй баннер(1:1)',
            7=>'Третий ряд третий баннер(1:1)',
            8=>'Четвертый ряд первый баннер(4:1)',
        ];
    }

    public static function getStatusList()
    {
        return [static::STATUS_ACTIVE=>'активный',static::STATUS_HIDDEN=>'скрытый'];
    }

    public function getLinkTitleHref()
    {
        return Html::a($this->linkTitle,['/'.explode('/',$this->link)[0].'/view', 'id'=>explode('/',$this->link)[1]]);
    }

    public function getLinkTitle()
    {
        $path = explode('/',$this->link);
        $name = '';
        $item = ['title'=>''];
        switch($path[0]) {
            case 'block':
                $item = Block::findOne($path[1]);
                $name = 'Страница: ';
                break;
            case 'present':
                $item = Item::findOne($path[1]);
                $name = 'Подарок: ';
                break;
            case 'survey':
                $item = Survey::findOne($path[1]);
                $name = 'Анкета: ';
                break;
            case 'seminar':
                $item = Seminar::findOne($path[1]);
                $name = 'Семинар: ';
                break;
            case 'stock':
                $item = Stock::findOne($path[1]);
                $name = 'Акция: ';
                break;
            case 'presentation':
                $item = Presentation::findOne($path[1]);
                $name = 'Презентация: ';
                break;
        }
        return $name.$item['title'];
    }

    public function getCitiesView($isFull = false)
    {
        $result = ArrayHelper::getColumn((City::find()
            ->select(C::tableName().'.name')
            ->joinWith('city')
            ->asArray()
            ->where(['banner_id'=>$this->id])
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
            ->select('name')
            ->joinWith('education')
            ->asArray()
            ->where(['banner_id'=>$this->id])
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
            ->where(['banner_id' => $this->id])
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

    public function hide()
    {
        $this->imageFile = null;
        $this->status = static::STATUS_HIDDEN;
        $this->save(false);
    }

    public function approve()
    {
        $this->status = static::STATUS_ACTIVE;
        $this->save(false);
    }

    public function loadImage() {
        if($this->imageFile) {

            $one_one = [5,6,7];
            $two_one = [1,2,3];
            $four_one = [4, 8];

            $width = 0;
            $height = 0;

            $path = Yii::getAlias('@uploads/banners/');
            if ($this->image && file_exists($path . $this->image))
                @unlink($path . $this->image);
            $filename = Yii::$app->getSecurity()->generateRandomString() . time() . '.' . $this->imageFile->extension;
            $path = $path . $filename;
            $this->imageFile->saveAs($path);
            $this->image = $filename;
            if(in_array($this->position, $one_one)) {
                $width = 1000;
                $height = 1000;
            }
            else if(in_array($this->position, $two_one)) {
                $width = 2000;
                $height = 1000;
            }
            else if(in_array($this->position, $four_one)) {
                $width = 4000;
                $height = 1000;
            }
            Image::thumbnail($path, $width, $height)
                ->save(Yii::getAlias('@uploads/banners/') . $this->image, ['quality' => 80]);
        }
    }

    public function beforeSave($insert) {
        if(parent::beforeSave($insert)) {
            $this->loadImage();
            return true;
        } else return false;
    }

    public function loadCities($cities)
    {
        if($cities) {
            for ($i = 0; $i < count($cities); $i++) {
                $city = new City();
                $city->city_id = $cities[$i];
                $city->banner_id = $this->id;
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
                $pharmacy->banner_id = $this->id;
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
                $education->banner_id = $this->id;
                $education->save();
            }
        }
    }

    public function updateEducation($educations)
    {
        Education::deleteAll(['banner_id' => $this->id]);
        if($educations) {
            for ($i = 0; $i < count($educations); $i++) {
                $education = new Education();
                $education->education_id = $educations[$i];
                $education->banner_id = $this->id;
                $education->save();
            }
        }
    }

    public function updateCities($cities)
    {
        City::deleteAll(['banner_id' => $this->id]);
        if($cities) {
            for ($i = 0; $i < count($cities); $i++) {
                $city = new City();
                $city->city_id = $cities[$i];
                $city->banner_id = $this->id;
                $city->save();
            }
        }
    }

    public function updatePharmacies($pharmacies)
    {
        Pharmacy::deleteAll(['banner_id' => $this->id]);
        if($pharmacies) {
            for ($i = 0; $i < count($pharmacies); $i++) {
                $pharmacy = new Pharmacy();
                $pharmacy->pharmacy_id = $pharmacies[$i];
                $pharmacy->banner_id = $this->id;
                $pharmacy->save();
            }
        }
    }

    public function afterDelete()
    {
        Education::deleteAll(['banner_id'=>$this->id]);
        City::deleteAll(['banner_id'=>$this->id]);
        Pharmacy::deleteAll(['banner_id'=>$this->id]);

        if($this->image) @unlink(Yii::getAlias('@uploads/banners/'.$this->image));
        parent::afterDelete();
    }
}
