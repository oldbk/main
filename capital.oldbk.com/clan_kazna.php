<?
//kazna-mod

function clan_kazna_have($clan_id)
{
  $get_k=mysql_fetch_array(mysql_query("SELECT * FROM oldbk.clans_kazna WHERE `clan_id` = '{$clan_id}' ;"));
     
  if ($get_k[0] >0)
   {
    if ($get_k[ban]==1)
    {
    //казна в бане
    echo "<font color=red>Внимание!Казна клана временно заморожена!</font>";
    return false;
    }
     else
     {
     return $get_k;
     }
   }
   else
   {
   return false;
   }
   
}

function make_clan_kazna($clan_id,$pass_kr,$pass_ekr)
 {
 global $user;
 if ($clan_id>0)
 {
 if(mysql_query("INSERT INTO `oldbk`.`clans_kazna` (`clan_id`,`kr_pass`,`ekr_pass`) values ($clan_id,'{$pass_kr}','{$pass_ekr}');")) 
     {
      echo "<font color=red>Вы успешно создали казну клана!</font><br>";
        //$delo_txt="\"".$user['login']."\", создал клановую казну для клана {$user[klan]}.";
        
	$rec['owner']=$user[id];
	$rec['owner_login']=$user[login];
	$rec['owner_balans_do']=$user['money'];
	$rec['owner_balans_posle']=$user['money'];
	$rec['target']=0;
	$rec['target_login']='';
	$rec['type']=406;//создание казны
	$rec['sum_kr']=0;
	$rec['sum_ekr']=0;
	$rec['sum_kom']=0;
	$rec['item_id']='';
	$rec['item_name']='';
	$rec['item_count']=0;
	$rec['item_type']=0;
	$rec['item_cost']=0;
	$rec['item_dur']=0;
	$rec['item_maxdur']=0;
	$rec['item_ups']=0;
	$rec['item_unic']=0;
	$rec['item_incmagic']='';
	$rec['item_incmagic_count']='';
	$rec['item_arsenal']='';
	$rec['add_info']=$user['klan'];
        add_to_new_delo($rec);        

	//txt_to_delo($delo_txt);

      return true;      
     }
     else
     {
      echo "<font color=red>Ошибка создания клан казны</font><br>";
      return false;
     }
  }
  else
  {
   echo "<font color=red>Ошибка создания клан казны</font><br>";
   return false;
  }
 }

function login_to_kazna($clan_id,$type,$pass)
{

if ($type==1)
 {
 //кредовя

$get_k=mysql_fetch_array(mysql_query("SELECT * FROM oldbk.clans_kazna WHERE `clan_id` = '{$clan_id}' and `kr_pass`='{$pass}' ;")); 
 }
 elseif ($type==2)
 {
 //екровая

$get_k=mysql_fetch_array(mysql_query("SELECT * FROM oldbk.clans_kazna WHERE `clan_id` = '{$clan_id}' and `ekr_pass`='{$pass}' ;")); 

 }
 else
 {
   return false;
 }

  if ($get_k[clan_id] >0)
   {
   return $get_k;
   }
   else
   {
   return false;
   }

}

