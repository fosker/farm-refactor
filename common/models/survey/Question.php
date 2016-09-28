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

    public function afterDelete()
    {
        parent::afterDelete();
        foreach($this->options as $option)
            $option->delete();
    }


    public static function transformRadioCommon($questions)
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

    public static function transformCheckboxCommon($questions, $survey)
    {
        $array = [];
        foreach($questions as $question) {
            $options = Option::find()->select('value')
                ->where(['question_id'=>$question->id])
                ->orderBy('value')
                ->asArray()
                ->all();
            $all_answers = Answer::find()->select('id, value, question_id')
                ->where(['question_id'=>$question->id])
                ->asArray()
                ->all();
            $counts = [];
            $all_answer_values = [];
            foreach($options as $option) {
                $counts[$option['value']] = 0;
            }
            foreach($all_answers as $answer) {
                $values = explode(';',$answer['value']);
                $all_answer_values[] = $values;
                foreach($values as $value) {
                    foreach($counts as $key => $option) {
                        if($value == $key) {
                            $c = 0;
                            for($i = 0; $i < count($all_answer_values); $i++) {
                                for($j = 0; $j < count($all_answer_values[$i]); $j++) {
                                    if($value == $all_answer_values[$i][$j]) {
                                        $c++;
                                    }
                                }
                            }
                            $counts[$key] = $c;
                        }
                    }
                }
            }
            $array[$question->id] = $counts;
        }

        foreach($array as $question_id => $question) {
            foreach($question as $option_id => $option) {
                $array[$question_id][$option_id] = $array[$question_id][$option_id]/$survey->answersCount*100;
            }
        }

        return $array;
    }


    public static function transformRadioRegions($questions, $sums)
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
        foreach($array as $question_id => $question) {
            foreach($question as $option_id => $option) {
                foreach($option as $region_id => $region) {
                    $array[$question_id][$option_id][$region_id] = $array[$question_id][$option_id][$region_id]/$sums[$question_id][$region_id]*100;
                }
            }
        }

        return $array;
    }

    public static function transformCheckboxRegions($questions, $sums)
    {
        $pre_array = [];
        $array = [];
        $regions = Region::find()
            ->orderBy('id')
            ->all();
        foreach($questions as $question) {
            $options = Option::find()->select('value')
                ->where(['question_id'=>$question->id])
                ->orderBy('value')
                ->asArray()
                ->all();
            foreach ($regions as $region) {
                $all_answers = Answer::find()
                    ->select('question_id, value, region_id')
                    ->from([Answer::tableName(), View::tableName(), Pharmacist::tableName(), Common_Pharmacy::tableName(), City::tableName()])
                    ->where(Answer::tableName().'.view_id ='.View::tableName().'.id')
                    ->andWhere(View::tableName().'.user_id ='.Pharmacist::tableName().'.id')
                    ->andWhere(Pharmacist::tableName().'.pharmacy_id ='.Common_Pharmacy::tableName().'.id')
                    ->andWhere(City::tableName().'.id ='.Common_Pharmacy::tableName().'.city_id')
                    ->andWhere([City::tableName().'.region_id'=>$region->id])
                    ->andWhere(['question_id'=>$question->id])
                    ->orderBy('value DESC')
                    ->asArray()
                    ->all();
                $counts = [];
                $all_answer_values = [];
                foreach($options as $option) {
                    $counts[$option['value']] = 0;
                }

                foreach($all_answers as $answer) {
                    $values = explode(';',$answer['value']);
                    $all_answer_values[] = $values;
                    foreach($values as $value) {
                        foreach($counts as $key => $option) {
                            if($value == $key) {
                                $c = 0;
                                for($i = 0; $i < count($all_answer_values); $i++) {
                                    for($j = 0; $j < count($all_answer_values[$i]); $j++) {
                                        if($value == $all_answer_values[$i][$j]) {
                                            $c++;
                                        }
                                    }
                                }
                                $counts[$key] = $c;
                            }
                        }
                    }
                    $pre_array[$question->id][$region->id] = $counts;
                }
            }
        }

        foreach($pre_array as $question_id => $regions) {
            foreach ($regions as $region_id => $options) {
                foreach($options as $option => $count) {
                    $array[$question_id][$option][$region_id] = $count;
                }
            }
        }

        foreach($array as $question_id => $question) {
            foreach($question as $option_id => $option) {
                foreach($option as $region_id => $region) {
                    $array[$question_id][$option_id][$region_id] = $array[$question_id][$option_id][$region_id]/reset($sums)[$region_id]*100;
                }
            }
        }

        return $array;
    }


    public static function transformRadioCompanies($questions, $survey, $sums)
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
        foreach($array as $question_id => $question) {
            foreach($question as $option_id => $option) {
                foreach($option as $company_id => $company) {
                    $array[$question_id][$option_id][$company_id] = $array[$question_id][$option_id][$company_id]/$sums[$question_id][$company_id]*100;
                }
            }
        }

        return $array;
    }

    public static function transformCheckboxCompanies($questions, $survey, $sums)
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
            $options = Option::find()->select('value')
                ->where(['question_id'=>$question->id])
                ->orderBy('value')
                ->asArray()
                ->all();
            foreach ($companies as $company) {
                $all_answers = Answer::find()
                    ->select('question_id, value, company_id')
                    ->from([Answer::tableName(), View::tableName(), Pharmacist::tableName(), Common_Pharmacy::tableName(), Company::tableName()])
                    ->where(Answer::tableName().'.view_id ='.View::tableName().'.id')
                    ->andWhere(View::tableName().'.user_id ='.Pharmacist::tableName().'.id')
                    ->andWhere(Pharmacist::tableName().'.pharmacy_id ='.Common_Pharmacy::tableName().'.id')
                    ->andWhere(Common_Pharmacy::tableName().'.company_id = '.Company::tableName().'.id')
                    ->andWhere([Company::tableName().'.id'=>$company->id])
                    ->andWhere(['question_id'=>$question->id])
                    ->orderBy('value DESC')
                    ->asArray()
                    ->all();
                $counts = [];
                $all_answer_values = [];
                foreach($options as $option) {
                    $counts[$option['value']] = 0;
                }

                foreach($all_answers as $answer) {
                    $values = explode(';',$answer['value']);
                    $all_answer_values[] = $values;
                    foreach($values as $value) {
                        foreach($counts as $key => $option) {
                            if($value == $key) {
                                $c = 0;
                                for($i = 0; $i < count($all_answer_values); $i++) {
                                    for($j = 0; $j < count($all_answer_values[$i]); $j++) {
                                        if($value == $all_answer_values[$i][$j]) {
                                            $c++;
                                        }
                                    }
                                }
                                $counts[$key] = $c;
                            }
                        }
                    }
                    $pre_array[$question->id][$company->id] = $counts;
                }
            }
        }

        foreach($pre_array as $question_id => $companies) {
            foreach ($companies as $company_id => $options) {
                foreach($options as $option => $count) {
                    $array[$question_id][$option][$company_id] = $count;
                }
            }
        }

        foreach($array as $question_id => $question) {
            foreach($question as $option_id => $option) {
                foreach($option as $company_id => $company) {
                    $array[$question_id][$option_id][$company_id] = $array[$question_id][$option_id][$company_id]/reset($sums)[$company_id]*100;
                }
            }
        }
        return $array;
    }


    public static function getRegionSums($questions)
    {
        $array = [];
        foreach($questions as $question)
        {
            $question_sum = ArrayHelper::map(Answer::find()
                ->select('question_id, value, region_id, count('.Answer::tableName().'.id) as count')
                ->from([Answer::tableName(), View::tableName(), Pharmacist::tableName(), Common_Pharmacy::tableName(), City::tableName()])
                ->where(Answer::tableName().'.view_id ='.View::tableName().'.id')
                ->andWhere(View::tableName().'.user_id ='.Pharmacist::tableName().'.id')
                ->andWhere(Pharmacist::tableName().'.pharmacy_id ='.Common_Pharmacy::tableName().'.id')
                ->andWhere(City::tableName().'.id ='.Common_Pharmacy::tableName().'.city_id')
                ->andWhere(['question_id'=>$question->id])
                ->groupBy('region_id, question_id')
                ->orderBy('region_id')
                ->asArray()
                ->all(),'region_id', 'count');
            $array[$question->id] = $question_sum;
        }

        return $array;
    }

    public static function getCompanySums($questions)
    {
        $array = [];
        foreach($questions as $question)
        {
            $question_sum = ArrayHelper::map(Answer::find()
                ->select('question_id, value, company_id, count('.Answer::tableName().'.id) as count')
                ->from([Answer::tableName(), View::tableName(), Pharmacist::tableName(), Common_Pharmacy::tableName(), Company::tableName()])
                ->where(Answer::tableName().'.view_id ='.View::tableName().'.id')
                ->andWhere(View::tableName().'.user_id ='.Pharmacist::tableName().'.id')
                ->andWhere(Pharmacist::tableName().'.pharmacy_id ='.Common_Pharmacy::tableName().'.id')
                ->andWhere(Common_Pharmacy::tableName().'.company_id ='.Company::tableName().'.id')
                ->andWhere(['question_id'=>$question->id])
                ->groupBy('company_id, question_id')
                ->orderBy('company_id')
                ->asArray()
                ->all(),'company_id', 'count');
            $array[$question->id] = $question_sum;
        }

        return $array;
    }




}
