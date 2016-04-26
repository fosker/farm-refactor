<?php

namespace backend\components;

use yii\widgets\InputWidget;
use yii\helpers\Html;


class CheckWidget extends InputWidget
{
    public $parent = [];
    public $child = [];

    public $parent_title;
    public $child_title;

    public $relation;

    public $update = [];

    private $values = [];

    public $height;

    public $firms;

    public $color;

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
        echo "<ul class = 'list-group'>";
        if($this->firms) {
            echo  Html::checkbox('all_producers', false, [
                'label' => 'Все производители',
                'class' => 'all_producers'
            ]);
            echo '<br/>';
            echo  Html::checkbox('all_companies', false, [
                'label' => 'Все компании',
                'class' => 'all_companies'
            ]);
        } else {
            echo  Html::checkbox('all', false, [
                'label' => 'Все',
                'class' => 'all'
            ]);
        }



        echo '<div>';
        foreach($this->parent as $parent) {
            $checked = in_array($parent['id'], $this->values);
            echo "<li class='list-group-item'><ul class = 'list-group";
            if($this->firms && $parent['producer'])
                echo " producer' style='color: $this->color'>";
            elseif(!$this->firms)
                echo " '>";
            else
                echo " not-producer'>";

            echo Html::checkbox($this->parent_title.'[]', $checked, [
                    'value' => $parent['id'],
                    'label' => $parent['name'],
                ]);


            echo "<div style='height: $this->height; overflow: auto'>";

            foreach($this->child as $child) {
                if($child[$this->relation] == $parent['id']) {
                    $checked = in_array($child['id'], $this->values);
                    echo "<li class='list-group-item";
                    if($this->firms && $parent['producer'])
                        echo " producer' style='color: $this->color'>";
                    elseif(!$this->firms)
                        echo " '>";
                    else
                        echo " not-producer'>";
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