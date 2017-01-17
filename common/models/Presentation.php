<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\imagine\Image;
use common\models\presentation\View;
use common\models\presentation\Slide;
use common\models\presentation\Question;
use common\models\presentation\Comment;
use common\models\presentation\Pharmacy as Presentation_Pharmacy;
use common\models\presentation\Education as Presentation_Education;
use common\models\presentation\Type as Presentation_Type;
use common\models\company\Pharmacy;
use common\models\Factory;
use common\models\profile\Type;


/**
 * This is the model class for table "presentations".
 *
 * @property integer $id
 * @property string $title
 * @property string $description
 * @property integer $points
 * @property string $image
 * @property integer $factory_id
 * @property string $thumbnail
 * @property integer $status
 * @property integer $home
 * @property integer $home_priority
 * @property integer $views_limit
 * @property integer $forList
 * @property string $date_added
 */
class Presentation extends ActiveRecord
{

    const STATUS_ACTIVE = 1;
    const STATUS_HIDDEN = 0;

    const HOME_ACTIVE = 1;
    const HOME_HIDDEN = 0;

    public $imageFile;
    public $thumbFile;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'presentations';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'description', 'points', 'factory_id', 'forList'], 'required'],
            [['points', 'home_priority', 'views_limit'], 'integer'],
            [['imageFile','thumbFile'], 'required', 'on' => 'create'],
            [['date_added'], 'safe']
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['create'] = ['title', 'description', 'points', 'imageFile','thumbFile', 'views_limit', 'factory_id', 'forList', 'date_added'];
        return $scenarios;
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Название презентации',
            'description' => 'Описание',
            'points' => 'Баллы',
            'imageFile' => 'Изображение',
            'thumbFile' => 'Превью',
            'image' => 'Изображение',
            'thumbnail' => 'Превью',
            'status' => 'Статус',
            'home' => 'Отображать на главной',
            'home_priority' => 'Приоритет',
            'views_limit' => 'Ограничение просмотров',
            'factory_id' => 'Компания Автор',
            'forList' => 'Показывать списку',
            'date_added' => 'Дата и время публикации'
        ];
    }

    public function fields()
    {
        return [
            'id', 'title', 'points', 'thumb'=>'thumbPath',
        ];
    }

    public function extraFields()
    {
        return [
            'description', 'questions', 'image'=>'imagePath', 'slides','viewed'=>function($model) {
                return static::isViewedByCurrentUser($model->id);
            }
        ];
    }

    public static function getForCurrentUser()
    {
        if(Yii::$app->user->identity->type_id == Type::TYPE_PHARMACIST) {
            $education = Presentation_Education::find()->select('presentation_id')->andFilterWhere(['education_id' => Yii::$app->user->identity->pharmacist->education_id]);
            $types = Presentation_Type::find()->select('presentation_id')->andFilterWhere(['type_id' => Yii::$app->user->identity->type_id]);
            $pharmacies = Presentation_Pharmacy::find()->select('presentation_id')->andFilterWhere(['pharmacy_id' => Yii::$app->user->identity->pharmacist->pharmacy_id]);
            return static::find()
                ->andWhere(['status'=>static::STATUS_ACTIVE])
                ->andFilterWhere(['in', static::tableName().'.id', $education])
                ->andFilterWhere(['in', static::tableName().'.id', $types])
                ->andFilterWhere(['in', static::tableName().'.id', $pharmacies])
                ->andFilterWhere(['like', 'forList', Yii::$app->user->identity->inList])
                ->orderBy(['id'=>SORT_DESC]);
        } elseif (Yii::$app->user->identity->type_id == Type::TYPE_AGENT) {
            return static::find()
                ->joinWith('types')
                ->where([
                    'factory_id'=>Yii::$app->user->identity->agent->factory_id,
                    Presentation_Type::tableName().'.type_id'=> Type::TYPE_PHARMACIST
                ])
                ->orWhere([
                    Presentation_Type::tableName().'.type_id'=> Type::TYPE_AGENT,
                    'factory_id'=>[Yii::$app->user->identity->agent->factory_id, '1']
                ])
                ->andWhere(['!=', 'views_limit', '0'])
                ->andFilterWhere(['like', 'forList', Yii::$app->user->identity->inList])
                ->andWhere(['status'=>static::STATUS_ACTIVE])
                ->orderBy(['id'=>SORT_DESC])
                ->groupBy(static::tableName().'.id');
        }
    }

    public static function getNotViewedForCurrentUser()
    {
        return static::getForCurrentUser()
            ->andWhere(['!=', 'views_limit', '0'])
            ->andWhere([
                'not exists',
                View::findByCurrentUser()
                    ->andWhere(View::tableName().'.presentation_id='.static::tableName().'.id')
            ]);
    }

    public static function getViewedForCurrentUser()
    {
        return static::getForCurrentUser()
            ->andWhere([
                'exists',
                View::findByCurrentUser()
                    ->andWhere(View::tableName().'.presentation_id='.static::tableName().'.id')
            ]);
    }

    public static function getOneForCurrentUser($id)
    {
        return static::getForCurrentUser()->andWhere([static::tableName().'.id'=>$id])->one();
    }

    public function getComments()
    {
        return $this->hasMany(Comment::className(),['presentation_id'=>'id']);
    }

    public function getQuestions()
    {
        return $this->hasMany(Question::className(), ['presentation_id' => 'id'])->orderBy('order_index');
    }

    public function getSlides()
    {
        return Slide::find()->select('*')->from(Slide::tableName())->where(['presentation_id' => $this->id])->orderBy('order_index')->all();
    }

    public function getFactory()
    {
        return $this->hasOne(Factory::className(),['id'=>'factory_id']);
    }

    public function getPharmacies()
    {
        return $this->hasMany(Presentation_Pharmacy::className(),['presentation_id'=>'id']);
    }

    public function getTypes()
    {
        return $this->hasMany(Presentation_Type::className(),['presentation_id'=>'id']);
    }

    public function getEducation()
    {
        return $this->hasMany(Presentation_Education::className(),['presentation_id'=>'id']);
    }

    public function getViews()
    {
        return $this->hasMany(View::className(), ['presentation_id' => 'id']);
    }

    public function getImagePath()
    {
        return Yii::getAlias('@uploads_view/presentations/'.$this->image);
    }

    public function getThumbPath()
    {
        return Yii::getAlias('@uploads_view/presentations/thumbs/'.$this->thumbnail);
    }

    public static function isViewedByCurrentUser($id)
    {
        return View::findByCurrentUser()->andWhere(['presentation_id'=>$id])->exists();
    }

    public function approve()
    {
        $this->status = static::STATUS_ACTIVE;
        $this->save(false);
    }

    public function approveHome()
    {
        $this->home = static::HOME_ACTIVE;
        $this->save(false);
    }

    public function hide()
    {
        $this->status = static::STATUS_HIDDEN;
        $this->save(false);
    }

    public function hideHome()
    {
        $this->home = static::HOME_HIDDEN;
        $this->save(false);
    }

    public function getAnswersCount()
    {
        return View::find()
            ->where(['presentation_id'=>$this->id])
            ->count();
    }

    public static function getStatusList()
    {
        return [static::STATUS_ACTIVE=>'активный',static::STATUS_HIDDEN=>'скрытый'];
    }

    public static function getHomeStatusList()
    {
        return [static::HOME_ACTIVE=>'да',static::HOME_HIDDEN=>'нет'];
    }

    public function getEducationsView($isFull = false)
    {
        $result = ArrayHelper::getColumn((Presentation_Education::find()
            ->select('name')
            ->joinWith('education')
            ->asArray()
            ->where(['presentation_id'=>$this->id])
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
        $result = ArrayHelper::getColumn((Presentation_Pharmacy::find()
            ->select(new \yii\db\Expression("CONCAT(`name`, ' (', `address`,')') as name"))
            ->joinWith('pharmacy')
            ->asArray()
            ->where(['presentation_id'=>$this->id])
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
        $result = ArrayHelper::getColumn((Presentation_Type::find()
            ->select('name')
            ->joinWith('type')
            ->asArray()
            ->where(['presentation_id'=>$this->id])
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
            ->join('LEFT JOIN', Presentation_Pharmacy::tableName(),
                Presentation_Pharmacy::tableName().'.pharmacy_id = '.Pharmacy::tableName().'.id')
            ->distinct()
            ->asArray()
            ->where(['presentation_id' => $this->id])
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

    public function loadImage()
    {
        if($this->imageFile) {
            $path = Yii::getAlias('@uploads/presentations/');
            if ($this->image && file_exists($path . $this->image))
                @unlink($path . $this->image);
            $filename = Yii::$app->getSecurity()->generateRandomString() . time() . '.' . $this->imageFile->extension;
            $path = $path . $filename;
            $this->imageFile->saveAs($path);
            $this->image = $filename;
            Image::thumbnail($path, 1000, 500)
                ->save(Yii::getAlias('@uploads/presentations/') . $this->image, ['quality' => 80]);
        }
    }

    public function loadThumb()
    {
        if($this->thumbFile) {
            $path = Yii::getAlias('@uploads/presentations/thumbs/');
            if ($this->thumbnail && file_exists($path . $this->thumbnail))
                @unlink($path . $this->thumbnail);
            $filename = Yii::$app->getSecurity()->generateRandomString() . time() . '.' . $this->thumbFile->extension;
            $path = $path . $filename;
            $this->thumbFile->saveAs($path);
            $this->thumbnail = $filename;
            Image::thumbnail($path, 200, 300)
                ->save(Yii::getAlias('@uploads/presentations/thumbs/') . $this->thumbnail, ['quality' => 80]);
        }
    }

    public function beforeSave($insert)
    {
        if(parent::beforeSave($insert)) {
            $this->loadImage();
            $this->loadThumb();
            return true;
        } else return false;
    }

    public function loadPharmacies($pharmacies)
    {
        if($pharmacies) {
            for ($i = 0; $i < count($pharmacies); $i++) {
                $pharmacy = new Presentation_Pharmacy();
                $pharmacy->pharmacy_id = $pharmacies[$i];
                $pharmacy->presentation_id = $this->id;
                $pharmacy->save();
            }
        }
    }

    public function loadEducation($educations)
    {
        if($educations) {
            for ($i = 0; $i < count($educations); $i++) {
                $education = new Presentation_Education();
                $education->education_id = $educations[$i];
                $education->presentation_id = $this->id;
                $education->save();
            }
        }
    }

    public function loadTypes($types)
    {
        if($types) {
            for ($i = 0; $i < count($types); $i++) {
                $type = new Presentation_Type();
                $type->type_id = $types[$i];
                $type->presentation_id = $this->id;
                $type->save();
            }
        }
    }

    public function updateEducation($educations)
    {
        Presentation_Education::deleteAll(['presentation_id' => $this->id]);
        if($educations) {
            for ($i = 0; $i < count($educations); $i++) {
                $education = new Presentation_Education();
                $education->education_id = $educations[$i];
                $education->presentation_id = $this->id;
                $education->save();
            }
        }
    }

    public function updatePharmacies($pharmacies)
    {
        Presentation_Pharmacy::deleteAll(['presentation_id' => $this->id]);
        if($pharmacies) {
            for ($i = 0; $i < count($pharmacies); $i++) {
                $pharmacy = new Presentation_Pharmacy();
                $pharmacy->pharmacy_id = $pharmacies[$i];
                $pharmacy->presentation_id = $this->id;
                $pharmacy->save();
            }
        }
    }

    public function deletePharmacies()
    {
        Presentation_Pharmacy::deleteAll(['presentation_id' => $this->id]);
    }

    public function updateTypes($types)
    {
        Presentation_Type::deleteAll(['presentation_id' => $this->id]);
        if($types) {
            for ($i = 0; $i < count($types); $i++) {
                $type = new Presentation_Type();
                $type->type_id = $types[$i];
                $type->presentation_id = $this->id;
                $type->save();
            }
        }
    }

    public function afterDelete()
    {
        foreach($this->questions as $question)
            $question->delete();
        foreach($this->views as $view)
            $view->delete();
        foreach($this->slides as $slide)
        {
            if($slide->image) @unlink(Yii::getAlias('@uploads/presentations/slides/'.$slide->image));
            $slide->delete();
        }
        Presentation_Education::deleteAll(['presentation_id'=>$this->id]);
        Presentation_Pharmacy::deleteAll(['presentation_id'=>$this->id]);
        Presentation_Type::deleteAll(['presentation_id'=>$this->id]);
        Comment::deleteAll(['presentation_id'=>$this->id]);
        if($this->image) @unlink(Yii::getAlias('@uploads/presentations/'.$this->image));
        if($this->thumbnail) @unlink(Yii::getAlias('@uploads/presentations/thumbs/'.$this->thumbnail));
        parent::afterDelete();
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
