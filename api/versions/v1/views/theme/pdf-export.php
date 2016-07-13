<?php
?>
<h1>Пользователь</h1>
<h3>Имя Фамилия </h3><p><?=$user->name?></p>
<h3>Компания </h3><p><?=$user->pharmacist->company->title?></p>
<h3>Аптека </h3><p><?=$user->pharmacist->pharmacy->name. ' (' . $user->pharmacist->pharmacy->address . ') '?></p>
<h3>Регион </h3><p><?=$user->pharmacist->pharmacy->city->region->name?></p>
<h3>Город </h3><p><?=$user->pharmacist->pharmacy->city->name?></p>
<h3>Телефон </h3><p><?=$user->phone?></p>
<h3>Email </h3><p><?=$user->email?></p>