function put_to_kazna($clan_id,$type,$sum,$klan_name='',$wars_users=false,$coment='')
 {
 global $user, $nodelo;
 if($wars_users)
 {
 	$user_s[login]=$wars_users[login];
 	$user_s[id]=$wars_users[id];
 	$user_s[klan]=$wars_users[klan];
 	$user_s[money]=$wars_users[money];
 	
 }
 else
 {
  	$user_s[login]=$user[login];
 	$user_s[id]=$user[id];
 	$user_s[klan]=$user[klan];
 	$user_s[money]=$user[money];
 }
 
 if ($klan_name=='') {$klan_name=$user_s[klan]; }
 if ($sum<=0) return false;
 
  if ($type==1)
	 {
	 //кредовя
		$max_sum=99999;
		$ktyle='`kr`'; $str_type='кр.';
	 }
	 elseif ($type==2)
	 {
	 //екровая
		 $max_sum=99999;
		 $ktyle='`ekr`'; $str_type='екр.';
	 }
	 else
	 {
		return false;
	 }
 
 
	 if ($sum > $max_sum)
	  {
		   echo "<font color=red>За раз Вы можете пополнить казну не более чем на ".$max_sum." ".$str_type." </font><br>";
		   return false;
	  }
	  else
	  {
	
		if (mysql_query("UPDATE oldbk.clans_kazna set {$ktyle}={$ktyle}+{$sum}   WHERE `clan_id` = '{$clan_id}' ;"))
	    	{
		           echo "<font color=red>Казна клана ".$klan_name." успешно пополнена на <b> ".$sum." ".$str_type." </b> </font><br>";
		          
		          if (($user[deal]==1)and($type==2)) { $add_text=' <b>Дилер</b> '; } else { $add_text=''; }
		           
		           if ($coment!="") { $coment="(".$coment.")"; }
		           
		         $delo_txt=$add_text."\"".$user_s['login']."\" пополнил казну клана {$klan_name}, на ".$sum." ".$str_type." ".$coment;
		        
		        
		   if ($nodelo!=true)
		   	{
		         if ($add_text=='') 
		         {
		         	$rec['owner']=$user_s[id];
				$rec['owner_login']=$user_s[login];
				$rec['owner_balans_do']=$user_s['money'];
				$rec['owner_balans_posle']=$user_s['money']-$sum;
				$rec['target']=0;
				$rec['target_login']='Казна '.$klan_name;
				$rec['type']=407;//пополнил казну
				
				if ($type==1)
				{
				$rec['sum_kr']=$sum;
				$rec['sum_ekr']=0;
				}
				else
				if ($type==2)
				{
				$rec['sum_kr']=0;
				$rec['sum_ekr']=$sum;
				}				
				
				$rec['sum_kom']=0;
				$rec['item_id']='';
				$rec['item_name']='';
				$rec['item_count']=0;
				$rec['item_type']=0;
				$rec['item_cost']=0;
				$rec['item_dur']=0;
				$rec['item_maxdur']=0;
				$rec['item_ups']=0;
				$rec['item_unic']=0;
				$rec['item_incmagic']='';
				$rec['item_incmagic_count']='';
				$rec['item_arsenal']='';
				$rec['add_info']=$klan_name;
			        add_to_new_delo($rec); 
		         }
		         }
		         
			 txt_to_kazna_log(1,$type,$clan_id,$delo_txt,$wars_users);           
		 	   return true;
	   	 }
	   	 else
	   	 {
		   	 echo mysql_error();
		   	 return false;
	   	 }
	   }

 
 
 }

function get_from_kazna($clan_id,$type,$sum)
 {
 global $user;
 $max_sum=99999;
 $nalog=0; //5%
 $total_sum=$sum*(1+$nalog);
 
 if (($sum > $max_sum) and ($sum <=0))
  {
   echo "<font color=red>За раз Вы можете забрать из казны, не более чем на ".$max_sum."</font><br>";
   return false;
    }
  else
  {
	 if ($type==1)
	 {
	 //кредовя
	$ktyle='`kr_pass`'; $str_type='кр.'; $rtype='kr';
	 }
	 elseif ($type==2)
	 {
	 //екровая
	 $ktyle='`ekr_pass`'; $str_type='екр.';  $rtype='ekr';
	 }
	 else
	 {
	   return false;
	 }

    
  $in_kaza=clan_kazna_have($clan_id);
  if ( ($in_kaza) and ($in_kaza[$rtype]>=$total_sum))
     {
	if (mysql_query("UPDATE oldbk.clans_kazna set {$ktyle}={$ktyle}-{$total_sum}   WHERE `clan_id` = '{$clan_id}' ;"))
    	{
           echo "<font color=red>Из казны переведено  <b> ".$sum." ".$str_type." </b> </font><br>";
 	   return true;
   	 }
   	 else
   	 {
   	   return false;
   	 }
     }
     else
      {
       echo "<font color=red>В клановой казне не достаточно ".$str_type." для выдачи </font><br>";
   	   return false;      
      }
   	 
   	 
   }

 
 
 }

