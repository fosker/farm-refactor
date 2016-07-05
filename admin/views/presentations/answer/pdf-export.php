<?php
/**
 * Created by PhpStorm.
 * User: Андрей
 * Date: 22.09.2015
 * Time: 14:11
 */

/* @var $answers array */
?>
<h1>Результаты по презентации "<?=$answers[0]->view->presentation->title;?>"</h1>

    <?php $author = null;
    foreach($answers as $answer) :
        if($author != $answer->view->user->name) : ?>
            <h3><?=$answer->view->user->name;?></h3>
            <p>Регион/Город: <?=$answer->view->user->pharmacist->pharmacy->city->region->name. '/'.
                $answer->view->user->pharmacist->pharmacy->city->name?></p>
            <p>Компания/Аптека: <?=$answer->view->user->pharmacist->pharmacy->company->title. '/'.
                $answer->view->user->pharmacist->pharmacy->name .
                ' (' . $answer->view->user->pharmacist->pharmacy->address . ')'?></p>
            <p>Дата: <?=substr($answer->view->added,0,10)?></p>
            <p>Образование: <?=$answer->view->user->pharmacist->education->name?></p>
        <?php endif; ?>
        <p><b><?=$answer->question->question;?></b> <?=$answer->value;?></p>
    <?php
        $author = $answer->view->user->name;
    endforeach; ?>

