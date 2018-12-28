<?
//прозрачный пиксель для партнера
	$value=617;
	setcookie ("part", $value,time()+604800, "/");/* период действия -7 дней - корневой путь*/
	


	Header("Content-type: image/gif");

    $im = imagecreate(1,1);
    $black = imagecolorallocate($im, 0, 0, 0);
    imagecolortransparent($im, $black);    
    ImageGif($im);
    ImageDestroy($im);
    
?>