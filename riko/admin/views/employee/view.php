<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use admin\models\Rate;

$this->title = $model->name;
?>
<div class="employee-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы действительно хотите удалить сотрудника?',
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a('Оценить', ['rate', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'surname',
            'phone',
            [
                'label' => 'Отдел',
                'attribute' => 'department.name'
            ],
            [
                'label' => 'Должность',
                'attribute' => 'position.name'
            ]

        ],
    ]) ?>

    <h3 class="text-center">Оценки</h3>
    <table class="table">
        <tr>
            <td></td>
            <?php for($i = 1; $i <= 10; $i++):?>
                <th><?=$i?></th>
            <?php endfor;?>
        </tr>
        <?php $sums = [];?>
        <?php foreach ($criteria as $criterion):?>
            <tr>
                <td><?=$criterion->customName?></td>
                <?php $criterionRates = Rate::findAll(['employee_id' => $model->id, 'criterion_id' => $criterion->id]);
                    foreach($criterionRates as $index => $criterionRate):?>
                        <?php $sums[$index] += $criterion->cash_multiplier * $criterionRate->rate; ?>
                        <td><?=Html::a($criterionRate->rate, ['/rate/view', 'id' => $criterionRate->id], ['class'=>'btn btn-info'])?></td>
                    <?php endforeach;?>
                <td>
            </tr>
        <?php endforeach;?>
        <tr>
            <td>Сумма</td>
            <?php foreach ($sums as $sum):?>
                <td><?=$sum?></td>
            <?php endforeach;?>
        </tr>
    </table>

</div>
