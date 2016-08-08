<?php
use yii\helpers\Html;
?>
<p>Зарегистрировался новый представитель:</p>
<p>Имя: <?=$user->name;?></p>
<p>Логин: <?=$user->login;?></p>
<p>Email: <?=$user->email;?></p>
<p>Ссылка:  <?=Html::a($user->login, 'http://admin.pharmbonus.by/index.php?r=user/view&id=' . $user->id);?></p>
