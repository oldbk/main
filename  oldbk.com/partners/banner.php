<?php
include "/www/capitalcity.oldbk.com/connect.php";

switch($_REQUEST['b']) {
	case "1": $path="partners/468x60_01.swf"; break;
	case "2": $path="partners/468x60_02.swf"; break;
	case "3": $path="partners/468x60_03.swf"; break;
	case "4": $path="partners/468x60_04_off.swf"; break;
	case "5": $path="partners/468x60_05.swf"; break;
	case "6": $path="partners/728x90_01_off.swf"; break;
	case "7": $path="partners/728x90_02_off.swf"; break;
	case "8": $path="partners/728x90_03.swf"; break;
	case "9": $path="partners/728x90_04.swf"; break;
	case "10": $path="partners/728x90_05_off.swf"; break;
	case "11": $path="partners/120_300_2.swf"; break;
	case "12": $path="partners/120_300_3.swf"; break;
	case "13": $path="baner_f-1.png"; break;
	case "14": $path="baner_f-2.png"; break;

	case "15": $path="bp/oldbk_240_400_03.swf"; break;
	case "16": $path="bp/oldbk_120_240_01.gif"; break;
	case "17": $path="bp/oldbk_240_400_01.jpg"; break;
	case "18": $path="bp/oldbk_240_400_01.swf"; break;
	case "19": $path="bp/oldbk_240_400_02.gif"; break;
	case "20": $path="bp/oldbk_240_400_03.gif"; break;
	case "21": $path="bp/oldbk_240_400_04.swf"; break;	
	case "22": $path="bp/oldbk_240_400_05.gif"; break;
	case "23": $path="bp/oldbk_728_90_02.gif"; break;	
	case "24": $path="bp/oldbk_728_90_02.swf"; break;
	case "25": $path="bp/oldbk_120_240_01.swf"; break;
	case "26": $path="bp/oldbk_120_240_02.gif"; break;			
	case "27": $path="bp/oldbk_240_400_02.swf"; break;
	case "28": $path="bp/oldbk_240_400_04.gif"; break;		
	case "29": $path="bp/oldbk_728_90_03.gif"; break;		
	case "30": $path="bp/oldbk_468_60_01.gif"; break;		
	case "31": $path="bp/oldbk_468_60_01.swf"; break;					
	case "32": $path="bp/oldbk_468_60_02.gif"; break;					
	case "33": $path="bp/oldbk_468_60_03.gif"; break;					
	
	case "34": $path="bp/300x250_04.swf"; break;
	
	
	case "35": $path="bp/600x90.swf"; break;	
	case "36": $path="bp/728x90.swf"; break;	
	case "37": $path="bp/728x90_n2.swf"; break;	
	case "38": $path="bp/warrior_200x300.swf"; break;
	case "39": $path="bp/warrior_240x400.swf"; break;	
		
	case "40": $path="bp/oldbk_468x60_fp8_fps60.swf"; break;	
	case "41": $path="bp/oldbk_728x90_fp8_fps60_2.swf"; break;
	case "42": $path="bp/oldbk_240x400_fp8_fps60_2.swf"; break;	

	case "43": $path="bp/240x400.swf"; break;	

	
	}
if (isset($_REQUEST['b']) and isset($_REQUEST['pid']))
	{
	 mysql_query("UPDATE `partners` SET show_b".$_REQUEST['b']."=show_b".$_REQUEST['b']."+1 WHERE `id`='".$_REQUEST['pid']."';");
	}

@readfile("https://i.oldbk.com/i/".$path);
?>
