<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = $model->name;

?>
<div class="admin-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Список', ['index'],['class'=>'btn btn-info']) ?>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Права', ['rights', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы уверены, что хотите удалить администратора?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'email:email',
            'name',
        ],
    ]) ?>

    <table class="table">
        <tr><th>Право</th><th>Значение</th></tr>

        <?php foreach($model->rights as $right) : ?>
            <tr><td><?=$right->right->name?></td><td><?= $right->value ? 'да' : 'нет'; ?></td></tr>
        <?php endforeach; ?>
    </table>
</div>
