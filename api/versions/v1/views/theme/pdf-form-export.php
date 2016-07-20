<?php
use common\models\forms\Field;
?>
<h2><b>Пользователь</b></h2>
<h4><u>Имя Фамилия </u></h4><p><?=$user->name?></p>
<h4><u>Компания </u></h4><p><?=$user->pharmacist->company->title?></p>
<h4><u>Аптека </u></h4><p><?=$user->pharmacist->pharmacy->name. ' (' . $user->pharmacist->pharmacy->address . ') '?></p>
<h4><u>Регион </u></h4><p><?=$user->pharmacist->pharmacy->city->region->name?></p>
<h4><u>Город </u></h4><p><?=$user->pharmacist->pharmacy->city->name?></p>
<h4><u>Телефон </u></h4><p><?=$reply->phone?></p>
<h4><u>Email </u></h4><p><?=$reply->email?></p>
<br>
<h2><b>Форма</b></h2>
<?php foreach($form as $field) :?>
<?php if($field->value):?>
<h4><u><?=Field::findOne($field->field_id)->label.' '?></u><h4><p><?=$field->value?></p>
        <?php endif;?>
<?php endforeach;?>


