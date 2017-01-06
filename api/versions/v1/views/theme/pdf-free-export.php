<?php
?>
<h2><b>Пользователь</b></h2>
<h4><u>Имя Фамилия </u></h4><p><?=$user->name?></p>
<h4><u>Организация </u></h4><p><?=$user->pharmacist->company->title?></p>
<h4><u>Аптека </u></h4><p><?=$user->pharmacist->pharmacy->name. ' (' . $user->pharmacist->pharmacy->address . ') '?></p>
<h4><u>Регион </u></h4><p><?=$user->pharmacist->pharmacy->city->region->name?></p>
<h4><u>Город </u></h4><p><?=$user->pharmacist->pharmacy->city->name?></p>
<?php if($reply->phone) :?>
    <h4><u>Телефон </u></h4><p><?=$reply->phone?></p>
<?php endif;?>
<?php if($reply->email) :?>
    <h4><u>Email </u></h4><p><?=$reply->email?></p>
<?php endif;?>
<br>
<h2><b>Ответ</b></h2>
<h4><u>Сообщение </u></h4><p><?=$reply->text?></p>
<?php if($reply->imagePath) :?>
    <h4><u>Фото </u></h4><img src="<?=$reply->imagePath?>" alt="Фото" style="width:90%">
<?php endif;?>


