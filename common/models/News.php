<?php

namespace common\models;

use common\models\news\Relation;
use Yii;
use yii\imagine\Image;
use yii\helpers\ArrayHelper;

use common\models\news\Comment;
use common\models\news\Pharmacy as News_Pharmacy;
use common\models\news\Education as News_Education;
use common\models\news\Type as News_Type;
use common\models\company\Pharmacy;
use common\models\Factory;
use common\models\news\View;
use common\models\profile\Type;
use common\models\news\ForList;

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
 * @property integer $factory_id
 * @property integer $forList
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
            [['title', 'text', 'factory_id', 'forList'], 'required'],
            [['views_added', 'factory_id'], 'integer'],
            [['imageFile', 'thumbFile'], 'required', 'on' => 'create'],
            [['title', 'text', 'date'], 'string'],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['create'] = ['title', 'text', 'imageFile', 'thumbFile', 'factory_id', 'views_added', 'forList'];
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
            'views' => 'Уникальных просмотров',
            'views_added' => 'Добавленные просмотры',
            'factory_id' => 'Фабрика Автор',
            'forList' => 'Показывать списку',
        ];
    }

    public function fields()
    {
        return [
            'id', 'title', 'thumb' => 'thumbPath',
            'author' => 'factory',
            'views' => function () {
                return $this->countUniqueViews();
            },
            'isViewed' => function () {
                return $isViewed = $this->isViewedByCurrentUser();
            },
            'image' => 'imagePath',
            'date' => function ($model) {
                return strtotime($model->date);
            }
        ];
    }

    public function extraFields()
    {
        return [
            'text',
            'recommended',
        ];
    }

    public function getRecommended()
    {
        $ids = Relation::find()->select('child_id')->where(['parent_id' => $this->id]);
        return News::find()->where(['in', 'id', $ids])->all();
    }

    public function getComments()
    {
        return $this->hasMany(Comment::className(), ['news_id' => 'id']);
    }

    public function getPharmacies()
    {
        return $this->hasMany(News_Pharmacy::className(), ['news_id' => 'id']);
    }

    public function getTypes()
    {
        return $this->hasMany(News_Type::className(), ['news_id' => 'id']);
    }

    public function getFactory()
    {
        return $this->hasOne(Factory::className(), ['id' => 'factory_id']);
    }

    public function getEducation()
    {
        return $this->hasMany(News_Education::className(), ['news_id' => 'id']);
    }

    public static function getForCurrentUser()
    {
        if (Yii::$app->user->identity->type_id == Type::TYPE_PHARMACIST) {
            $education = News_Education::find()->select('news_id')->andFilterWhere(['education_id' => Yii::$app->user->identity->pharmacist->education_id]);
            $types = News_Type::find()->select('news_id')->andFilterWhere(['type_id' => Yii::$app->user->identity->type_id]);
            $pharmacies = News_Pharmacy::find()->select('news_id')->andFilterWhere(['pharmacy_id' => Yii::$app->user->identity->pharmacist->pharmacy_id]);
            return static::find()
                ->andFilterWhere(['in', static::tableName() . '.id', $education])
                ->andFilterWhere(['in', static::tableName() . '.id', $types])
                ->andFilterWhere(['in', static::tableName() . '.id', $pharmacies])
                ->andFilterWhere(['or', ['forList' => 1], ['and', ['forList' => 0], Yii::$app->user->identity->inList. '<> 1'],
                    ['and', ['forList' => 2], Yii::$app->user->identity->inList. '=2'],
                    ['and', ['forList' => 3], Yii::$app->user->identity->inList. '=1'],
                    ['and', ['forList' => 4], Yii::$app->user->identity->inList. '=0'],
                    ['and', ['forList' => 5], Yii::$app->user->identity->inList. '=3']
                ])
                ->orderBy(['date' => SORT_DESC]);
        } elseif (Yii::$app->user->identity->type_id == Type::TYPE_AGENT) {
            return static::find()
                ->andFilterWhere(['or', ['factory_id' => 10], ['factory_id' => Yii::$app->user->identity->agent->factory_id]])
                ->andFilterWhere(['or', ['forList' => 1], ['and', ['forList' => 0], Yii::$app->user->identity->inList. '<> 1'],
                    ['and', ['forList' => 2], Yii::$app->user->identity->inList. '=2'],
                    ['and', ['forList' => 3], Yii::$app->user->identity->inList. '=1'],
                    ['and', ['forList' => 4], Yii::$app->user->identity->inList. '=0'],
                ])
                ->orderBy(['date' => SORT_DESC])
                ->groupBy(static::tableName() . '.id');
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
        return static::find()->where(['id' => $id])->one();
    }

    public function countUniqueViews()
    {
        $this->views = View::find()->select('user_id')->
            distinct()->where(['news_id' => $this->id])->count() + $this->views_added;
        return $this->views;
    }

    public function countRealViews()
    {
        $this->views = View::find()->select('user_id')->
        distinct()->where(['news_id' => $this->id])->count();
        return $this->views;
    }

    public function isViewedByCurrentUser()
    {
        return View::findOne(['news_id' => $this->id, 'user_id' => Yii::$app->user->id]) !== null;
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->loadImage();
            $this->loadThumb();
            return true;
        } else return false;
    }

    public function afterDelete()
    {
        parent::afterDelete();
        News_Education::deleteAll(['news_id' => $this->id]);
        News_Pharmacy::deleteAll(['news_id' => $this->id]);
        News_Type::deleteAll(['news_id' => $this->id]);
        Comment::deleteAll(['news_id' => $this->id]);
        if ($this->image) @unlink(Yii::getAlias('@uploads/news/' . $this->image));
        if ($this->thumbnail) @unlink(Yii::getAlias('@uploads/news/thumbs/' . $this->thumbnail));
    }

    public function loadImage()
    {
        if ($this->imageFile) {
            $path = Yii::getAlias('@uploads/news/');
            if ($this->image && file_exists($path . $this->image))
                @unlink($path . $this->image);
            $filename = Yii::$app->getSecurity()->generateRandomString() . time() . '.' . $this->imageFile->extension;
            $path = $path . $filename;
            $this->imageFile->saveAs($path);
            $this->image = $filename;
            Image::thumbnail($path, 1000, 500)
                ->save(Yii::getAlias('@uploads/news/') . $this->image, ['quality' => 80]);
        }
    }

    public function loadThumb()
    {
        if ($this->thumbFile) {
            $path = Yii::getAlias('@uploads/news/thumbs/');
            if ($this->thumbnail && file_exists($path . $this->thumbnail))
                @unlink($path . $this->thumbnail);
            $filename = Yii::$app->getSecurity()->generateRandomString() . time() . '.' . $this->thumbFile->extension;
            $path = $path . $filename;
            $this->thumbFile->saveAs($path);
            $this->thumbnail = $filename;
            Image::thumbnail($path, 200, 300)
                ->save(Yii::getAlias('@uploads/news/thumbs/') . $this->thumbnail, ['quality' => 80]);
        }
    }

    public function getImagePath()
    {
        return Yii::getAlias('@uploads_view/news/' . $this->image);
    }

    public function getThumbPath()
    {
        return Yii::getAlias('@uploads_view/news/thumbs/' . $this->thumbnail);
    }

    public function getTypesView($isFull = false)
    {
        $result = ArrayHelper::getColumn((News_Type::find()
            ->select('name')
            ->joinWith('type')
            ->asArray()
            ->where(['news_id' => $this->id])
            ->all()), 'name');

        $string = "";
        if (!$isFull) {
            $limit = 5;
            if (count($result) > $limit) {
                for ($i = 0; $i < $limit; $i++) {
                    $string .= $result[$i] . ", ";
                }
                $string .= "и ещё (" . (count($result) - $limit) . ")";
            } else
                $string = implode(", ", $result);
        } else
            $string = implode(", ", $result);

        return $string;
    }

    public function getCompanyView($isFull = false)
    {
        $result = ArrayHelper::getColumn((Company::find()->select([
            Company::tableName() . '.title'])
            ->from(Company::tableName())
            ->join('LEFT JOIN', Pharmacy::tableName(),
                Company::tableName() . '.id = ' . Pharmacy::tableName() . '.company_id')
            ->join('LEFT JOIN', News_Pharmacy::tableName(),
                News_Pharmacy::tableName() . '.pharmacy_id = ' . Pharmacy::tableName() . '.id')
            ->distinct()
            ->asArray()
            ->where(['news_id' => $this->id])
            ->all()), 'title');

        $string = "";
        if (!$isFull) {
            $limit = 5;
            if (count($result) > $limit) {
                for ($i = 0; $i < $limit; $i++) {
                    $string .= $result[$i] . ", ";
                }
                $string .= "и ещё (" . (count($result) - $limit) . ")";
            } else
                $string = implode(", ", $result);
        } else
            $string = implode(", ", $result);

        return $string;
    }

    public function getPharmaciesView($isFull = false)
    {
        $result = ArrayHelper::getColumn((News_Pharmacy::find()
            ->select(new \yii\db\Expression("CONCAT(`name`, ' (', `address`,')') as name"))
            ->joinWith('pharmacy')
            ->asArray()
            ->where(['news_id' => $this->id])
            ->all()), 'name');

        $string = "";
        if (!$isFull) {
            $limit = 5;
            if (count($result) > $limit) {
                for ($i = 0; $i < $limit; $i++) {
                    $string .= $result[$i] . ", ";
                }
                $string .= "и ещё (" . (count($result) - $limit) . ")";
            } else
                $string = implode(", ", $result);
        } else
            $string = implode(", ", $result);

        return $string;
    }

    public function getEducationsView($isFull = false)
    {
        $result = ArrayHelper::getColumn((News_Education::find()
            ->select('name')
            ->joinWith('education')
            ->asArray()
            ->where(['news_id' => $this->id])
            ->all()), 'name');

        $string = "";
        if (!$isFull) {
            $limit = 5;
            if (count($result) > $limit) {
                for ($i = 0; $i < $limit; $i++) {
                    $string .= $result[$i] . ", ";
                }
                $string .= "и ещё (" . (count($result) - $limit) . ")";
            } else
                $string = implode(", ", $result);
        } else
            $string = implode(", ", $result);

        return $string;
    }

    public function loadPharmacies($pharmacies)
    {
        if ($pharmacies) {
            for ($i = 0; $i < count($pharmacies); $i++) {
                $pharmacy = new News_Pharmacy();
                $pharmacy->pharmacy_id = $pharmacies[$i];
                $pharmacy->news_id = $this->id;
                $pharmacy->save();
            }
        }
    }

    public function loadEducation($educations)
    {
        if ($educations) {
            for ($i = 0; $i < count($educations); $i++) {
                $education = new News_Education();
                $education->education_id = $educations[$i];
                $education->news_id = $this->id;
                $education->save();
            }
        }
    }

    public function loadTypes($types)
    {
        if ($types) {
            for ($i = 0; $i < count($types); $i++) {
                $type = new News_Type();
                $type->type_id = $types[$i];
                $type->news_id = $this->id;
                $type->save();
            }
        }
    }

    public function loadRelations($relations)
    {
        if ($relations) {
            for ($i = 0; $i < count($relations); $i++) {
                $relation = new Relation();
                $relation->child_id = $relations[$i];
                $relation->parent_id = $this->id;
                $relation->save();
            }
        }
    }

    public function loadLists($lists)
    {
        if ($lists) {
            for ($i = 0; $i < count($lists); $i++) {
                $list = new ForList();
                $list->list = $lists[$i];
                $list->news_id = $this->id;
                $list->save();
            }
        }
    }

    public function updateEducation($educations)
    {
        News_Education::deleteAll(['news_id' => $this->id]);
        if ($educations) {
            for ($i = 0; $i < count($educations); $i++) {
                $education = new News_Education();
                $education->education_id = $educations[$i];
                $education->news_id = $this->id;
                $education->save();
            }
        }
    }

    public function updatePharmacies($pharmacies)
    {
        News_Pharmacy::deleteAll(['news_id' => $this->id]);
        if ($pharmacies) {
            for ($i = 0; $i < count($pharmacies); $i++) {
                $pharmacy = new News_Pharmacy();
                $pharmacy->pharmacy_id = $pharmacies[$i];
                $pharmacy->news_id = $this->id;
                $pharmacy->save();
            }
        }
    }

    public function deletePharmacies()
    {
        News_Pharmacy::deleteAll(['news_id' => $this->id]);
    }

    public function updateTypes($types)
    {
        News_Type::deleteAll(['news_id' => $this->id]);
        if ($types) {
            for ($i = 0; $i < count($types); $i++) {
                $type = new News_Type();
                $type->type_id = $types[$i];
                $type->news_id = $this->id;
                $type->save();
            }
        }
    }

    public function updateRelations($relations)
    {
        Relation::deleteAll(['parent_id' => $this->id]);
        if ($relations) {
            for ($i = 0; $i < count($relations); $i++) {
                $relation = new Relation();
                $relation->child_id = $relations[$i];
                $relation->parent_id = $this->id;
                $relation->save();
            }
        }
    }

    public function updateLists($lists)
    {
        ForList::deleteAll(['news_id' => $this->id]);
        if ($lists) {
            for ($i = 0; $i < count($lists); $i++) {
                $list = new ForList();
                $list->list = $lists[$i];
                $list->news_id = $this->id;
                $list->save();
            }
        }
    }
}
