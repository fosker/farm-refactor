<?php

namespace common\models;

use Yii;
use backend\models\Admin;
use common\models\pharm_company\Comment;

/**
 * This is the model class for table "pharm_company".
 *
 * @property integer $id
 * @property string $name
 * @property integer $admin_id
 * @property string $type
 * @property string $location
 * @property string $size
 * @property string $rx_otc
 * @property string $first_visit
 * @property string $planned_visit
 */
class PharmCompany extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pharm_company';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'admin_id', 'type', 'location', 'size', 'rx_otc'], 'required'],
            [['admin_id'], 'integer'],
            [['first_visit', 'planned_visit'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['type', 'location', 'size'], 'string', 'max' => 100],
            [['rx_otc'], 'string', 'max' => 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'admin_id' => 'Кто добавил',
            'type' => 'Тип',
            'location' => 'Расположение',
            'size' => 'Размер',
            'rx_otc' => 'RX/OTX',
            'first_visit' => 'Дата первого визита',
            'planned_visit' => 'Дата запланированного визита',
        ];
    }

    public function getAdmin()
    {
        return $this->hasOne(Admin::className(),['id'=>'admin_id']);
    }

    public function getComments()
    {
        return $this->hasMany(Comment::className(),['pharm_company_id'=>'id']);
    }

    public function afterDelete()
    {
        parent::afterDelete();
        Comment::deleteAll(['pharm_company_id'=>$this->id]);
    }
}
