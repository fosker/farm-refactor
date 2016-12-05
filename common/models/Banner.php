<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\imagine\Image;
use yii\helpers\ArrayHelper;
use yii\db\Query;

use common\models\banner\Pharmacy as Banner_Pharmacy;
use common\models\banner\Education as Banner_Education;
use common\models\banner\Type as Banner_Type;
use common\models\company\Pharmacy;
use common\models\Factory;
use common\models\profile\Type;


/**
 * This is the model class for table "banners".
 *
 * @property integer $id
 * @property string $image
 * @property integer $position
 * @property string $title
 * @property string $link
 * @property string $status
 * @property integer $factory_id
 * @property integer $forList
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
            [['title', 'position', 'link', 'factory_id', 'forList'], 'required'],
            [['factory_id'], 'integer'],
            ['imageFile', 'required', 'on' => 'create']
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['create'] = ['title', 'position', 'link', 'imageFile', 'factory_id', 'forList'];
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
            'position' => 'Позиция',
            'factory_id' => 'Фабрика Автор',
            'forList' => 'Показывать списку'
        ];
    }

    public function fields()
    {
        return [
            'id', 'image' => 'imagePath', 'title', 'link', 'position'
        ];
    }

    public static function getForCurrentUser()
    {
        if(Yii::$app->user->identity->type_id == Type::TYPE_PHARMACIST) {
            $education = Banner_Education::find()->select('banner_id')->andFilterWhere(['education_id' => Yii::$app->user->identity->pharmacist->education_id]);
            $types = Banner_Type::find()->select('banner_id')->andFilterWhere(['type_id' => Yii::$app->user->identity->type_id]);
            $pharmacies = Banner_Pharmacy::find()->select('banner_id')->andFilterWhere(['pharmacy_id' => Yii::$app->user->identity->pharmacist->pharmacy_id]);
            $base = static::find()
                ->andWhere(['status'=>static::STATUS_ACTIVE])
                ->andFilterWhere(['in', static::tableName().'.id', $education])
                ->andFilterWhere(['in', static::tableName().'.id', $types])
                ->andFilterWhere(['in', static::tableName().'.id', $pharmacies])
                ->andFilterWhere(['or', ['forList' => 1], ['and', ['forList' => 0], Yii::$app->user->identity->inList. '<> 1'],
                    ['and', ['forList' => 2], Yii::$app->user->identity->inList. '=2'],
                    ['and', ['forList' => 3], Yii::$app->user->identity->inList. '=1']]);
        } elseif (Yii::$app->user->identity->type_id == Type::TYPE_AGENT) {
            $base = static::find()
                ->joinWith('types')
                ->where([
                    'factory_id'=>Yii::$app->user->identity->agent->factory_id,
                    Banner_Type::tableName().'.type_id'=> Type::TYPE_PHARMACIST
                ])
                ->orWhere([
                    Banner_Type::tableName().'.type_id'=> Type::TYPE_AGENT,
                    'factory_id'=>[Yii::$app->user->identity->agent->factory_id, '1']
                ])
                ->andFilterWhere(['or', ['forList' => 1], ['and', ['forList' => 0], Yii::$app->user->identity->inList. '<> 1'],
                    ['and', ['forList' => 2], Yii::$app->user->identity->inList. '=2'],
                    ['and', ['forList' => 3], Yii::$app->user->identity->inList. '=1']])
                ->andWhere(['status'=>static::STATUS_ACTIVE])
                ->groupBy(static::tableName().'.id');
        }

        $banners = clone $base;
        $slider = clone $base;

        $slider->andWhere(['position'=>1]);
        $banners->andWhere('position!=1')->groupBy(['position']);

        return Banner::find()
            ->from(['u' => $slider->union($banners)])
            ->orderBy('position ASC, RAND()');
    }

    public function getLists()
    {
        $values = array(
            0 => 'нейтральному и белому',
            1 => 'всем',
            2 => 'только белому',
            3 => 'только серому'
        );
        if(isset($values[$this->forList])) {
            return $values[$this->forList];
        }
    }

    public static function getOneForCurrentUser($id)
    {
        return static::getForCurrentUser()->andWhere([static::tableName().'.id'=>$id])->one();
    }

    public function getFactory()
    {
        return $this->hasOne(Factory::className(),['id'=>'factory_id']);
    }

    public function getPharmacies()
    {
        return $this->hasMany(Banner_Pharmacy::className(),['banner_id'=>'id']);
    }

    public function getTypes()
    {
        return $this->hasMany(Banner_Type::className(),['banner_id'=>'id']);
    }

    public function getEducation()
    {
        return $this->hasMany(Banner_Education::className(),['banner_id'=>'id']);
    }

    public function getImagePath()
    {
        return Yii::getAlias('@uploads_view/banners/'.$this->image);
    }

    public static function pages()
    {
        return [
            'present'=>'Подарки',
            'survey'=>'Анкеты',
            'seminar'=>'Семинары',
            'stock'=>'Акции',
            'presentation'=>'Презентации',
            'news' => 'Новости'
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
            case 'news':
                $item = News::findOne($path[1]);
                $name = 'Новость: ';
                break;
            case 'presentation':
                $item = Presentation::findOne($path[1]);
                $name = 'Презентация: ';
                break;
        }
        return $name.$item['title'];
    }


    public function getEducationsView($isFull = false)
    {
        $result = ArrayHelper::getColumn((Banner_Education::find()
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

    public function getPharmaciesView($isFull = false)
    {
        $result = ArrayHelper::getColumn((Banner_Pharmacy::find()
            ->select(new \yii\db\Expression("CONCAT(`name`, ' (', `address`,')') as name"))
            ->joinWith('pharmacy')
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


    public function getTypesView($isFull = false)
    {
        $result = ArrayHelper::getColumn((Banner_Type::find()
            ->select('name')
            ->joinWith('type')
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

    public function getCompanyView($isFull = false)
    {
        $result = ArrayHelper::getColumn((Company::find()->select([
            Company::tableName().'.title'])
            ->from(Company::tableName())
            ->join('LEFT JOIN', Pharmacy::tableName(),
                Company::tableName().'.id = '.Pharmacy::tableName().'.company_id')
            ->join('LEFT JOIN', Banner_Pharmacy::tableName(),
                Banner_Pharmacy::tableName().'.pharmacy_id = '.Pharmacy::tableName().'.id')
            ->distinct()
            ->asArray()
            ->where(['banner_id' => $this->id])
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

    public function loadImage()
    {
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

    public function beforeSave($insert)
    {
        if(parent::beforeSave($insert)) {
            $this->loadImage();
            return true;
        } else return false;
    }

    public function loadPharmacies($pharmacies)
    {
        if($pharmacies) {
            for ($i = 0; $i < count($pharmacies); $i++) {
                $pharmacy = new Banner_Pharmacy();
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
                $education = new Banner_Education();
                $education->education_id = $educations[$i];
                $education->banner_id = $this->id;
                $education->save();
            }
        }
    }

    public function loadTypes($types)
    {
        if($types) {
            for ($i = 0; $i < count($types); $i++) {
                $type = new Banner_Type();
                $type->type_id = $types[$i];
                $type->banner_id = $this->id;
                $type->save();
            }
        }
    }

    public function updateEducation($educations)
    {
        Banner_Education::deleteAll(['banner_id' => $this->id]);
        if($educations) {
            for ($i = 0; $i < count($educations); $i++) {
                $education = new Banner_Education();
                $education->education_id = $educations[$i];
                $education->banner_id = $this->id;
                $education->save();
            }
        }
    }

    public function updatePharmacies($pharmacies)
    {
        Banner_Pharmacy::deleteAll(['banner_id' => $this->id]);
        if($pharmacies) {
            for ($i = 0; $i < count($pharmacies); $i++) {
                $pharmacy = new Banner_Pharmacy();
                $pharmacy->pharmacy_id = $pharmacies[$i];
                $pharmacy->banner_id = $this->id;
                $pharmacy->save();
            }
        }
    }

    public function updateTypes($types)
    {
        Banner_Type::deleteAll(['banner_id' => $this->id]);
        if($types) {
            for ($i = 0; $i < count($types); $i++) {
                $type = new Banner_Type();
                $type->type_id = $types[$i];
                $type->banner_id = $this->id;
                $type->save();
            }
        }
    }

    public function afterDelete()
    {
        Banner_Education::deleteAll(['banner_id'=>$this->id]);
        Banner_Pharmacy::deleteAll(['banner_id'=>$this->id]);
        Banner_Type::deleteAll(['banner_id'=>$this->id]);

        if($this->image) @unlink(Yii::getAlias('@uploads/banners/'.$this->image));
        parent::afterDelete();
    }
}
