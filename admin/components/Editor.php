<?php

namespace backend\components;

use dosamigos\ckeditor\CKEditor;
use Yii;
use yii\helpers\ArrayHelper;

class Editor extends CKEditor {

    protected function initOptions()
    {
        $options = [];
        switch ($this->preset) {
            case 'custom':
                $preset = null;
                break;
            case 'basic':
            case 'full':
            case 'standard':
                $preset = __DIR__ . '/editor/' . $this->preset . '.php';
                break;
            case 'click':
                $preset = __DIR__ . '/editor/click.php';
                break;
            default:
                $preset = __DIR__ . '/editor/standard.php';
        }
        if ($preset !== null) {
            $options = require($preset);
        }
        $this->clientOptions = ArrayHelper::merge($options, $this->clientOptions);
    }

}