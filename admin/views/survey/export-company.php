<?php
use yii\helpers\Html;


$radio_questions = $survey->devidedQuestions['radio'];
$checkbox_questions = $survey->devidedQuestions['checkbox'];
?>
<?php foreach($survey->questions as $i => $question):?>
    <?php if(in_array($question, $radio_questions)):?>
        <div class="text-center"><h4>Вопрос: <?=$question->question?></h4></div>
        <div>
            <?=Html::img(Yii::getAlias('@temp/'.$question->id.'_common_legend.png'))?>
        </div>
        <div>
            <?=Html::img(Yii::getAlias('@temp/'.$question->id.'_common.png'))?>
        </div>
        </br>
        <div>
            <?=Html::img(Yii::getAlias('@temp/'.$question->id.'_company.png'))?>
        </div>
        <pagebreak />
    <?php endif;?>

    <?php if(in_array($question, $checkbox_questions)):?>
        <div class="text-center"><h4>Вопрос: <?=$question->question?></h4></div>
        <div>
            <?=Html::img(Yii::getAlias('@temp/'.$question->id.'_common_legend.png'))?>
        </div>
        <div>
            <?=Html::img(Yii::getAlias('@temp/'.$question->id.'_common.png'))?>
        </div>
        <div>
            <?=Html::img(Yii::getAlias('@temp/'.$question->id.'_company.png'))?>
        </div>
        <pagebreak />
    <?php endif;?>
<?php endforeach;?>



