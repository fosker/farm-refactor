<?php

$path = 'content-img/'.uniqid();
$array = explode('.', $_FILES['file']['name']);
$extension = end($array);
$filename = $path.'.'.$extension;

if (copy($_FILES['file']['tmp_name'], $filename))
{
    print json_encode(['data' => ['link' => 'http://admin.pharmbonus.by/'.$filename]]);
}