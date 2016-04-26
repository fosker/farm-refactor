<?php
/**
 * @var $vendor array
*/
use yii\helpers\Html;
use yii\helpers\Url;

?>

<h2>Список промо-кодов за <? date("j.n.Y") ?></h2>

<? foreach($vendor as $promo) : ?>
    <p><?=Html::a($promo['code'],Url::to(['use-promo', 'promo'=>$promo['code'], 'token'=>$promo['token']],true));?> (<?=Url::to(['use-promo', 'promo'=>$promo['code'], 'token'=>$promo['token']],true);?>)</p>
<? endforeach; ?>