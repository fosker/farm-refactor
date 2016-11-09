<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = 'Оценка сотрудника';
?>
<div class="rate-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'employee.fullname',
            [
                'attribute' => 'criterion.name',
                'label' => 'Критерий'
            ],
            'rate',
            'date',
            'comment:ntext',
        ],
    ]) ?>

</div>
