<?php

use yii\helpers\Html;
use yii\bootstrap\Modal;
use backend\components\CheckWidget;
use kartik\form\ActiveForm;
use common\models\location\Region;
use common\models\agency\Firm;
use kartik\widgets\Growl;
use kartik\widgets\Select2;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */

$this->title = 'Push-уведомления для групп';
$this->registerJsFile('js/checkWidget.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

$url = Url::to(['/users/push-groups/link-list']);

if(Yii::$app->session->hasFlash('PushMessage')) :
    echo Growl::widget([
        'type' => Growl::TYPE_SUCCESS,
        'title' => 'Успешно',
        'icon' => 'glyphicon glyphicon-ok-sign',
        'body' => Yii::$app->session->getFlash('PushMessage'),
        'showSeparator' => true,
        'delay' => 0,
        'pluginOptions' => [
            'placement' => [
                'from' => 'top',
                'align' => 'right',
            ]
        ]
    ]);
endif;

if(Yii::$app->session->hasFlash('PushMessage2')) :
    echo Growl::widget([
        'type' => Growl::TYPE_SUCCESS,
        'title' => 'Успешно',
        'icon' => 'glyphicon glyphicon-ok-sign',
        'body' => Yii::$app->session->getFlash('PushMessage2'),
        'showSeparator' => true,
        'delay' => 0,
        'pluginOptions' => [
            'placement' => [
                'from' => 'top',
                'align' => 'right',
            ]
        ]
    ]);
endif;
?>
<div class="user-push">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?php
    $regions = Region::find()->asArray()->all();
    $firms = Firm::find()->asArray()->all();


    echo $form->field($model, 'message')->textInput();

    echo $form->field($model, 'link')->widget(Select2::classname(),
        [
            'initValueText' => $model->linkTitle,
            'pluginOptions' => [
                'allowClear' => false,
                'minimumInputLength' => 0,
                'ajax' => [
                    'url' => $url,
                    'dataType' => 'json',
                    'data' => new JsExpression('function(params) { return {q:params.term}; }'),
                ],
                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                'templateResult' => new JsExpression('function(link) { return link.text; }'),
                'templateSelection' => new JsExpression('function (link) { return link.text; }'),
            ],
        ]
    );

    Modal::begin([
        'header' => '<h2>Выберите города</h2>',
        'toggleButton' => ['label' => 'Для городов', 'class' => 'btn btn-primary'],
    ]);

    echo $form->field($model, 'cities')->widget(CheckWidget::className(), [
        'parent_title' => 'regions',
        'parent' => $regions,

        'child_title' => 'cities',
        'child' => $cities,
        'relation' => 'region_id'
    ]);
    Modal::end();


    Modal::begin([
        'header' => '<h2>Выберите аптеки</h2>',
        'toggleButton' => ['label' => 'Для аптек', 'class' => 'btn btn-primary'],
    ]);
    echo $form->field($model, 'pharmacies')->widget(CheckWidget::className(), [
        'firms' => true,
        'color' => 'green',
        'parent_title' => 'firms',
        'parent' => $firms,
        'update' => $old_pharmacies,

        'child_title' => 'pharmacies',
        'child' => $pharmacies,
        'relation' => 'firm_id'

    ]);
    Modal::end();


    Modal::begin([
        'header' => '<h2>Выберите образования</h2>',
        'toggleButton' => ['label' => 'Для образований', 'class' => 'btn btn-primary'],
    ]);

    echo $form->field($model, 'education')->widget(CheckWidget::className(), [
        'parent_title' => 'education',
        'parent' => $education,
        'height' => '10px'
    ]);
    Modal::end();
    ?>

    <p></p>
    <div class="form-group">
        <?= Html::submitButton('Отправить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
