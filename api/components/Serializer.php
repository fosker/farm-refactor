<?php

namespace rest\components;


use yii\base\Arrayable;
use yii\base\Model;
use yii\data\DataProviderInterface;

class Serializer extends \yii\rest\Serializer
{

    public function serialize($data)
    {

        if ($data instanceof Model && $data->hasErrors()) {
            return $this->serializeModelErrors($data);
        } elseif ($data instanceof Arrayable) {
            return $this->serializeModel($data);
        } elseif ($data instanceof DataProviderInterface) {
            return $this->serializeDataProvider($data);
        } elseif($this->IsTabularInput($data)) {
            return $this->serializeTabularInput($data);
        } else {
            return $data;
        }
    }

    private function IsTabularInput($data)
    {
        if(!is_array($data)) return false;
        foreach($data as $model) {
            if(!($model instanceof Model)) return false;
        }
        return true;
    }

    private function serializeTabularInput($data)
    {
        $result = [];

        foreach($data as $key=>$model) {
            if($model->hasErrors()) {
                $result[$key] = $this->serializeModelErrors($model);
            } else {
                //$result[$key] = $this->serializeModel($model);
            }
        }

        return $result;
    }

}