<?php
use yii\helpers\Html;


$radio_questions = $presentation->devidedQuestions['radio'];
$checkbox_questions = $presentation->devidedQuestions['checkbox'];
$i = 1;
?>
<?php foreach($presentation->questions as  $question):?>
    <?php if(in_array($question, $radio_questions)):?>
        <pagebreak />
        <div class="text-center"><h4>Вопрос: <?=$question->question?></h4></div>
        <div>
            <?=Html::img(Yii::getAlias('@temp/presentation/'.$presentation->id.'/'.$question->id.'_common_legend.png'))?>
        </div>
        <div>
            <?=Html::img(Yii::getAlias('@temp/presentation/'.$presentation->id.'/'.$question->id.'_common.png'))?>
        </div>
        <div class="text-center"><p>Р.<?php echo $i; $i++?></p></div>
        <pagebreak />
        <div>
            <?=Html::img(Yii::getAlias('@temp/presentation/'.$presentation->id.'/'.$question->id.'_region.png'))?>
        </div>
        <div class="text-center"><p>Р.<?php echo $i; $i++?></p></div>
    <?php endif;?>

    <?php if(in_array($question, $checkbox_questions)):?>
        <pagebreak />
        <div class="text-center"><h4>Вопрос: <?=$question->question?></h4></div>
        <div>
            <?=Html::img(Yii::getAlias('@temp/presentation/'.$presentation->id.'/'.$question->id.'_common_legend.png'))?>
        </div>
        <div>
            <?=Html::img(Yii::getAlias('@temp/presentation/'.$presentation->id.'/'.$question->id.'_common.png'))?>
        </div>
        <div class="text-center"><p>Р.<?php echo $i; $i++?></p></div>
        <pagebreak />
        <div>
            <?=Html::img(Yii::getAlias('@temp/presentation/'.$presentation->id.'/'.$question->id.'_region.png'))?>
        </div>
        <div class="text-center"><p>Р.<?php echo $i; $i++?></p></div>
    <?php endif;?>
<?php endforeach;?>