function ch_pass_kazna($clan_id,$type,$old_pass,$new_pas)
{
global $user;

if ($type==1)
	{
	 //кредовя
	$ktylep='`kr_pass`'; $at='кредитному';
	 }
	 elseif ($type==2)
	 {
	 //екровая
	 $ktylep='`ekr_pass`'; $at='валютному';
	 }
	 else
	 {
	   return false;
	 }

if (mysql_query("UPDATE oldbk.clans_kazna set {$ktylep}='{$new_pas}'  WHERE `clan_id` = '{$clan_id}' and {$ktylep}='{$old_pass}' ;"))
    {
      if(mysql_affected_rows()>0)
        {
        $txt="\"".$user['login']."\" сменил пароль к {$at} счету казны.";
	txt_to_kazna_log(0,$type,$clan_id,$txt);          
        return true;
        }
        else
        {
        return false;
        }
    }
    else
    {
   
    return false;
    }

}

function give_to_skol_from_kazna($clan_id,$soklan,$sum,$type,$pass,$coment)
{
 global $user;
 $max_sum=99999;
 if ($coment!='') { $coment='('.$coment.')'; }
 

    $nalog=0; //0 
    

 $total_sum=$sum+$nalog;
 
 if ($sum > $max_sum) 
  {
  err("<br><br>За раз Вы можете забрать из казны, не более ".$max_sum);
   return false;
    }
  else
  {
	 if ($type==1)
	 {
	 //кредовя
	$ktyle='`kr`'; $str_type='кр.'; $rtype='kr';
	$kuda_sql="UPDATE users set money=money+{$sum}   WHERE `id` = '{$soklan[id]}' ;";
	 }
	 elseif ($type==2)
	 {
	 //екровая
	 $ktyle='`ekr`'; $str_type='екр.';  $rtype='ekr';
	 $kuda_sql="UPDATE oldbk.bank set ekr=ekr+{$sum} where id='{$soklan[ekr_bank_id]}' and owner='{$soklan[id]}' ; ";
	 }
	 else
	 {
	   return false;
	 }
 
 $in_kaza=login_to_kazna($clan_id,$type,$pass);
  if ( ($in_kaza) and ($in_kaza[$rtype]>=$total_sum))
     {

               if ((mysql_query("UPDATE oldbk.clans_kazna set {$ktyle}={$ktyle}-{$total_sum}   WHERE `clan_id` = '{$clan_id}' ;")) and (mysql_query($kuda_sql)))
                     {
                      // записать в дело перса
                       if ($type==1)
                        {
	                        $delo_txt="\"".$soklan['login']."\" получил из клан казны {$user[klan]}, ".$sum." ".$str_type." ".$coment;
		 	        //txt_to_delo($delo_txt,$soklan['id']);
		 	       
		 	        $rec['owner']=$soklan[id];
				$rec['owner_login']=$soklan[login];
				$rec['owner_balans_do']=$soklan['money'];
				$rec['owner_balans_posle']=$soklan['money']+$sum;
				$rec['target']=0;
				$rec['target_login']='Казна '.$soklan['klan'];
				$rec['type']=408;//получил КР из казны
				$rec['sum_kr']=$sum;
				$rec['sum_ekr']=0;
				$rec['sum_kom']=0;
				$rec['item_id']='';
				$rec['item_name']='';
				$rec['item_count']=0;
				$rec['item_type']=0;
				$rec['item_cost']=0;
				$rec['item_dur']=0;
				$rec['item_maxdur']=0;
				$rec['item_ups']=0;
				$rec['item_unic']=0;
				$rec['item_incmagic']='';
				$rec['item_incmagic_count']='';
				$rec['item_arsenal']='';
				$rec['add_info']=$soklan['klan'].'. '.$coment;
			        add_to_new_delo($rec); 
		 	        
		 	        
		 	        
	                        // записать в дело казны
	                        $txt="\"".$user['login']."\" выдал из клан казны {$user[klan]}, ".$sum." ".$str_type."(налог:".$nalog." ".$str_type.") соклановцу \"{$soklan['login']}\" ".$coment;
	                        txt_to_kazna_log(2,$type,$clan_id,$txt);  
	                         if ($coment!='') { $coment='Примечание:'.$coment;}
		        	$message="<font color=red>Внимание!</font> Вам пришел перевод ".$sum." ".$str_type." из клановой казны \"".$user['klan']."\". ".$coment;
				telepost_new($soklan,$message);
	                        return $total_sum;
                        }
                        else
                        if ($type==2)
                        {
	                        $delo_txt="\"".$soklan['login']."\" получил из клан казны {$user[klan]}, ".$sum." ".$str_type." ".$coment;
		 	        //txt_to_delo($delo_txt,$soklan['id']);
		 	        
		 	        $rec['owner']=$soklan[id];
				$rec['owner_login']=$soklan[login];
				$rec['owner_balans_do']=$soklan['money'];
				$rec['owner_balans_posle']=$soklan['money'];
				$rec['target']=0;
				$rec['target_login']='Казна '.$soklan['klan'];
				$rec['type']=409;//получил EКР из казны
				$rec['sum_kr']=0;
				$rec['sum_ekr']=$sum;
				$rec['bank_id']=$soklan[ekr_bank_id];
				$rec['sum_kom']=0;
				$rec['item_id']='';
				$rec['item_name']='';
				$rec['item_count']=0;
				$rec['item_type']=0;
				$rec['item_cost']=0;
				$rec['item_dur']=0;
				$rec['item_maxdur']=0;
				$rec['item_ups']=0;
				$rec['item_unic']=0;
				$rec['item_incmagic']='';
				$rec['item_incmagic_count']='';
				$rec['item_arsenal']='';
				$rec['add_info']=$soklan['klan'].'. '.$coment;
			        add_to_new_delo($rec); 
			        
	   			mysql_query("INSERT INTO `oldbk`.`bankhistory`(`date`, `text` , `bankid`) VALUES ('".time()."','".$delo_txt." <i>(Итого: ".($soklan[ekr_bank_ekr]+$sum)." екр.)</i>','{$soklan[ekr_bank_id]}');");
	                        // записать в дело казны
	                        $txt="\"".$user['login']."\" выдал из клан казны {$user[klan]}, ".$sum." ".$str_type."(налог:".$nalog." ".$str_type.") соклановцу \"{$soklan['login']}\".На счет:".$soklan[ekr_bank_id].". ".$coment;
	                        txt_to_kazna_log(2,$type,$clan_id,$txt);  
	                         if ($coment!='') { $coment='Примечание:'.$coment;}
		        	$message="<font color=red>Внимание!</font> Вам пришел перевод ".$sum." ".$str_type." из клановой казны \"".$user['klan']."\". На счет:".$soklan[ekr_bank_id]." ".$coment;
				telepost_new($soklan,$message);
	                        return $total_sum;
                        }
                        else return false;
                        
                        
                     }
                 
                 
                 
                 
     }
     else if ($in_kaza)
      {
       err("<br><br>В клановой казне не достаточно ".$str_type." для выдачи");
   	   return false;      
      }
       else
       {
       err("<br><br>Неверный пароль доступа!");
   	   return false;      
       }
 
 }
}

