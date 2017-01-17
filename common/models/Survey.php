<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\imagine\Image;
use yii\helpers\ArrayHelper;

use common\models\profile\Type;
use common\models\survey\View;
use common\models\survey\Question;
use common\models\survey\Pharmacy as Survey_Pharmacy;
use common\models\survey\Education as Survey_Education;
use common\models\survey\Type as Survey_Type;
use common\models\company\Pharmacy;
use common\models\user\Pharmacist;
use common\models\Factory;

/**
 * This is the model class for table "surveys".
 *
 * @property integer $id
 * @property string $title
 * @property string $description
 * @property integer $points
 * @property integer $factory_id
 * @property string $image
 * @property string $thumbnail
 * @property integer $status
 * @property integer $views_limit
 * @property integer $forList
 * @property string $date_added
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
            [['points', 'views_limit'], 'integer'],
            [['title', 'description', 'points', 'factory_id', 'forList'], 'required'],
            [['imageFile', 'thumbFile'], 'required', 'on' => 'create'],
            [['date_added'], 'safe']

        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['create'] = ['title', 'description', 'points', 'imageFile', 'thumbFile', 'views_limit', 'factory_id', 'forList'];
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
            'thumbFile' => 'Превью',
            'views_limit' => 'Ограничение просмотров',
            'factory_id' => 'Компания Автор',
            'forList' => 'Показывать списку',
            'date_added' => 'Дата и время публикации'
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
     * @return \yii\db\ActiveQuery
     */
    public static function getForCurrentUser()
    {
        if(Yii::$app->user->identity->type_id == Type::TYPE_PHARMACIST) {
            $education = Survey_Education::find()->select('survey_id')->andFilterWhere(['education_id' => Yii::$app->user->identity->pharmacist->education_id]);
            $types = Survey_Type::find()->select('survey_id')->andFilterWhere(['type_id' => Yii::$app->user->identity->type_id]);
            $pharmacies = Survey_Pharmacy::find()->select('survey_id')->andFilterWhere(['pharmacy_id' => Yii::$app->user->identity->pharmacist->pharmacy_id]);
            return static::find()
                ->andWhere([static::tableName().'.status'=>static::STATUS_ACTIVE])
                ->andFilterWhere(['in', static::tableName().'.id', $education])
                ->andFilterWhere(['in', static::tableName().'.id', $types])
                ->andFilterWhere(['in', static::tableName().'.id', $pharmacies])
                ->andFilterWhere(['like', 'forList', Yii::$app->user->identity->inList])
                ->andWhere(['!=', 'views_limit', '0'])
                ->andWhere([
                    'not exists',
                    View::findByCurrentUser()
                        ->andWhere(View::tableName().'.survey_id='.static::tableName().'.id')
                ])
                ->orderBy(['id'=>SORT_DESC]);
        } elseif (Yii::$app->user->identity->type_id == Type::TYPE_AGENT) {
            return static::find()
                ->joinWith('types')
                ->where([
                    'factory_id'=>Yii::$app->user->identity->agent->factory_id,
                    Survey_Type::tableName().'.type_id'=> Type::TYPE_PHARMACIST
                ])
                ->orWhere([
                    Survey_Type::tableName().'.type_id'=> Type::TYPE_AGENT,
                    'factory_id'=>[Yii::$app->user->identity->agent->factory_id, '1']
                ])
                ->andFilterWhere(['like', 'forList', Yii::$app->user->identity->inList])
                ->andWhere(['!=', 'views_limit', '0'])
                ->andWhere([
                    'not exists',
                    View::findByCurrentUser()
                        ->andWhere(View::tableName().'.survey_id='.static::tableName().'.id')
                ])
                ->andWhere(['status'=>static::STATUS_ACTIVE])
                ->orderBy(['id'=>SORT_DESC])
                ->groupBy(static::tableName().'.id');
        }
    }

    public static function getOneForCurrentUser($id)
    {
        return static::getForCurrentUser()
            ->joinWith('questions')
            ->andWhere([static::tableName().'.id'=>$id])
            ->one();
    }

    public function getFactory()
    {
        return $this->hasOne(Factory::className(),['id'=>'factory_id']);
    }

    public function getTypes()
    {
        return $this->hasMany(Survey_Type::className(),['survey_id'=>'id']);
    }

    public function getQuestions()
    {
        return $this->hasMany(Question::className(), ['survey_id' => 'id']);
    }

    public function getEducation()
    {
        return $this->hasMany(Survey_Education::className(),['survey_id'=>'id']);
    }

    public function getPharmacies()
    {
        return $this->hasMany(Survey_Pharmacy::className(), ['survey_id' => 'id']);
    }

    public function getImagePath()
    {
        return Yii::getAlias('@uploads_view/surveys/'.$this->image);
    }

    public function getThumbPath()
    {
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

    public function getEducationsView($isFull = false)
    {
        $result = ArrayHelper::getColumn((Survey_Education::find()
            ->select('name')
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

    public function getPharmaciesView($isFull = false)
    {
        $result = ArrayHelper::getColumn((Survey_Pharmacy::find()
            ->select(new \yii\db\Expression("CONCAT(`name`, ' (', `address`,')') as name"))
            ->joinWith('pharmacy')
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


    public function getTypesView($isFull = false)
    {
        $result = ArrayHelper::getColumn((Survey_Type::find()
            ->select('name')
            ->joinWith('type')
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

    public function getCompanyView($isFull = false)
    {
        $result = ArrayHelper::getColumn((Company::find()->select([
            Company::tableName().'.title'])
            ->from(Company::tableName())
            ->join('LEFT JOIN', Pharmacy::tableName(),
                Company::tableName().'.id = '.Pharmacy::tableName().'.company_id')
            ->join('LEFT JOIN', Survey_Pharmacy::tableName(),
                Survey_Pharmacy::tableName().'.pharmacy_id = '.Pharmacy::tableName().'.id')
            ->distinct()
            ->asArray()
            ->where(['survey_id' => $this->id])
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
        Survey_Education::deleteAll(['survey_id'=>$this->id]);
        Survey_Pharmacy::deleteAll(['survey_id'=>$this->id]);
        Survey_Type::deleteAll(['survey_id'=>$this->id]);
        if($this->image) @unlink(Yii::getAlias('@uploads/surveys/'.$this->image));
        if($this->thumbnail) @unlink(Yii::getAlias('@uploads/surveys/thumbs/'.$this->thumbnail));
        parent::afterDelete();
    }

    public function loadPharmacies($pharmacies)
    {
        if($pharmacies) {
            for ($i = 0; $i < count($pharmacies); $i++) {
                $pharmacy = new Survey_Pharmacy();
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
                $education = new Survey_Education();
                $education->education_id = $educations[$i];
                $education->survey_id = $this->id;
                $education->save();
            }
        }
    }

    public function loadTypes($types)
    {
        if($types) {
            for ($i = 0; $i < count($types); $i++) {
                $type = new Survey_Type();
                $type->type_id = $types[$i];
                $type->survey_id = $this->id;
                $type->save();
            }
        }
    }

    public function updateEducation($educations)
    {
        Survey_Education::deleteAll(['survey_id' => $this->id]);
        if($educations) {
            for ($i = 0; $i < count($educations); $i++) {
                $education = new Survey_Education();
                $education->education_id = $educations[$i];
                $education->survey_id = $this->id;
                $education->save();
            }
        }
    }

    public function updatePharmacies($pharmacies)
    {
        Survey_Pharmacy::deleteAll(['survey_id' => $this->id]);
        if($pharmacies) {
            for ($i = 0; $i < count($pharmacies); $i++) {
                $pharmacy = new Survey_Pharmacy();
                $pharmacy->pharmacy_id = $pharmacies[$i];
                $pharmacy->survey_id = $this->id;
                $pharmacy->save();
            }
        }
    }

    public function deletePharmacies()
    {
        Survey_Pharmacy::deleteAll(['survey_id' => $this->id]);
    }

    public function updateTypes($types)
    {
        Survey_Type::deleteAll(['survey_id' => $this->id]);
        if($types) {
            for ($i = 0; $i < count($types); $i++) {
                $type = new Survey_Type();
                $type->type_id = $types[$i];
                $type->survey_id = $this->id;
                $type->save();
            }
        }
    }

    public function getDevidedQuestions()
    {
        foreach($this->questions as $question) {
            if($question->right_answers == 1 && $question->options) {
                $radio_questions[] = $question;
            } elseif($question->right_answers > 1 && $question->options) {
                $checkbox_questions[] = $question;
            } else {
                $free_questions[] = $question;
            }
        }
        return [
            'radio' => $radio_questions,
            'checkbox' => $checkbox_questions,
            'free' => $free_questions
        ];
    }
}
