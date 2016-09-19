<?php

use yii\helpers\Html;
use yii\bootstrap\Modal;
use scotthuangzl\googlechart\GoogleChart;


$this->title = "Статистика просмотров для новости: $model->title";
?>
<div class="news-stats">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Вернуться к новости', ['view', 'id' => $model->id],['class'=>'btn btn-info']) ?>
    </p>

    <div class="col-md-12">
<?php

    echo GoogleChart::widget([
        'visualization' => 'LineChart',
        'data' => [
            ['Task', 'Просмотров'],
            ['Понедельник', (int)$days_views[1]],
            ['Вторник', (int)$days_views[2]],
            ['Среда', (int)$days_views[3]],
            ['Четверг', (int)$days_views[4]],
            ['Пятница', (int)$days_views[5]],
            ['Суббота', (int)$days_views[6]],
            ['Воскресенье', (int)$days_views[7]]
        ],
        'options' => [
            'width' => '1100',
            'height' => '300',
            'title' => 'Просмотры по дням недели',
            'legend' => [
                'position' => 'none'
            ],
        ]
    ]);
?>
    </div>

    <div class="col-md-12">

<?php
    echo GoogleChart::widget([
        'visualization' => 'LineChart',
        'data' => [
            ['Task', 'Просмотров'],
            ['00:00', intval($hours_views[0])],
            ['01:00', intval($hours_views[1])],
            ['02:00', intval($hours_views[2])],
            ['03:00', intval($hours_views[3])],
            ['04:00', intval($hours_views[4])],
            ['05:00', intval($hours_views[5])],
            ['06:00', intval($hours_views[6])],
            ['07:00', intval($hours_views[7])],
            ['08:00', intval($hours_views[8])],
            ['09:00', intval($hours_views[9])],
            ['10:00', intval($hours_views[10])],
            ['11:00', intval($hours_views[11])],
            ['12:00', intval($hours_views[12])],
            ['13:00', intval($hours_views[13])],
            ['14:00', intval($hours_views[14])],
            ['15:00', intval($hours_views[15])],
            ['16:00', intval($hours_views[16])],
            ['17:00', intval($hours_views[17])],
            ['18:00', intval($hours_views[18])],
            ['19:00', intval($hours_views[19])],
            ['20:00', intval($hours_views[20])],
            ['21:00', intval($hours_views[21])],
            ['22:00', intval($hours_views[22])],
            ['23:00', intval($hours_views[23])],
        ],
        'options' => [
            'vAxis' => [
                'format' => '#',
                'gridlines' => [
                    'count' => 6
                ]
            ],
            'width' => '1100',
            'height' => '300',
            'title' => 'Просмотры по часам',
            'legend' => [
                'position' => 'none'
            ],
        ],
    ]);

?>
    </div>
</div>