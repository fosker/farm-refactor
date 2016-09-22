<?php

namespace common\models\survey;


use Yii;
use yii\helpers\ArrayHelper;
use yii\db\ActiveRecord;

use common\models\Survey;
use common\models\Company;
use common\models\location\Region;
use common\models\user\Pharmacist;
use common\models\location\City;
use common\models\company\Pharmacy as Common_Pharmacy;

/**
 * This is the model class for table "survey_questions".
 *
 * @property integer $id
 * @property string $question
 * @property integer $survey_id
 * @property integer $right_answers
 * @property integer $validAnswer
 */
class Question extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'survey_questions';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['question', 'right_answers'], 'required'],
            [['question', 'validAnswer'], 'string'],
            ['right_answers', 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'question' => 'Вопрос',
            'right_answers' => 'Количество правильных ответов',
            'validAnswer' => 'Правильный ответ'
        ];
    }

    public function fields()
    {
        return [
            'id', 'question', 'options', 'right_answers', 'validAnswer'
        ];
    }

    public function getSurvey()
    {
        return $this->hasOne(Survey::className(), ['id' => 'survey_id']);
    }

    public function getOptions()
    {
        return $this->hasMany(Option::className(), ['question_id' => 'id']);
    }

    public static function transformCommon($questions)
    {
        $array = [];
        foreach($questions as $question) {
            $subQuery = Answer::find()
                ->select('count('.Answer::tableName().'.id)')
                ->where(Answer::tableName().'.question_id ='.Option::tableName().'.question_id')
                ->andWhere(Option::tableName().'.value ='.Answer::tableName().'.value');
            $query = Option::find()
                ->select([Option::tableName().'.question_id', Option::tableName().'.value'])
                ->addSelect(['count' => $subQuery])
                ->where(['question_id' => $question->id])
                ->groupBy(Option::tableName().'.value');
            $array[$question->id] = ArrayHelper::map($query
                ->orderBy('value')
                ->asArray()
                ->all(),'value','count');
        }
        return $array;
    }

    public static function transformForRegions($questions)
    {
        $pre_array = [];
        $array = [];
        $regions = Region::find()
            ->orderBy('id')
            ->all();
        foreach($questions as $question) {
            foreach($regions as $region) {
                $subQuery = Answer::find()
                    ->select('count('.Answer::tableName().'.id)')
                    ->from([Answer::tableName(), View::tableName(), Pharmacist::tableName(), Common_Pharmacy::tableName(), City::tableName()])
                    ->where(Answer::tableName().'.question_id ='.Option::tableName().'.question_id')
                    ->andWhere(Option::tableName().'.value ='.Answer::tableName().'.value')
                    ->andWhere(Answer::tableName().'.view_id ='.View::tableName().'.id')
                    ->andWhere(View::tableName().'.user_id ='.Pharmacist::tableName().'.id')
                    ->andWhere(Pharmacist::tableName().'.pharmacy_id ='.Common_Pharmacy::tableName().'.id')
                    ->andWhere(City::tableName().'.id ='.Common_Pharmacy::tableName().'.city_id')
                    ->andWhere(City::tableName().'.region_id='.$region->id);
                $query = Option::find()
                    ->select([Option::tableName().'.question_id', Option::tableName().'.value'])
                    ->addSelect(['count' => $subQuery])
                    ->where('question_id ='.$question->id)
                    ->groupBy(Option::tableName().'.value');
                $pre_array[$question->id][$region->id] = ArrayHelper::map($query
                    ->orderBy('value')
                    ->asArray()
                    ->all(),'value','count');
            }
        }
        foreach($pre_array as $question_id => $regions) {
            foreach ($regions as $region_id => $options) {
                foreach($options as $option => $count) {
                    $array[$question_id][$option][$region_id] = $count;
                }
            }
        }
        return $array;
    }

    public static function transformForCompanies($questions, $survey)
    {
        $pre_array = [];
        $array = [];
        $companies = Company::find()
            ->select(Company::tableName().'.id, count('.View::tableName().'.survey_id) as count')
            ->from([Survey::tableName(), View::tableName(),
                Pharmacist::tableName(), Common_Pharmacy::tableName(), Company::tableName()])
            ->where(Survey::tableName().'.id ='.View::tableName().'.survey_id')
            ->andWhere(View::tableName().'.user_id ='.Pharmacist::tableName().'.id')
            ->andWhere(Pharmacist::tableName().'.pharmacy_id ='.Common_Pharmacy::tableName().'.id')
            ->andWhere(Common_Pharmacy::tableName().'.company_id ='.Company::tableName().'.id')
            ->andWhere([Survey::tableName().'.id' => $survey->id])
            ->groupBy(Company::tableName().'.id')
            ->orderBy('count DESC')
            ->limit(7)
            ->all();
        foreach($questions as $question) {
            foreach($companies as $company) {
                $subQuery = Answer::find()
                    ->select('count('.Answer::tableName().'.id)')
                    ->from([Answer::tableName(), View::tableName(), Pharmacist::tableName(), Common_Pharmacy::tableName(), Company::tableName()])
                    ->where(Answer::tableName().'.question_id ='.Option::tableName().'.question_id')
                    ->andWhere(Option::tableName().'.value ='.Answer::tableName().'.value')
                    ->andWhere(Answer::tableName().'.view_id ='.View::tableName().'.id')
                    ->andWhere(View::tableName().'.user_id ='.Pharmacist::tableName().'.id')
                    ->andWhere(Pharmacist::tableName().'.pharmacy_id ='.Common_Pharmacy::tableName().'.id')
                    ->andWhere(Common_Pharmacy::tableName().'.company_id ='.Company::tableName().'.id')
                    ->andWhere(Common_Pharmacy::tableName().'.company_id ='.$company->id);
                $query = Option::find()
                    ->select([Option::tableName().'.question_id', Option::tableName().'.value'])
                    ->addSelect(['count' => $subQuery])
                    ->where('question_id ='.$question->id)
                    ->groupBy(Option::tableName().'.value');
                $pre_array[$question->id][$company->id] = ArrayHelper::map($query
                    ->orderBy('value')
                    ->asArray()
                    ->all(),'value','count');
            }
        }
        foreach($pre_array as $question_id => $companies) {
            foreach ($companies as $company_id => $options) {
                foreach($options as $option => $count) {
                    $array[$question_id][$option][$company_id] = $count;
                }
            }
        }
        return $array;
    }

    public function afterDelete()
    {
        parent::afterDelete();
        foreach($this->options as $option)
            $option->delete();
    }

}
