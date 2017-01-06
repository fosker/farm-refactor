<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\imagine\Image;

use common\models\stock\Pharmacy as Stock_Pharmacy;
use common\models\stock\Education as Stock_Education;
use common\models\stock\Type as Stock_Type;
use common\models\stock\Reply;
use common\models\company\Pharmacy;
use common\models\Factory;
use common\models\profile\Type;

/**
 * This is the model class for table "stocks".
 *
 * @property integer $id
 * @property integer $factory_id
 * @property string $title
 * @property string $description
 * @property string $image
 * @property integer $status
 * @property string $email
 * @property integer $comment_type
 * @property integer $forList
 */
class Stock extends ActiveRecord
{
    const COMMENT_REQUIRED = 1;
    const COMMENT_NOT_REQUIRED = 2;
    const COMMENT_NOT = 3;

    const STATUS_ACTIVE = 1;
    const STATUS_HIDDEN = 0;

    public $imageFile;

    public static function tableName()
    {
        return 'stocks';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'description', 'factory_id', 'email', 'comment_type', 'forList'], 'required'],
            ['imageFile', 'required', 'on' => 'create'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'factory_id' => 'Компания Автор',
            'title' => 'Название акции',
            'description' => 'Описание',
            'image' => 'Изображение',
            'imageFile' => 'Изображение',
            'status' => 'Статус',
            'email' => 'Email',
            'comment_type' => 'Тип комментария',
            'forList' => 'Показывать списку'
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['create'] = ['title', 'description', 'factory_id', 'imageFile', 'email', 'comment_type', 'forList'];
        return $scenarios;
    }

    public function fields()
    {
        return [
            'id','image'=>'imagePath','title',
            'withComment' => function() {
                if ($this->comment_type == static::COMMENT_REQUIRED || $this->comment_type == static::COMMENT_NOT_REQUIRED) {
                    return true;
                } else
                    return false;
            }
        ];
    }

    public function extraFields()
    {
        return [
            'description'
        ];
    }

    /**
     * @return \yii\db\Query
     */
    public static function getForCurrentUser()
    {
        if(Yii::$app->user->identity->type_id == Type::TYPE_PHARMACIST) {
            $education = Stock_Education::find()->select('stock_id')->andFilterWhere(['education_id' => Yii::$app->user->identity->pharmacist->education_id]);
            $types = Stock_Type::find()->select('stock_id')->andFilterWhere(['type_id' => Yii::$app->user->identity->type_id]);
            $pharmacies = Stock_Pharmacy::find()->select('stock_id')->andFilterWhere(['pharmacy_id' => Yii::$app->user->identity->pharmacist->pharmacy_id]);
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
                ->orderBy([static::tableName().'.id'=>SORT_DESC]);
        } elseif (Yii::$app->user->identity->type_id == Type::TYPE_AGENT) {
            return static::find()
                ->joinWith('types')
                ->where([
                    'factory_id'=>Yii::$app->user->identity->agent->factory_id,
                    Stock_Type::tableName().'.type_id'=> Type::TYPE_PHARMACIST
                ])
                ->orWhere([
                    Stock_Type::tableName().'.type_id'=> Type::TYPE_AGENT,
                    'factory_id'=>[Yii::$app->user->identity->agent->factory_id, '1']
                ])
                ->andFilterWhere(['or', ['forList' => 1], ['and', ['forList' => 0], Yii::$app->user->identity->inList. '<> 1'],
                    ['and', ['forList' => 2], Yii::$app->user->identity->inList. '=2'],
                    ['and', ['forList' => 3], Yii::$app->user->identity->inList. '=1'],
                    ['and', ['forList' => 4], Yii::$app->user->identity->inList. '=0']
                ])
                ->andWhere(['status'=>static::STATUS_ACTIVE])
                ->orderBy([static::tableName().'.id'=>SORT_DESC])
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

    public function getFactory()
    {
        return $this->hasOne(Factory::className(),['id'=>'factory_id']);
    }

    public function getPharmacies()
    {
        return $this->hasMany(Stock_Pharmacy::className(),['stock_id'=>'id']);
    }

    public function getTypes()
    {
        return $this->hasMany(Stock_Type::className(),['stock_id'=>'id']);
    }

    public function getEducation()
    {
        return $this->hasMany(Stock_Education::className(),['stock_id'=>'id']);
    }

    public function getImagePath()
    {
        return Yii::getAlias('@uploads_view/stocks/'.$this->image);
    }


    public static function getStatusList()
    {
        return [static::STATUS_ACTIVE=>'активный',static::STATUS_HIDDEN=>'скрытый'];
    }

    public function getEducationsView($isFull = false)
    {
        $result = ArrayHelper::getColumn((Stock_Education::find()
            ->select('name')
            ->joinWith('education')
            ->asArray()
            ->where(['stock_id'=>$this->id])
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
        $result = ArrayHelper::getColumn((Stock_Pharmacy::find()
            ->select(new \yii\db\Expression("CONCAT(`name`, ' (', `address`,')') as name"))
            ->joinWith('pharmacy')
            ->asArray()
            ->where(['stock_id'=>$this->id])
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
        $result = ArrayHelper::getColumn((Stock_Type::find()
            ->select('name')
            ->joinWith('type')
            ->asArray()
            ->where(['stock_id'=>$this->id])
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
            ->join('LEFT JOIN', Stock_Pharmacy::tableName(),
                Stock_Pharmacy::tableName().'.pharmacy_id = '.Pharmacy::tableName().'.id')
            ->distinct()
            ->asArray()
            ->where(['stock_id' => $this->id])
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

    public function loadPharmacies($pharmacies)
    {
        if($pharmacies) {
            for ($i = 0; $i < count($pharmacies); $i++) {
                $pharmacy = new Stock_Pharmacy();
                $pharmacy->pharmacy_id = $pharmacies[$i];
                $pharmacy->stock_id = $this->id;
                $pharmacy->save();
            }
        }
    }

    public function loadEducation($educations)
    {
        if($educations) {
            for ($i = 0; $i < count($educations); $i++) {
                $education = new Stock_Education();
                $education->education_id = $educations[$i];
                $education->stock_id = $this->id;
                $education->save();
            }
        }
    }

    public function loadTypes($types)
    {
        if($types) {
            for ($i = 0; $i < count($types); $i++) {
                $type = new Stock_Type();
                $type->type_id = $types[$i];
                $type->stock_id = $this->id;
                $type->save();
            }
        }
    }

    public function updateEducation($educations)
    {
        Stock_Education::deleteAll(['stock_id' => $this->id]);
        if($educations) {
            for ($i = 0; $i < count($educations); $i++) {
                $education = new Stock_Education();
                $education->education_id = $educations[$i];
                $education->stock_id = $this->id;
                $education->save();
            }
        }
    }

    public function updatePharmacies($pharmacies)
    {
        Stock_Pharmacy::deleteAll(['stock_id' => $this->id]);
        if($pharmacies) {
            for ($i = 0; $i < count($pharmacies); $i++) {
                $pharmacy = new Stock_Pharmacy();
                $pharmacy->pharmacy_id = $pharmacies[$i];
                $pharmacy->stock_id = $this->id;
                $pharmacy->save();
            }
        }
    }

    public function deletePharmacies()
    {
        Stock_Pharmacy::deleteAll(['stock_id' => $this->id]);
    }

    public function updateTypes($types)
    {
        Stock_Type::deleteAll(['stock_id' => $this->id]);
        if($types) {
            for ($i = 0; $i < count($types); $i++) {
                $type = new Stock_Type();
                $type->type_id = $types[$i];
                $type->stock_id = $this->id;
                $type->save();
            }
        }
    }

    public function beforeSave($insert)
    {
        if(parent::beforeSave($insert)) {
            $this->loadImage();
            return true;
        } else return false;
    }

    public function afterDelete()
    {
        if($this->image) @unlink(Yii::getAlias('@uploads/stocks/'.$this->image));
        Stock_Education::deleteAll(['stock_id'=>$this->id]);
        Stock_Pharmacy::deleteAll(['stock_id'=>$this->id]);
        Stock_Type::deleteAll(['stock_id'=>$this->id]);
        Reply::deleteAll(['stock_id'=>$this->id]);
        parent::afterDelete();
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

    public function loadImage()
    {
        if($this->imageFile) {
            $path = Yii::getAlias('@uploads/stocks/');
            if($this->image && file_exists($path . $this->image))
                @unlink($path . $this->image);
            $filename = Yii::$app->getSecurity()->generateRandomString() . time() . '.' . $this->imageFile->extension;
            $path = $path . $filename;
            $this->imageFile->saveAs($path);
            $this->image = $filename;
            Image::thumbnail($path, 1000, 500)
                ->save(Yii::getAlias('@uploads/stocks/').$this->image, ['quality' => 80]);
        }
    }

}
