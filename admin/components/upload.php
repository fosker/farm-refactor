<?php

namespace backend\components;

$path = '../../uploads/';
$filename = $path.basename(generateRandomString() . $_FILES['file']['name']);

if (copy($_FILES['file']['tmp_name'], $filename))
{
    print json_encode(['data' => ['link' => 'http://farm.loc/uploads/'.$filename]]);
}

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}







