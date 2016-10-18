<?php

use yii\helpers\Html;
use yii\bootstrap\Modal;
use backend\components\CheckWidget;
use kartik\form\ActiveForm;
use common\models\location\Region;
use common\models\profile\Education;
use common\models\Company;
use common\models\Factory;
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
?>
<div class="user-push">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?php

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
    echo $form->field(new Region(), '_')->widget(CheckWidget::className(), [
        'parent_title' => 'regions',
        'parent' => $regions,
        'parent_label' => 'name',
        'child_title' => 'cities',
        'child' => $cities,
        'relation' => 'region_id'
    ]);
    Modal::end();

    Modal::begin([
        'header' => '<h2>Выберите образования</h2>',
        'toggleButton' => ['label' => 'Для образований', 'class' => 'btn btn-primary education'],
        'id' => 'education'
    ]);

    echo $form->field(new Education, '_')->widget(CheckWidget::className(), [
        'parent_title' => 'education',
        'parent' => $education,
        'parent_label' => 'name',

        'height' => '10px'
    ]);
    Modal::end();


    Modal::begin([
        'header' => '<h2>Выберите аптеки</h2>',
        'toggleButton' => ['label' => 'Для аптек', 'class' => 'btn btn-primary company'],
        'id' => 'companies'
    ]);

    echo $form->field(new Company(), '_')->widget(CheckWidget::className(), [
        'parent_title' => 'companies',
        'parent' => $companies,
        'parent_label' => 'title',
        'child_title' => 'pharmacies',
        'child' => $pharmacies,
        'relation' => 'company_id',
    ]);
    Modal::end();


    Modal::begin([
        'header' => '<h2>Выберите фабрики</h2>',
        'toggleButton' => ['label' => 'Для фабрик', 'class' => 'btn btn-primary factory'],
        'id' => 'factories'
    ]);

    echo $form->field(new Factory(), '_')->widget(CheckWidget::className(), [
        'parent_title' => 'factories',
        'parent' => $factories,
        'parent_label' => 'title',

        'height' => '1px',
    ]);
    Modal::end();

    echo $form->field($model, 'grayList')->checkbox();

    ?>
    <?= $form->field($model, 'type')->radioList([
        1=>'Переход на определенный экран в приложение (со списка) (без подробного экрана о уведомлении)',
        2=>'Переход на подробный экран уведомления без кнопки ознакомился',
        3=>'Переход на подробный экран уведомления с кнопкой ознакомился'
    ]); ?>

    <p></p>
    <div class="form-group">
        <?= Html::submitButton('Отправить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
