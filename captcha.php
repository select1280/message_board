<?php
session_start();
$code = '';
for ($i = 0; $i < 4; $i++) {
  $code .= chr(rand(65, 90)); // 隨機大寫英文字母
}
$_SESSION['captcha'] = $code;

header('Content-Type: image/png');
$image = imagecreate(80, 30);
$bg = imagecolorallocate($image, 240, 240, 240);
$text = imagecolorallocate($image, 50, 50, 50);
imagestring($image, 5, 15, 7, $code, $text);
imagepng($image);
imagedestroy($image);
