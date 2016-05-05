<?php

namespace backend\components;

use yii\widgets\InputWidget;
use yii\helpers\Html;


class CheckWidget extends InputWidget
{
    public $parent = [];
    public $child = [];

    public $parent_title;

    public $parent_label;

    public $child_title;

    public $relation;

    public $update = [];

    private $values = [];

    public $height;

    public $pharmacy;

    public function init()
    {
        parent::init();
        if (!isset($this->height)) {
            $this->height = '200px';
        }
        if (isset($this->update)) {
            foreach($this->update as $items) {
                foreach($items as $item) {
                    $this->values[] = $item;
                }
            }
        }

    }
    public function run()
    {
        echo '<div>';
        echo "<ul class = list-group>";
        echo  Html::checkbox('all', false, [
                'label' => 'Все',
                'class' => 'all'
            ]);



        echo '<div>';
        foreach($this->parent as $parent) {
            $checked = in_array($parent['id'], $this->values);
            if($this->pharmacy){
                echo "<li class='list-group-item pharmacy-item'><ul class = 'list-group'>";
            } else
                echo "<li class='list-group-item'><ul class = 'list-group'>";
            echo Html::checkbox($this->parent_title.'[]', $checked, [
                    'value' => $parent['id'],
                    'label' => $parent[$this->parent_label],
                ]);


            echo "<div style='height: $this->height; overflow: auto'>";

            foreach($this->child as $child) {
                if($child[$this->relation] == $parent['id']) {
                    $checked = in_array($child['id'], $this->values);
                    echo "<li class='list-group-item'>";
                    echo Html::checkbox($this->child_title.'[]',
                            $checked
                            , [
                                'value' => $child['id'],
                                'label' => $child['name'],
                            ]) . '</li>';
                }
            };
            echo '</div></ul></li>';
        }
        echo '</div>';

        echo '</ul>';
        echo '</div>';

    }

}