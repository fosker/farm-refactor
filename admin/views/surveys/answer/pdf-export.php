<?php
/**
 * Created by PhpStorm.
 * User: Андрей
 * Date: 22.09.2015
 * Time: 14:11
 */

/* @var $answers array */
?>
<h1>Результаты по анкете "<?=$answers[0]->view->survey->title;?>"</h1>

    <?php $author = null;
    foreach($answers as $answer) :
        if($author != $answer->view->user->name) : ?>
            <h3><?=$answer->view->user->name;?></h3>
            <p>Регион/город: <?=$answer->view->user->pharmacy->city->region->name. '/'.
                $answer->view->user->pharmacy->city->name?></p>
            <p>Дата/время: <?=$answer->view->added?></p>
            <p>Образование: <?=$answer->view->user->education->name?></p>
        <?php endif; ?>
        <p><b><?=$answer->question->question;?></b> <?=$answer->value;?></p>
    <?php
        $author = $answer->view->user->name;
    endforeach; ?>

