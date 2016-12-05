<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\PharmCompany */

$this->title = $model->name;
?>
<div class="pharm-company-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Список', ['index'], ['class' => 'btn btn-info']) ?>
        <?php if ($model->admin_id == Yii::$app->admin->id): ?>
            <?= Html::a('Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Вы действительно хотите удалить фарм. компанию?',
                    'method' => 'post',
                ],
            ]) ?>
        <?php endif; ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            [
                'label' => 'Кто создал',
                'attribute' => 'admin.name'
            ],
            'type',
            'location',
            'size',
            'rx_otc',
            'first_visit',
            'planned_visit',
        ],
    ]) ?>

    <h4 class="text-center">Комментарии</h4>
    <div class="row">
        <div class="col-md-5">

            <?php $form = ActiveForm::begin(['action' => ['add-comment']]); ?>

            <?= $form->field($comment, 'text')->textarea(['maxlength' => true]) ?>

            <?= $form->field($comment, 'author_id')->hiddenInput(['value' => Yii::$app->admin->id])->label(false) ?>

            <?= $form->field($comment, 'pharm_company_id')->hiddenInput(['value' => $model->id])->label(false) ?>

            <div class="form-group">
                <?= Html::submitButton('Добавить комментарий', ['class' => 'btn btn-success']) ?>
            </div>

            <?php ActiveForm::end(); ?>

        </div>
    </div>

    <div class="col-md-6">
        <?php if(!$model->comments) {
            echo "Комментариев нет.";
        } else {
        foreach ($model->comments as $comment): ?>
        <div class="row">
            <div class="col-md-2">
                <div class="row">
                    <p class="text-center"><?=$comment->admin->name?></p>
                </div>
            </div>
            <div class="col-md-10" style="word-wrap: break-word;">
                <p><?=$comment->text?></p>
                <h6><i><?=$comment->date_add?></i></h6>
            </div>
        </div>
        </br>
<?php endforeach;
}?>
    </div>

</div>