function display_kazna($clan_id)
{
 $kazna=clan_kazna_have($clan_id);
  if ($kazna)
   {
   echo "<b>В клановой казне: <font color=#003388>".$kazna[kr]."</font> кр. и <font color=#003388>".$kazna[ekr]."</font> екр.</b><br>";
   }
   else
   {
   return false;
   }

}

function txt_to_delo($txt,$who=0,$wars_users=false)
 {
  global $user;
  if ($who==0) {$who=$user[id];}
  if($wars_users)
  {
  	$who=$wars_users[id];
  }
  if ($txt!='')
  {
      if ( mysql_query("INSERT INTO oldbk.`delo` (`id` , `author` ,`pers`, `text`, `type`, `date`) VALUES  ('','0','".$who."','".$txt."',1,'".time()."');"))
      {
      	return true;
      }
      else
      {
      	return false;
      }
  }
   else
   {
   return false;
   }

 }

function txt_to_kazna_log($met,$type,$clan_id,$txt,$wars_users=false)
 {
 global $user;
 if($wars_users)
 {
 	$user_s[id]=$wars_users[id];
 }
 else
 {
 	$user_s[id]=$user[id];
 }
  if ( mysql_query("INSERT INTO oldbk.`clans_kazna_log` (`method` ,`ktype`, `clan_id`, `owner`, `target`, `kdate`)
   VALUES  ('{$met}','{$type}','{$clan_id}','{$user_s[id]}','".$txt."','".time()."');"))
      {
      return true;
      }
      else
      {
      //echo mysql_error();
      return false;
      }
 }

function сhange_ekr_kazna($clan_id,$ekr,$kurs,$pass)
{
global $user;
$str_type="екр.";
$in_kaza=login_to_kazna($clan_id,2,$pass);
 
 if ( ($in_kaza) and ($in_kaza['ekr']>=$ekr))
      {
        
        if (mysql_query("UPDATE oldbk.clans_kazna set ekr=ekr-{$ekr} , kr=kr+".($ekr*$kurs)."   WHERE `clan_id` = '{$clan_id}' ;"))
          {
           $rez[dekr]=$ekr;
           $rez[dkr]=($ekr*$kurs);    
           // записать в дело казны
            $txt="\"".$user['login']."\" обменял в клан казне {$user[klan]}, ".$ekr." ".$str_type." на ".$rez[dkr]."кр.";
            txt_to_kazna_log(0,2,$clan_id,$txt);  
            return $rez;
          } else return false;
        
      }
     else if ($in_kaza)
      {
       err("<br><br>В клановой казне не достаточно ".$str_type." для обмена");
   	   return false;      
      }
       else
       {
       err("<br><br>Неверный пароль доступа!");
   	   return false;      
       }

}

function by_silver_kazna($clan_id,$soklan,$pass)
{
global $user;
$ekr=15; //стоимость сильвера
$to_bank=5; // бонус в банк
$str_type="екр.";
$in_kaza=login_to_kazna($clan_id,2,$pass);
 if ( ($in_kaza) and ($in_kaza['ekr']>=$ekr))
      {

                    $sql='select * from effects where owner ='.$soklan[id].' AND type =4999 LIMIT 1;';
		    $prod=mysql_fetch_array(mysql_query($sql));
	            if($prod[id]) // ефекта нет, новый сильвер
	            //Edit by Fred - Эфект ЕСТЬ!
	            {
           	    $exp=$prod['time']+60*60*24*30; //плюсуем к мому что есть время
            	    $usql=''; //  Бонус опыта не даем
            	   // echo "Продлеваем бонус<br>";
	            }
	            elseif(!$prod[id]) //ефект есть - добавляем время
	            {
            	    //echo "Устанавливаем бонус<br>";
	            	$prod[id]='NULL';
	            	$exp=(time()+60*60*24*30); // текущее + время
	            	$usql=',`expbonus`=expbonus+0.1'; //ставим бонус
	            }
	            else
	            {
	            	die('ERROR');
	            }

		    if (
				mysql_query("UPDATE `users` set `prem` = 1 ".$usql." WHERE `id` = '{$soklan[id]}' LIMIT 1;") &&
				mysql_query("UPDATE oldbk.clans_kazna set ekr=ekr-{$ekr} WHERE `clan_id` = '{$clan_id}' ;")  &&
			 	mysql_query("UPDATE oldbk.`bank` set `ekr`=ekr+{$to_bank} WHERE `id` = '{$soklan[ekr_bank_id]}' LIMIT 1;")
			)
			{
			mysql_query("insert into effects (`id`,`type`, `name`, `owner`, `time`) VALUES	('".$prod[id]."','4999','Silver account','".$soklan[id]."','".$exp."')	ON DUPLICATE KEY UPDATE time='".$exp."';");
			mysql_query("INSERT INTO oldbk.`delo`(`id` , `author` ,`pers`, `text`, `type`, `date`) VALUES ('','0','{$soklan[id]}','Приобрел/продлил Silver account от соклановца ".$user[login].", из казны клана ".$user[klan].", сроком до ".(date('d-m-Y',$exp))."',9,'".time()."');");
			mysql_query("INSERT INTO `oldbk`.`bankhistory`(`date`, `text` , `bankid`) VALUES ('".time()."','Приобрел/продлил Silver account от соклановца ".$user[login].", из казны клана ".$user[klan].". <i>(Итого: ".($soklan[ekr_bank_ekr]+$to_bank)." екр.)</i>','{$soklan[ekr_bank_id]}');");
			$message="<font color=red>Внимание!</font> Вам присвоен Silver account соклановцем ".$user[login]." и переведено ".$to_bank." екр. на ваш счет №\"".$soklan[ekr_bank_id]."\". ";
			telepost_new($soklan,$message);
			
		            $txt="\"".$user['login']."\", Установил/продлил Silver account соклановцу {$soklan[login]}, сумма:".$ekr." ".$str_type."";
		            txt_to_kazna_log(3,2,$clan_id,$txt);  
		            return $ekr;			
			}
        }
         else if ($in_kaza)
        {
           err("<br><br>В клановой казне не достаточно ".$str_type." для выполнения операции!");
   	   return false;      
        }
       else
       {
           err("<br><br>Неверный пароль доступа!");
   	   return false;      
       }

}


function by_from_kazna($clan_id,$type,$sum,$coment)
 {
 global $user;
 if ($type==1)
	 {
	 //кредовя
		$ktyle='`kr`'; $str_type='кр.'; $rtype='kr';
	 }
	 elseif ($type==2)
	 {
	 //екровая
	 	$ktyle='`ekr`'; $str_type='екр.';  $rtype='ekr';
	 }
	 else
	 {
	   return false;
	 }

    
  $in_kaza=clan_kazna_have($clan_id);
  if ( ($in_kaza) and ($in_kaza[$rtype]>=$sum))
     {
	if (mysql_query("UPDATE oldbk.clans_kazna set {$ktyle}={$ktyle}-{$sum}   WHERE `clan_id` = '{$clan_id}' ;"))
    	{
    	  // записать в дело казны
           $txt="\"".$user['login']."\" заплатил из клан казны {$user[klan]}, ".$sum." ".$str_type." ".$coment;
           txt_to_kazna_log(2,$type,$clan_id,$txt);  
 	   return true;
   	 }
   	 else
   	 {
   	   return false;
   	 }
     }
     else
      {
       echo " В клановой казне недостаточно ".$str_type." для оплаты!";
   	   return false;      
      }
 
 }

function sell_to_kazna($clan_id,$sum,$item,$delo_txt)
{
	if (mysql_query("UPDATE oldbk.clans_kazna set kr=kr+{$sum}   WHERE `clan_id` = '{$clan_id}' ;"))
    	{
    	txt_to_kazna_log(1,1,$clan_id,$delo_txt);  
    	return true;
    	}
    	else return false;

}

function pay_from_kazna($clan_id,$type,$sum,$coment)
 {
 global $user;
 if ($type==1)
	 {
	 //кредовя
	$ktyle='`kr`'; $str_type='кр.'; $rtype='kr';
	 }
	 elseif ($type==2)
	 {
	 //екровая
	 $ktyle='`ekr`'; $str_type='екр.';  $rtype='ekr';
	 }
	 else
	 {
	   return false;
	 }

    
  $in_kaza=clan_kazna_have($clan_id);
  if ( ($in_kaza) and ($in_kaza[$rtype]>=$sum))
     {
	if (mysql_query("UPDATE oldbk.clans_kazna set {$ktyle}={$ktyle}-{$sum}   WHERE `clan_id` = '{$clan_id}' ;"))
    	{
    	  // записать в дело казны
           $txt="Оплата из клановой казны, ".$sum." ".$str_type." ".$coment;
           txt_to_kazna_log(2,$type,$clan_id,$txt);  
 	   return true;
   	 }
   	 else
   	 {
   	   return false;
   	 }
     }
     else
      {
       echo "<font color=red>В клановой казне не достаточно ".$str_type." для оплаты!</font><br>";
   	   return false;      
      }
 
 }

?>