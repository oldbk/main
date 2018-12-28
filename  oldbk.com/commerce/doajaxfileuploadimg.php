<?php
session_start();

function is_ani ( $filename ) {
if(!( $fh = @ fopen ( $filename , 'rb' )))
return false ;
$count = 0 ;
//an animated gif contains multiple "frames", with each frame having a
//header made up of:
// * a static 4-byte sequence (\x00\x21\xF9\x04)
// * 4 variable bytes
// * a static 2-byte sequence (\x00\x2C) (some variants may use \x00\x21 ?)

// We read through the file til we reach the end of the file, or we've found
// at least 2 frame headers
while(! feof ( $fh ) && $count < 2 ) {
$chunk = fread ( $fh , 1024 * 100 ); //read 100kb at a time
$count += preg_match_all ( '#\x00\x21\xF9\x04.{4}\x00(\x2C|\x21)#s' , $chunk , $matches );
}

fclose ( $fh );
return $count > 1 ;
} 

if ( ($_SESSION[select_menu]>0) and ($_SESSION['uid']) )
{

	$error = "";
	$msg = "";
	
	 if (($_GET[f]!='') and (($_SESSION[select_menu]>=17) AND  ($_SESSION[select_menu]<=20))  )
	 	{
	 	include ('price.php');
		$fid=(int)($_GET[f]);
		$pp=(int)($_GET[masparam]);
			
			if  (in_array($pp, $PRICE[$_SESSION[select_menu]][param]) )
					{
					$fileElementName = 'fileToUpload'.$fid;	
					$sel_array=$PRICE[$pp];
					$multi=1;
					}
					else
					{
					die();
					}
		}
	elseif (($_GET[f]!='') and (($_SESSION[select_menu]==81) OR  ($_SESSION[select_menu]==82)))
	 	{
	 	include ('price.php');
		$fid=(int)($_GET[f]);
		$pp=(int)($_GET[masparam]);
			
			if  (in_array($pp, $PRICE[$_SESSION[select_menu]][param]) )
					{
					$fileElementName = 'fileToUpload'.$fid;	
					$sel_array=$PRICE[$pp];
					$multi=1;
					}
					else
					{
					die();
					}
		}
		else
		{
		$fileElementName = 'fileToUpload';
		$sel_array=$_SESSION[select_array];
		}
	
	if(!empty($_FILES[$fileElementName]['error']))
	{
		switch($_FILES[$fileElementName]['error'])
		{

			case '1':
				$error = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
				break;
			case '2':
				$error = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
				break;
			case '3':
				$error = 'The uploaded file was only partially uploaded';
				break;
			case '4':
				$error = 'No file was uploaded.';
				break;

			case '6':
				$error = 'Missing a temporary folder';
				break;
			case '7':
				$error = 'Failed to write file to disk';
				break;
			case '8':
				$error = 'File upload stopped by extension';
				break;
			case '999':
			default:
				$error = 'No error code avaiable';
		}
	}elseif(empty($_FILES[$fileElementName]['tmp_name']) || $_FILES[$fileElementName]['tmp_name'] == 'none')
	{
		$error = 'No file was uploaded:'.$_GET[masparam].'/'.$fileElementName;
	}else 
	{
	$valid_types = array("gif");
	#определяем расширение файла
        $ext = substr($_FILES[$fileElementName]['name'], 1 + strrpos($_FILES[$fileElementName]['name'], "."));
	$type_upload = GetImageSize($_FILES[$fileElementName]["tmp_name"]);
	//Индекс 0 содержит ширину/width изображения в пикселах. Индекс 1 содержит высоту/height. 
	//Индекс 2 это флаг, указывающий тип изображения.1 = GIF, 
		if($sel_array[size]==0)
		   {
		    $error ="Ошибка параметров, возможно Вы не выбрали изображение для замены!";
		   }
		else	
		if(($_FILES[$fileElementName]["size"] > (1024*$sel_array[size])) and ((!($sel_array[size_prise]>0)) OR (is_array($PRICE[$_SESSION[select_menu]][param]))  )  )
		   {
		    $error ="Размер изображения ".$_FILES[$fileElementName]["size"]." байт, что превышает допустимый лимит в ".$sel_array[size]."000 байт.  Попробуйте загрузить другое изображение.";
		   }
	       elseif ((!in_array($ext, $valid_types)) OR ($type_upload[2]!=1))
		   {
		         $error = "Можно загружать только: .gif-файлы.";
		   }
		elseif ( ($type_upload[0]!=$sel_array[w]) and ($_SESSION[select_menu]!=90) and ($_SESSION[select_menu]!=91) and ($_SESSION[select_menu]!=500) and ($_SESSION[select_menu]!=501) and ($_SESSION[select_menu]!=502) and ($_SESSION[select_menu]!=503))
		  {
		  $error = "Для данного типа предмета ширина изображения должна быть ".$sel_array[w]." px";
		  }   
		elseif (($type_upload[1]!=$sel_array[h]) and ($_SESSION[select_menu]!=90) and ($_SESSION[select_menu]!=91) and ($_SESSION[select_menu]!=500) and ($_SESSION[select_menu]!=501) and ($_SESSION[select_menu]!=502) and ($_SESSION[select_menu]!=503))
		  {
		  $error = "Для данного типа предмета высота изображения должна быть ".$sel_array[h]." px";
		  } 
		 elseif ( ( ($type_upload[0]<$sel_array[w]) OR ($type_upload[0]>$sel_array[wmax]) )   and (($_SESSION[select_menu]==90) OR ($_SESSION[select_menu]==91) OR ($_SESSION[select_menu]==500) OR ($_SESSION[select_menu]==501) OR ($_SESSION[select_menu]==502) OR ($_SESSION[select_menu]==503)))
		  {
		  $error = "Ширина изображения должна быть от ".$sel_array[w]." px  до ".$sel_array[wmax]." px ";
		  }  
		elseif ( ( ($type_upload[1]<$sel_array[h]) OR ($type_upload[1]>$sel_array[hmax]) ) and (($_SESSION[select_menu]==90) OR ($_SESSION[select_menu]==91) OR ($_SESSION[select_menu]==500) OR ($_SESSION[select_menu]==501) OR ($_SESSION[select_menu]==502) OR ($_SESSION[select_menu]==503)))
		  {
		  $error = "Высота изображения должна быть от  ".$sel_array[h]." px до ".$sel_array[hmax]." px ";
		  }   
		elseif ( (is_ani($_FILES[$fileElementName]["tmp_name"]) >= 1) and  ($sel_array[anim]==false) )
		  {
		  $error = "Вы можете загрузить только статичное изображение!";
		  } 
		elseif ( (is_ani($_FILES[$fileElementName]["tmp_name"]) < 1) and  ($sel_array[anim]==true) )
		  {
		  $error = "Отсутствует анимация! Выберите другое изображение!";
		  } 

	   // Проверяем загружен ли файл		   
	       elseif(is_uploaded_file($_FILES[$fileElementName]["tmp_name"]))
		   {
		     // Если файл загружен успешно, перемещаем его
		     // из временной директории в конечную
		     //+ даем новое имя
		     if ($multi==1)
		     {
		     $new_file_name="img_".$_SESSION['uid']."_t".$pp."_d".time().".gif";
		     }
		     else
		     {
		     $new_file_name="img_".$_SESSION['uid']."_t".$_SESSION[select_menu]."_d".time().".gif";
		     }
		     move_uploaded_file($_FILES[$fileElementName]["tmp_name"], "/www/oldbk.com/commerce/uploadimg/".$new_file_name);
		   } 
		else 
		   {
		      $error = "Ошибка загрузки файла";
		   }
			
			$msg .= "<img src='uploadimg/".$new_file_name."'>";
			
			 if ($multi==1)
		     	{
		     	 if ($pp==31) { $pp=($fid*100)+$pp; } //ring fix
		     	 if ($pp==46) { $pp=($fid*100)+$pp; } //ring fix		     	 
		     	 if ($pp==61) { $pp=($fid*100)+$pp; } //ring fix		     	 
		     	 if ($pp==76) { $pp=($fid*100)+$pp; } //ring fix		     	 
		     	 
		     	 if ( ($_SESSION[select_menu]==81) or ($_SESSION[select_menu]==82))
		     	 	{
		     	 	$pp=($fid*100)+$pp; // fix multi clan shadows
		     	 	}  
		     	 
			$_SESSION['new_serv_img'][$pp]=$new_file_name;
			$_SESSION['new_serv_img_size']=$_FILES[$fileElementName]["size"];
		     	}
		     	else
		     	{
			$_SESSION['new_serv_img']=$new_file_name;		     	
			$_SESSION['new_serv_img_size']=$_FILES[$fileElementName]["size"];
		     	}
			//for security reason, we force to remove all uploaded file
			@unlink($_FILES[$fileElementName]);		
			
			
			
			
	}		
}	
else
{
$error = "Ошибка загрузки файла";
}
	echo "{";
	echo				"error: '" . $error . "',\n";
	echo				"msg: '" . $msg . "'\n";
	echo "}";

?>