<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
use backend\components\Editor;
use yii\helpers\ArrayHelper;
use common\models\Form;
?>
<div class="theme-form">

    <?php $form = ActiveForm::begin([]); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'factory_id')->widget(Select2::classname(), [
        'data' => $factories,
        'options' => ['placeholder' => 'Выберите компанию ...'],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); ?>
    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'description')->widget(Editor::className(), [
        'options' => ['rows' => 6],
        'preset' => 'click'
    ]); ?>
    <?= $form->field($model, 'form_id')->widget(Select2::classname(), [
        'data' => ArrayHelper::map(Form::find()->all(), 'id', 'title'),
        'options' => ['placeholder' => 'Выберите форму (необязательно) ...'],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); ?>

    <?= $form->field($model, 'status')->dropDownList($model->getStatusList()) ?>

    <label class="control-label">Показывать спискам</label>

    <div>

        <?= Html::checkbox('forList[]', in_array(0, $old_lists), ['value' => 0]) . 'нейтральному' ?>

        <?= Html::checkbox('forList[]', in_array(1, $old_lists), ['value' => 1]) . 'черному' ?>

        <?= Html::checkbox('forList[]', in_array(2, $old_lists), ['value' => 2]) . 'белому' ?>

        <?= Html::checkbox('forList[]', in_array(3, $old_lists), ['value' => 3]) . 'серому' ?>

    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
