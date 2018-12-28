<?
session_start();

if ( ($_SESSION['save_proto_memory']) and ($_SESSION['uid']) and  ($_SESSION['save_proto_memory']['isart']==1))
{
$oper_count=0;

	 if ($_GET[new_art_name])
 	{
 	//надо преобразовать и сохранить в сессию
 	/*
 	$conv=iconv("UTF-8", "CP1251", $_GET[new_art_name]);
 	$escaped_item=mysql_real_escape_string($conv);
 	$_SESSION['save_proto_memory'][new_art_name]=htmlspecialchars($escaped_item,ENT_COMPAT | ENT_HTML401,"cp1251");
 	*/
 	$oper_count++;
 	}
	

	
	
	
	
	$mark_st=0;
	$mark_mf=0;
	foreach ($_GET as $key => $value)
	{


	 if ($key=='mark_sila') {  if (($value=='true') and ($mark_st<2) )
	 			 { $mark_st++; $_SESSION['save_proto_memory'][$key]=true; }
	 			 else { $_SESSION['save_proto_memory'][$key]=false;}
			 	$oper_count++;	 			 
	 			}
	else
	 if ($key=='mark_lovk') 
	 			{
	 				if (!(($_SESSION['save_proto_memory']['nclass']==2) OR ($_SESSION['save_proto_memory']['nclass']==3)))
					{
					//критовой шмотке и танковой шмотке не льзя ставить ловку
					
				 			if (($value=='true') and ($mark_st<2) )
				 			 { $mark_st++; $_SESSION['save_proto_memory'][$key]=true; }
				 			 else { $_SESSION['save_proto_memory'][$key]=false;}
					}
					else
						{
						$_SESSION['save_proto_memory'][$key]=false;
						}
				 $oper_count++;					
	 			}
	else
	 if ($key=='mark_inta') 
	 			{
	 			if (!(($_SESSION['save_proto_memory']['nclass']==1) OR ($_SESSION['save_proto_memory']['nclass']==3)))
					{
					//уворотной  шмотке и танковой шмотке не льзя ставить инту
			 			  if (($value=='true') and ($mark_st<2) )
			 			 { $mark_st++; $_SESSION['save_proto_memory'][$key]=true; }
			 			 else { $_SESSION['save_proto_memory'][$key]=false;}
					}
					else
						{
						$_SESSION['save_proto_memory'][$key]=false;
						}
				$oper_count++;						
	 			}
	else
	 if ($key=='mark_intel') {  if (($value=='true') and ($mark_st<2) )
	 			 { $mark_st++; $_SESSION['save_proto_memory'][$key]=true; }
	 			 else { $_SESSION['save_proto_memory'][$key]=false;}
			 	$oper_count++;	 			 
	 			}
	else
	 if ($key=='mark_gmp') {  if (($value=='true') and ($mark_st<2) )
	 			 { $mark_st++; $_SESSION['save_proto_memory'][$key]=true; }
	 			 else { $_SESSION['save_proto_memory'][$key]=false;}
			 	$oper_count++;	 			 
	 			}
	 else
	 if ($key=='mark_krit') 
	 			{ 
				if (!(($_SESSION['save_proto_memory']['nclass']==1) OR ($_SESSION['save_proto_memory']['nclass']==3)))	 			
					{
					//увороту и танку нельзя ставить крут
			 			 if (($value=='true') and ($mark_mf<2) )
			 			 { $mark_mf++; $_SESSION['save_proto_memory'][$key]=true; }
			 			 else { $_SESSION['save_proto_memory'][$key]=false;}
					}
					else
					{
					$_SESSION['save_proto_memory'][$key]=false;
					}
			 	$oper_count++;						
	 			}
	 else
	 if ($key=='mark_akrit') {  if (($value=='true') and ($mark_mf<2) )
	 			 { $mark_mf++; $_SESSION['save_proto_memory'][$key]=true; }
	 			 else { $_SESSION['save_proto_memory'][$key]=false;}
			 	$oper_count++;	 			 
	 			}	 			
	 else
	 if ($key=='mark_uvorot') { 
				if (!(($_SESSION['save_proto_memory']['nclass']==2) OR ($_SESSION['save_proto_memory']['nclass']==3)))	 				 			 
	 			 {
				//криту и танку нельзя ставить уворот	 			 
	 				 if (($value=='true') and ($mark_mf<2) )	 			 
					{
		 			  $mark_mf++; $_SESSION['save_proto_memory'][$key]=true; }
		 			 else { $_SESSION['save_proto_memory'][$key]=false;}
					
	 			}
				 else { 
				 	$_SESSION['save_proto_memory'][$key]=false;
				 }
				$oper_count++;	 			 	 			
				}
	 else
	 if ($key=='mark_auvorot') {  if (($value=='true') and ($mark_mf<2) )
	 			 { $mark_mf++; $_SESSION['save_proto_memory'][$key]=true; }
	 			 else { $_SESSION['save_proto_memory'][$key]=false;}
			 	$oper_count++;	 			 
	 			}
	}

 if ($oper_count==10) {echo "true";} else {echo "false";}

} else { echo "false";}
?>