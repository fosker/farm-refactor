<?php
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\widgets\FileInput;
use yii\bootstrap\Modal;
use backend\components\CheckWidget;
use common\models\location\Region;
use common\models\profile\Education;
use common\models\Company;
use common\models\profile\Type;
use kartik\widgets\Select2;

use common\models\News;

use backend\components\Editor;


$this->registerJs("CKEDITOR.plugins.addExternal('dropler', 'http://pharmbonus.by/admin/js/dropler/');");
$this->registerJsFile('js/checkWidget.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>

<div class="news-form">

    <input type="checkbox" class="btn btn-info all-groups">Выбрать все</input>
    </br>
    </br>

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?php
    Modal::begin([
        'header' => '<h2>Выберите города</h2>',
        'toggleButton' => ['label' => 'Для городов', 'class' => 'btn btn-primary city'],
        'id' => 'cities'
    ]);

    echo $form->field(new Region(), '_')->widget(CheckWidget::className(), [
        'parent_title' => 'regions',
        'parent' => $regions,
        'parent_label' => 'name',
        'update' => $old_cities,

        'child_title' => 'cities',
        'child' => $cities,
        'relation' => 'region_id'
    ]);
    Modal::end();

    Modal::begin([
        'header' => '<h2>Выберите организации</h2>',
        'toggleButton' => ['label' => 'Для организаций', 'class' => 'btn btn-primary company'],
        'id' => 'companies'
    ]);

    echo $form->field(new Company(), '_')->widget(CheckWidget::className(), [
        'parent_title' => 'companies',
        'parent' => $companies,
        'parent_label' => 'title',
        'update' => $old_companies,

        'height' => '1px',
    ]);
    Modal::end();


    Modal::begin([
        'header' => '<h2>Выберите аптеки</h2>',
        'toggleButton' => ['label' => 'Для аптек', 'class' => 'btn btn-primary pharmacy'],
        'id' => 'pharmacies'
    ]);
    Modal::end();

    Modal::begin([
        'header' => '<h2>Выберите типы пользователей</h2>',
        'toggleButton' => ['label' => 'Для типов пользователей', 'class' => 'btn btn-primary type'],
        'id' => 'types'
    ]);

    echo $form->field(new Type, '_')->widget(CheckWidget::className(), [
        'parent_title' => 'types',
        'parent' => $types,
        'parent_label' => 'name',

        'update' => $old_types,
        'height' => '10px'
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

        'update' => $old_education,
        'height' => '10px'
    ]);
    Modal::end();
    ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'priority')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'factory_id')->widget(Select2::classname(), [
        'data' => $factories,
        'options' => ['placeholder' => 'Выберите компанию ...'],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); ?>

    <?= $form->field($model, 'text')->widget(Editor::className(), [
        'options' => ['rows' => 6],
        'clientOptions' => [
            'extraPlugins' => 'dropler',
            'droplerConfig' => [
                'backend' => 'basic',
                'settings' => [
                    'uploadUrl' => 'upload.php'
                ]
            ],
            'height' => 800
        ],
        'preset' => 'basic'
    ]); ?>

    <?= $form->field($model, 'imageFile')->widget(FileInput::classname(), [
        'pluginOptions' => [
            'initialPreview' => $model->image ? Html::img($model->imagePath, ['class' => 'file-preview-image', 'alt' => 'image', 'title' => 'Image']) : '',
            'showUpload' => false,
            'showRemove' => false,
        ]
    ]); ?>

    <?= $form->field($model, 'thumbFile')->widget(FileInput::classname(), [
        'pluginOptions' => [
            'initialPreview' => $model->thumbnail ? Html::img($model->thumbPath, ['class' => 'file-preview-image', 'alt' => 'thumb', 'title' => 'thumb']) : '',
            'showUpload' => false,
            'showRemove' => false,
        ]
    ]); ?>

    <?= $form->field($model, 'views_added')->textInput() ?>

    <label class="control-label">Показывать спискам</label>

    <div>

        <?= Html::checkbox('forList[]', in_array(0, $old_lists), ['value' => 0]) . 'нейтральному' ?>

        <?= Html::checkbox('forList[]', in_array(1, $old_lists), ['value' => 1]) . 'черному' ?>

        <?= Html::checkbox('forList[]', in_array(2, $old_lists), ['value' => 2]) . 'белому' ?>

        <?= Html::checkbox('forList[]', in_array(3, $old_lists), ['value' => 3]) . 'серому' ?>

    </div>

    <?php
    echo '<label class="control-label">Рекомендуемые новости</label>';
    echo Select2::widget([
        'name' => 'relations[]',
        'value' => array_keys($old_relations),
        'data' => $news,
        'options' => [
            'placeholder' => 'Выберите рекомендуемые новости ...',
            'multiple' => true,
        ],
    ]);
    ?>
    </br>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
