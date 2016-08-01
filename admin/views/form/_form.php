<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\components\Editor;
use wbraganca\dynamicform\DynamicFormWidget;

$this->registerJsFile('admin/js/form-constructor.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
<div class="theme-form">

    <?php $form = ActiveForm::begin(['options' => ['id'=>'theme-form']]); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'description')->widget(Editor::className(), [
        'options' => ['rows' => 6],
        'preset' => 'click'
    ]); ?>

    <div class="row panel-body">
        <?php DynamicFormWidget::begin([
            'widgetContainer' => 'dynamicform_wrapper',
            'widgetBody' => '.container-fields',
            'widgetItem' => '.field-item',
            'insertButton' => '.add-field',
            'deleteButton' => '.del-field',
            'model' => $fields[0],
            'formId' => 'theme-form',
            'formFields' => [
                'field',
            ],
        ]); ?>

        <h4>Поля</h4>
        <table class="table table-bordered">
            <thead>
            <tr class="active">
                <td></td>
                <td><label class="control-label">Поле</label></td>
                <td><label class="control-label">Тип поля</label></td>
                <td><label class="control-label">Варианты</label></td>
            </tr>
            </thead>

            <tbody class="container-fields">
            <?php foreach ($fields as $i => $field): ?>

                <tr class="field-item">
                    <td>
                        <button type="button" class="del-field btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
                        <?php
                        if (! $field->isNewRecord) {
                            echo Html::activeHiddenInput($field, "[{$i}]id");
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        echo $form->field($field, "[{$i}]label")->begin();
                        echo Html::activeTextInput($field, "[{$i}]label", ['maxlength' => true, 'class' => 'form-control']); //Field
                        echo Html::error($field,"[{$i}]label", ['class' => 'help-block']); //error
                        echo $form->field($field, "[{$i}]field")->end();
                        ?>
                    </td>

                    <td>
                        <table>
                            <tr>
                                <td>
                                    <?php
                                    echo $form->field($field, "[{$i}]type")->dropDownList([
                                        '1' => 'Радиокнопка',
                                        '2' => 'Текстовое поле',
                                        '3' => 'Большое текстовое поле',
                                        '4' => 'Чекбокс',
                                        '5' => 'Выпадающий список'
                                    ]);
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <?php
                                    echo $form->field($field, "[{$i}]isRequired")->checkbox();
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </td>

                    <td id="field_options">

                        <?php DynamicFormWidget::begin([
                            'widgetContainer' => 'dynamicform_inner',
                            'widgetBody' => '.container-options',
                            'widgetItem' => '.option-item',
                            'insertButton' => '.add-option',
                            'deleteButton' => '.del-option',
                            'min'=>0,
                            'model' => $options[$i][0],
                            'formId' => 'theme-form',
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
                                        <button type="button" class="del-option btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
                                        <?php
                                        // necessary for update action.
                                        if (! $option->isNewRecord) {
                                            echo Html::activeHiddenInput($option, "[{$i}][{$ix}]id");
                                        }
                                        ?>
                                    </td>

                                    <td>
                                        <?php
                                        echo $form->field($option, "[{$i}][{$ix}]value")->begin();
                                        echo Html::activeTextInput($option, "[{$i}][{$ix}]value", ['maxlength' => true, 'class' => 'form-control']); //Field
                                        echo Html::error($option,"[{$i}][{$ix}]value", ['class' => 'help-block']); //error
                                        echo $form->field($option, "[{$i}][{$ix}]value")->end();
                                        ?>
                                    </td>

                                </tr>
                            <?php endforeach; // end of options loop ?>
                            </tbody>
                            <tfoot>
                            <td colspan="5" class="active"><button type="button" class="add-option btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button></td>
                            </tfoot>
                        </table>
                        <?php DynamicFormWidget::end(); // end of options widget ?>

                    </td> <!-- options sub column -->
                </tr><!-- question -->
            <?php endforeach; // end of questions loop ?>
            </tbody>
            <tfoot>
            <td colspan="5" class="active">
                <button type="button" class="add-field btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button>
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
