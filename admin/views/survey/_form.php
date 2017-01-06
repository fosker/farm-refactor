<?php

use kartik\widgets\Select2;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\widgets\FileInput;
use backend\components\Editor;
use wbraganca\dynamicform\DynamicFormWidget;
use common\models\location\Region;
use common\models\profile\Education;
use common\models\Company;
use common\models\profile\Type;
use backend\components\CheckWidget;
use yii\bootstrap\Modal;

$this->registerJsFile('js/checkWidget.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('js/answer.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>

<div class="survey-form">

    <input type="checkbox" class="btn btn-info all-groups">Выбрать все</input>
    </br>
    </br>

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'id' => 'survey-form']]); ?>

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

    <?= $form->field($model, 'factory_id')->widget(Select2::classname(), [
        'data' => $factories,
        'options' => ['placeholder' => 'Выберите компанию ...'],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); ?>

    <?= $form->field($model, 'points')->textInput() ?>

    <?= $form->field($model, 'description')->widget(Editor::className(), [
        'options' => ['rows' => 6],
        'preset' => 'basic'
    ]); ?>

    <?= $form->field($model, 'views_limit')->textInput() ?>

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

    <?= $form->field($model, 'forList')->radioList([0 => 'нейтральному и белому', 1 => 'всем', 2 => 'только белому',
        3 => 'только черному', 4 => 'только нейтральному', 5 => 'только серому']) ?>

    <!-- The Questions on the Survey -->
    <div class="row panel-body">
        <?php DynamicFormWidget::begin([
            'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
            'widgetBody' => '.container-questions', // required: css class selector
            'widgetItem' => '.question-item', // required: css class
            'insertButton' => '.add-question', // css class
            'deleteButton' => '.del-question', // css class
            'model' => $questions[0],
            'formId' => 'survey-form',
            'formFields' => [
                'question',
            ],
        ]); ?>


        <h4>Вопросы</h4>
        <table class="table table-bordered">
            <thead>
            <tr class="active">
                <td></td>
                <td><?= Html::activeLabel($questions[0], 'question'); ?></td>
                <td><label class="control-label">Количество правильных ответов</label></td>
                <td><label class="control-label">Варианты ответа</label></td>
            </tr>
            </thead>

            <tbody class="container-questions"><!-- widgetContainer -->
            <?php foreach ($questions as $i => $question): ?>

                <tr class="question-item"><!-- widgetBody -->
                    <td>
                        <button type="button" class="del-question btn btn-danger btn-xs"><i
                                class="glyphicon glyphicon-minus"></i></button>
                        <?php
                        if (!$question->isNewRecord) {
                            echo Html::activeHiddenInput($question, "[{$i}]id");
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        echo $form->field($question, "[{$i}]question")->begin();
                        echo Html::activeTextInput($question, "[{$i}]question", ['maxlength' => true, 'class' => 'form-control']); //Field
                        echo Html::error($question, "[{$i}]question", ['class' => 'help-block']); //error
                        echo $form->field($question, "[{$i}]question")->end();
                        ?>
                    </td>

                    <td>
                        <table>
                            <tr>
                                <td>
                                    <?php
                                    echo $form->field($question, "[{$i}]right_answers")->textInput();
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <?php
                                    echo $form->field($question, "[{$i}]validAnswer")->textInput();
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </td>

                    <!-- The Options on the Question -->
                    <td id="questions_options">

                        <?php DynamicFormWidget::begin([
                            'widgetContainer' => 'dynamicform_inner', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                            'widgetBody' => '.container-options', // required: css class selector
                            'widgetItem' => '.option-item', // required: css class
                            'insertButton' => '.add-option', // css class
                            'deleteButton' => '.del-option', // css class
                            'min' => 0,
                            'model' => $options[$i][0],
                            'formId' => 'survey-form',
                            'formFields' => [
                                'value',
                            ],
                        ]);

                        ?>

                        <table class="table table-bordered">
                            <thead>
                            <tr class="active">
                                <td></td>
                                <td><?= Html::activeLabel($options[$i][0], 'value'); ?></td>
                            </tr>
                            </thead>
                            <tbody class="container-options"><!-- widgetContainer -->
                            <?php foreach ($options[$i] as $ix => $option): ?>
                                <tr class="option-item"><!-- widgetBody -->
                                    <td>
                                        <button type="button" class="del-option btn btn-danger btn-xs"><i
                                                class="glyphicon glyphicon-minus"></i></button>
                                        <?php
                                        // necessary for update action.
                                        if (!$option->isNewRecord) {
                                            echo Html::activeHiddenInput($option, "[{$i}][{$ix}]id");
                                        }
                                        ?>
                                    </td>

                                    <td>
                                        <?php
                                        echo $form->field($option, "[{$i}][{$ix}]value")->begin();
                                        echo Html::activeTextInput($option, "[{$i}][{$ix}]value", ['maxlength' => true, 'class' => 'form-control']); //Field
                                        echo Html::error($option, "[{$i}][{$ix}]value", ['class' => 'help-block']); //error
                                        echo $form->field($option, "[{$i}][{$ix}]value")->end();
                                        echo $form->field($option, "[{$i}][{$ix}]isValid")->checkbox();
                                        ?>
                                    </td>

                                </tr>
                            <?php endforeach; // end of options loop ?>
                            </tbody>
                            <tfoot>
                            <td colspan="5" class="active">
                                <button type="button" class="add-option btn btn-success btn-xs"><i
                                        class="glyphicon glyphicon-plus"></i></button>
                            </td>
                            </tfoot>
                        </table>
                        <?php DynamicFormWidget::end(); // end of options widget ?>

                    </td>
                    <!-- options sub column -->
                </tr><!-- question -->
            <?php endforeach; // end of questions loop ?>
            </tbody>
            <tfoot>
            <td colspan="5" class="active">
                <button type="button" class="add-question btn btn-success btn-xs"><i
                        class="glyphicon glyphicon-plus"></i></button>
            </td>
            </tfoot>
        </table>
        <?php DynamicFormWidget::end(); // end of questions widget ?>

    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
