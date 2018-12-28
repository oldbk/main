<?
if (!($_SESSION['uid'] >0)) header("Location: index.php");
		//это входящие данные с магии, пример серьгами
		$diff=1;
		$imf_count=8;
		$img_name='boots_';
		$otdel=array(2);
		$txt='иллюзию сапог';
		/*
				4=>'clip_ny_'.$s,
				41=>'scarf_ny_'.$s,
				1=>'armor_ny_'.$s,
				22=>'overclothes_ny_'.$s,
				42=>'ring_ny_'.$s,
				24=>'cap_ny_'.$s,
				21=>'gloves_ny_'.$s,
				3=>'shield_ny_'.$s,
				2=>'boots_ny_'.$s
		*/
//конец вводных
include "open_gift_gallery_single_wear.php";

?>
