<?php

namespace common\models\presentation;

use Yii;
use yii\db\ActiveRecord;
use common\models\User;
use common\models\Presentation;
/**
 * This is the model class for table "presentation_view_unique".
 *
 * @property integer $user_id
 * @property integer $presentation_id
 */
class Unique extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'presentation_view_unique';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [

        ];
    }

    /**
     * @return \yii\db\Query
     */

    public function getUser()
    {
        return $this->hasOne(User::className(),['id'=>'user_id']);
    }

    public function getPresentation()
    {
        return $this->hasOne(Presentation::className(),['id'=>'presentation_id']);
    }

}
