<?php
?>
<h2><b>Пользователь</b></h2>
<h4><u>Имя Фамилия </u></h4><p><?=$user->name?></p>
<h4><u>Регион </u></h4><p><?=$user->pharmacist->pharmacy->city->region->name?></p>
<h4><u>Город </u></h4><p><?=$user->pharmacist->pharmacy->city->name?></p>
<h4><u>Компания </u></h4><p><?=$user->pharmacist->company->title?></p>
<h4><u>Аптека </u></h4><p><?=$user->pharmacist->pharmacy->name. ' (' . $user->pharmacist->pharmacy->address . ') '?></p>





