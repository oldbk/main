<?PHP
header("Expires: Thu, 01 Jan 1970 00:00:01 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Настройки

   $SECURITY_IMAGE_TYPE = 'GIF';     // Возможные форматы: GIF, JPEG, PNG
   $SECURITY_WIDTH = 60;            // Ширина изображения
   $SECURITY_HEIGHT = 20;            // Высота изображения
   $SECURITY_NUM_GENSIGN = 3;        // Количество символов, которые нужно набрать
   if(isset($_GET['battle']))
   {
		$SECURITY_WIDTH = 60;            // Ширина изображения
		$SECURITY_HEIGHT = 20;            // Высота изображения
		$SECURITY_NUM_GENSIGN = 3;        // Количество символов, которые нужно набрать
	   	$LETTERS = array('0','2','3','4','5','6','8','9');
   }
   else
   {
      $LETTERS = array('0','1','2','3','4','5','6','7','8','9');
   }
   $path_fonts = '/www/fonts/';         // Путь к шрифтам

  // $EXT = strtoupper($_GET['ext']);
  // if($EXT == 'GIF' || $EXT == 'JPEG' || $EXT == 'PNG') $SECURITY_IMAGE_TYPE = $EXT;

 //  if(is_numeric($_GET['width']) && $_GET['width']>100 && $_GET['width']<500) $SECURITY_WIDTH = $_GET['width'];
 //  if(is_numeric($_GET['height']) && $_GET['height']>100 && $_GET['height']<500) $SECURITY_HEIGHT = $_GET['height'];
 //  if(is_numeric($_GET['qty']) && $_GET['qty']>2 && $_GET['qty']<10) $SECURITY_NUM_GENSIGN = $_GET['qty'];

// Ядро


	session_start();

	if (isset($_SESSION['securityCode']) && isset($_SESSION['securityCodedata'])) {
		header ("Content-type: image/gif");
		echo $_SESSION['securityCodedata'];
		die();
	}

   $SECURITY_FONT_SIZE = intval($SECURITY_HEIGHT/(($SECURITY_HEIGHT/$SECURITY_WIDTH)*7));
   $SECURITY_NUM_SIGN = intval(($SECURITY_WIDTH*$SECURITY_HEIGHT)/150);
   $CODE = array();

   $FIGURES = array('50','70','90','110','130','150','170','190','210');

// Создаем полотно

   $src = imagecreatetruecolor($SECURITY_WIDTH,$SECURITY_HEIGHT);

// Заливаем фон

   $fon = imagecolorallocate($src,255,255,255);
   imagefill($src,0,0,$fon);

// Загрузка шрифтов

   $FONTS = array();
   $dir = opendir($path_fonts);

   while($fontName = readdir($dir))
   {
       if($fontName != "." && $fontName != "..")
       {
           if(strtolower(strrchr($fontName,'.'))=='.ttf') $FONTS[] = $path_fonts.$fontName;
       }
   }
   closedir($dir);

// Если есть шрифты

   if(sizeof($FONTS)>0)
   {

    // Рисуем линии на заднем фоне

       //for($i = 0; $i<($SECURITY_HEIGHT)/(($SECURITY_HEIGHT)/4); $i++)
	for($i = 0; $i<0; $i++)
       {

        // Ориентир

           $h = 1;
           $x1 = 0;
           $y1 = ($h == rand(8,10)) ? (($SECURITY_HEIGHT*1.3*3)/5) + rand(0,$SECURITY_HEIGHT*0.5) : (($SECURITY_HEIGHT*1.3*3)/5) - rand(0,$SECURITY_HEIGHT*0.5);
           $x2 = $SECURITY_WIDTH;
           $y2 = ($h == rand(8,10)) ? (($SECURITY_HEIGHT*1.3*3)/5) + rand(0,$SECURITY_HEIGHT*0.5) : (($SECURITY_HEIGHT*1.3*3)/5) - rand(0,$SECURITY_HEIGHT*0.5);
           $color = imagecolorallocatealpha($src,$FIGURES[rand(0,sizeof($FIGURES)-1)],$FIGURES[rand(0,sizeof($FIGURES)-1)],$FIGURES[rand(0,sizeof($FIGURES)-1)],rand(10,30));
           ImageLine($src, $x1, $y1, $x2, $y2, $color);
       }
       unset($x,$y);
       unset($x1,$y2);

    // Если папка шрифтов не пуста то, заливаем основными буквами

       for($i = 0; $i<$SECURITY_NUM_GENSIGN; $i++)
       {

        // Ориентир

           $h = 1;

        // Рисуем

           $color = imagecolorallocatealpha($src,$FIGURES[rand(0,sizeof($FIGURES)-1)],$FIGURES[rand(0,sizeof($FIGURES)-1)],$FIGURES[rand(0,sizeof($FIGURES)-1)],rand(10,30));
           $font = $FONTS[rand(0,sizeof($FONTS)-1)];
           $letter = $LETTERS[rand(0,sizeof($LETTERS)-1)];
           $size = rand($SECURITY_FONT_SIZE*1.8-0.5,$SECURITY_FONT_SIZE*1.8+0.5);
       $x = (empty($x)) ? $SECURITY_WIDTH*0.16 : $x + ($SECURITY_WIDTH*0.7)/$SECURITY_NUM_GENSIGN+rand(0,$SECURITY_WIDTH*0.01);
           $y = ($h == rand(8,10)) ? (($SECURITY_HEIGHT*1.3*3)/5) + rand(0,$SECURITY_HEIGHT*0.02) : (($SECURITY_HEIGHT*1.3*3)/5) - rand(0,$SECURITY_HEIGHT*0.02);
           $angle = rand(20,50);

        // Запоминаем

           $CODE[] = $letter;
           if($h == rand(0,10)) $letter = strtoupper($letter);
           if($h == rand(1,2)) $angle = rand(340,355);

        // Пишем

           imagettftext($src,$size,$angle,$x,$y,$color,$font,$letter);
       }

    // Если нет шрифтов

   }
   else
   {

    // Если папка шрифтов пуста

       for($i = 0; $i<$SECURITY_NUM_GENSIGN; $i++)
       {

        // Ориентир

           $h = 1;

        // Рисуем

           $color = imagecolorallocatealpha($src,$FIGURES[rand(0,sizeof($FIGURES)-1)],$FIGURES[rand(0,sizeof($FIGURES)-1)],$FIGURES[rand(0,sizeof($FIGURES)-1)],rand(10,30));
           $letter = $LETTERS[rand(0,sizeof($LETTERS)-1)];
           $x = (empty($x)) ? $SECURITY_WIDTH*0.1 : $x + ($SECURITY_WIDTH*0.8)/$SECURITY_NUM_GENSIGN+rand(0,$SECURITY_WIDTH*0.01);
           $y = ($h == rand(1,2)) ? (($SECURITY_HEIGHT*1)/4) + rand(0,$SECURITY_HEIGHT*0.1) : (($SECURITY_HEIGHT*1)/4) - rand(0,$SECURITY_HEIGHT*0.1);

        // Запоминаем

           $CODE[] = $letter;
           if($h == rand(0,10)) $letter = strtoupper($letter);

        // Пишем

           imagestring($src,9,$x,$y,$letter,$color);
       }
   }

// Получаем код

   $_SESSION['securityCode'] = implode('',$CODE);

// Печать


function mv ($im,$w,$h) {
  $width = $w; $height = $h;
  $im2 = @imagecreate ($width, $height)
    or die ("Cannot initialize new GD image stream!"); // создаём новую подложку

	$x = 0; $i = 0;
  while ($x<$width) { // идем по X-су и копируем кусочки
    $i=$i+0.2; // шаг для Sin-уса
    $y = ceil(sin($i)*2); // смещение по Y-ку
    imagecopy ($im2, $im, $x, $y, $x, 0, 1, $height); // копирование кусочка
    $x++;
  }
  return $im2; // возвращаем новый рисунок
}

	$src = mv($src,$SECURITY_WIDTH,$SECURITY_HEIGHT);

   if($SECURITY_IMAGE_TYPE == 'PNG')
   {
       header ("Content-type: image/png");
       imagepng($src);
   }
   elseif($SECURITY_IMAGE_TYPE == 'JPEG')
   {
       header ("Content-type: image/jpeg");
       imagejpeg($src);
   }
   else
   {
       header("Content-type: image/gif");
       ob_start();
       imagegif($src);
       $_SESSION["securityCodedata"] = ob_get_flush();
   }

   imagedestroy($src);
?>