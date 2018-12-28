<?
   // unset($_SESSION['beginer_quest']);  //убрать после тестов

    function check_status($log=0)
    {
		//$user = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;"));
		for($jj=1;$jj<=count($_SESSION['beginer_quest']);$jj++)
		{
			if($_SESSION['beginer_quest'][$jj][step_f]==1)
			{
				$sql = 'SELECT * FROM oldbk.beginers_quests WHERE id='.$jj.' AND step='.$_SESSION['beginer_quest'][$jj][step].' LIMIT 1;';
			    $data = mysql_query($sql);
			    if(mysql_affected_rows()>0)
			    {
                    while($row=mysql_fetch_assoc($data))
		    		{
                       if($row[answer_1]==1 && $_SESSION['beginer_quest'][$jj][step_f]==1)
                       {
		                     if($user[id]==28453)
							{
								echo 'FIXED';
							}
                       		$_SESSION['beginer_quest'][1][step_f]=0;
                       		mysql_query('update oldbk.beginers_quests_step set step_f=0 where owner = '.$_SESSION['uid'].' AND quest_id='.$jj.';');
                       		$tm='FIXED ERROR ||'. time(). ' || owner:'. $_SESSION['uid'].' || step:'. $row[step];
                   			q_error_log($tm);
                       }
		    		}
			    }
			}
		}
			//echo '<br>';
			/*
			if($_SESSION['beginer_quest'][1][step_f]==1)
	        {
				$sql = 'SELECT * FROM oldbk.beginers_quests WHERE step='.$_SESSION['beginer_quest'][1][step].' LIMIT 1;';
			    $data = mysql_query($sql);
			    if(mysql_affected_rows()>0)
			    {
				    while($row=mysql_fetch_assoc($data))
				    {
                        if($row[answer_1]==1 && $_SESSION['beginer_quest'][1][step_f]==1)
                        {
                            $_SESSION['beginer_quest'][1][step_f]=0;
                            mysql_query('update oldbk.beginers_quests_step set step_f=0 where owner = '.$_SESSION['uid'].';');
                            $tm='FIXED ERROR ||'. time(). ' || owner:'. $_SESSION['uid'].' || step:'. $row[step];
                        	q_error_log($tm);
                        }
				    }
			    }
	        }
	        */
		//q_error_log($log);
    }

if($user[id]==28453)
{	//print_r($_SESSION['beginer_quest']);}
    check_status();

    function q_error_log($tm)
	{
      /*  $load = file("q_error");
        $load=implode('',$load);
		$tm=$load.$tm.'======
		======
		';
		$save = fopen("q_error","w");
		fwrite($save,$tm);
		fclose($save); */
	}


    if(!$_SESSION['beginer_quest']) //для новичков
    {
       $quest_nm=check_quest_step();
      // print_r($quest_nm);
    }
      // $sql = 'SELECT * FROM my_table WHERE';
       //echo $sql;

  function check_quest_step($id=0)
  {

	if (!(isset($_SESSION['uid'])) ) { return 0; } // если нет сессии просто 0

	$user = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;"));
  	$ok=0;
  	$sql='';
  	if($id>0)
  	{
  		$sql=' AND quest_id='.$id;
  	}
  	
  	
  	$data=mysql_query('select * from oldbk.beginers_quests_step where status=0 AND owner = '.$user[id].' '. $sql.' order by id desc;');
	if(mysql_affected_rows()>0)
	{
	  	while($quest_nm= mysql_fetch_array($data))
	  	{
	         //проверили квест. Пишем в сессию данные по квесту ( шаги )
		     if($quest_nm[status]==0)
		     {
		          $_SESSION['beginer_quest'][$quest_nm[quest_id]][status]='0';
		          $_SESSION['beginer_quest'][$quest_nm[quest_id]][step]=$quest_nm[step];
		          $_SESSION['beginer_quest'][$quest_nm[quest_id]][step_f]=$quest_nm[step_f];
		          $_SESSION['beginer_quest'][$quest_nm[quest_id]][qtype]=$quest_nm[qtype];
		          $_SESSION['beginer_quest'][$quest_nm[quest_id]]['count']=$quest_nm['count'];
		          $_SESSION['beginer_quest'][$quest_nm[quest_id]]['qftype']=$quest_nm['qftype'];
		     }
		     else
		     {
		       	  $_SESSION['beginer_quest'][$quest_nm[quest_id]][status]='1';
		     }

	      $ok++;
	    }
    	}

    if($ok==0)
    {
    	$_SESSION['beginer_quest'][none]='1';
    }
    return $ok;
  }

  function take_quest_data($id,$quest_step)
  {
	//$user = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;"));
    $sql='select * from oldbk.beginers_quests where id='.$id.' AND step='.$quest_step.';';
  
  	$q_data=mysql_fetch_assoc(mysql_query($sql));
    return $q_data;
  }

  function check_last_quest($key=0)
   {
		//$user = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;"));

	   	if($key==0)
	   	{//выводим ID наименьшего квеста, требующего подтверждения, для отражения диалога в чате.
			foreach($_SESSION['beginer_quest'] as $id_q => $value)
			{
			       if($_SESSION['beginer_quest'][$id_q][step_f]==0)
			       {
			       	  $last_q=$id_q;
			       }
			}
		    return $last_q;
	        }

		// 1 Выбивалка - поиск предмета в инвентаре
		// 2 Собиралка - чекаем кол-во предметов нужных для квеста
		// 3 Убивалка - из боевки с любовью
		// 4 Характеристики перса
		// 5 Локации нахождения
		// 7 регистрация
		// 20 окончание боя
		// 21 внутри боя
		//11 - детский квест ристалка - одиночка
	    elseif($key==2 || $key==4 || $key==5 || $key==6 || $key==9 || $key==10 || $key==11  || $key==20 || $key==7 || $key==30)//возвращаем квесты, в зависимости от ключа входа (тип квеста)
	    {
	       $last_q=array();
	       	foreach($_SESSION['beginer_quest'] as $id_q => $value)
		    {
		       if($_SESSION['beginer_quest'][$id_q][qtype]==$key)
		       {
		       	  $last_q[$id_q]=$_SESSION['beginer_quest'][$id_q];
		       }
		    }
		    return $last_q;
	    }
   }

	function make_quest_div($jq=false)
	{
		$user = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;"));

        $sh_div='none';
        $last_q=0;

        if(!$_SESSION['beginer_quest'][none])
        {
	       $last_q=check_last_quest();
        }


        if($last_q>0)
        {
           $sh_div='block';
        }
     //   print_r($_SESSION);

            if ($jq==false)
            	{
		echo '        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.1/jquery.min.js"></script>';            	
            	}
            ?>
		<script>
			function answer(id,step)
			{
			       $.get('quest_get.php?q='+id+'&s='+step, function(data) {
					  $('#quest').html(data);
					});

			}
		</script>
		<div id="quest" style="z-index: 300; position: absolute; left: 50px; top: 30px;
					background: #DEDCDD url('http://i.oldbk.com/i/quest/fp_1.png') no-repeat;
					background-position: top;
					width: 688px;
					border: 1px solid black; display: <?=$sh_div?>;"><br>
				<? echo "<table cellpadding=3 cellspasing=3 style=\"background: url('http://capitalcity.oldbk.com/i/quest/fp_2.jpg') repeat-y; width:688px;\">
			    	<tr>
		            	<td>&nbsp;&nbsp;";
		            			if($last_q>0)
		            			{
		            				$prnt=show_quest_dialogs($last_q);
		            				echo '&nbsp;&nbsp;'.$prnt.'&nbsp;&nbsp;';
		            			}
		            		?>
		            	</td>
		            	<td>&nbsp;</td>
		              </tr>
		    	 </table>
                 <img src="http://i.oldbk.com/i/quest/fp_3.png">
			</div>
			<?
	}

	function show_quest_dialogs_for_new_sost($last_q,$type=0)
	{
 		global $db_city,$city_name;
/*
когда пишешь квест, если надо описать различное описание городов (на будущее тоже учтено)
делаешь так:
[city_desc]опискние кепа|описание авалона[/city_desc]
(на будущее)
просто прибавляешь новое описание через |
[city_desc]опискние кепа|описание авалона|описание нового города[/city_desc]
============
отразить название текущего города:
{this_city}
отразить названия других городов
{other_city} (если будет больше городов, будут через , выводится
 
Если ты соглас{ен|на} - давай начнем!<BR> // пол
[a1]{Согласен|Согласна}[/a]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; [a2]Нет, не нуждаюсь[/a]</center> //ответ 1 (выбор пола) ответ 2
(кол-во ответов ограниченно фантазией, не зватает только небольшого описания выбранного ответа к нужному шагу 
(это в ответе на квете, пару строк добавить, в базе поля заложены для древовидных квестов
*/

		$this_city=$city_name[CITY_ID];
		
 		for($i=0;$i<count($db_city);$i++)
		{
			$m.="(.*?)\|"; //кол-во поиска в зависимости от кол-ва городов.
			if($i!=CITY_ID)
			{
				$other_city.=$city_name[$i].','; //текстом выводим все другие города
			}
		}
		$other_city=substr($other_city,0,-1);
 		$m=substr($m,0,-2); 
 		$city='\\'.(CITY_ID+1);//для регуляки ID города +1 (кеп 0, но в регулярке это будет совпадение 1)
 		
 		$user = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;"));
 		$quest_data=take_quest_data($last_q, $_SESSION['beginer_quest'][$last_q][step]);
		
		$text = preg_replace('#\{login\}#si', '<b>'.$user[login].'</b>', $quest_data[qstart]); //подставляем логин
		
		$text = preg_replace('#\{this_city\}#si', '<b>'.$this_city.'</b>', $text);// подставляем текущий город
		$text = preg_replace('#\{other_city\}#si', '<b>'.$other_city.'</b>', $text);//все другие города		
		$text = preg_replace('#\[city_desc\]'.$m.'\[/city_desc\]#si', $city, $text);//описание текущего города
		$text = preg_replace('#\{(.*?)\|(.*?)\}#si', ($user[sex]==1?'\1':'\2'), $text);//пол (в том числе и окончания)
		
		
		if($type==0)
		{
        		$text = preg_replace('#\[a(.*?)\](.*?)\[/a\]#si', '<a style="cursor: pointer;" onclick=\'answer("\1","'.$quest_data[step].'");\'>\2</a>', $text); //нужные ответы в зависимости от квеста
        	}

	        if($type==1)
	        {
	           $text = preg_replace('#\[a(.*?)\](.*?)\[/a\]#si', '', $text);
	           $outbuf[1]='<b>'.$quest_data[qname].'</b>';
	           $outbuf[2]=$text;
				if($quest_data[qfin]>0)
				{
	               $qcounts=mysql_fetch_array(mysql_query('select * from oldbk.beginers_quests_step where owner ='.$_SESSION[uid].' AND quest_id='.$last_q.' ;'));
	               $outbuf[2].='&nbsp;<b>('.$qcounts['count'].'/'.$quest_data[qfin].')</b>';
				}
	           if($quest_data[id]!=7)
	           {
	           $outbuf[3]='<b><a onclick="if (!confirm(\'Отказаться от квеста?\')) { return false; } " href="?reject='.$quest_data[id].'&edit=1&effects=1">Отказаться</a></b>';
	           }
	        }


		if($quest_data[nps_img]!='' && $type==0)
		{
			$quest_data[nps_img]=str_replace('{s}',($user[sex]==1?'g':'m'),$quest_data[nps_img]);
			$outbuf[1]='<img src=http://i.oldbk.com/i/quest/'.$quest_data[nps_img].'>';
			$outbuf[2]=$text;
			$outbuf[3]=' ';			
		}
		return  $outbuf;
	}

	function show_quest_dialogs($last_q,$type=0)
	{
 		global $db_city,$city_name;
/*
когда пишешь квест, если надо описать различное описание городов (на будущее тоже учтено)
делаешь так:
[city_desc]опискние кепа|описание авалона[/city_desc]
(на будущее)
просто прибавляешь новое описание через |
[city_desc]опискние кепа|описание авалона|описание нового города[/city_desc]
============
отразить название текущего города:
{this_city}
отразить названия других городов
{other_city} (если будет больше городов, будут через , выводится
 
Если ты соглас{ен|на} - давай начнем!<BR> // пол
[a1]{Согласен|Согласна}[/a]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; [a2]Нет, не нуждаюсь[/a]</center> //ответ 1 (выбор пола) ответ 2
(кол-во ответов ограниченно фантазией, не зватает только небольшого описания выбранного ответа к нужному шагу 
(это в ответе на квете, пару строк добавить, в базе поля заложены для древовидных квестов
*/

		$this_city=$city_name[CITY_ID];
		
 		for($i=0;$i<count($db_city);$i++)
		{
			$m.="(.*?)\|"; //кол-во поиска в зависимости от кол-ва городов.
			if($i!=CITY_ID)
			{
				$other_city.=$city_name[$i].','; //текстом выводим все другие города
			}
		}
		$other_city=substr($other_city,0,-1);
 		$m=substr($m,0,-2); 
 		$city='\\'.(CITY_ID+1);//для регуляки ID города +1 (кеп 0, но в регулярке это будет совпадение 1)
 		
 		$user = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;"));
 		$quest_data=take_quest_data($last_q, $_SESSION['beginer_quest'][$last_q][step]);
		
		$text = preg_replace('#\{login\}#si', '<b>'.$user[login].'</b>', $quest_data[qstart]); //подставляем логин
		
		$text = preg_replace('#\{this_city\}#si', '<b>'.$this_city.'</b>', $text);// подставляем текущий город
		$text = preg_replace('#\{other_city\}#si', '<b>'.$other_city.'</b>', $text);//все другие города		
		$text = preg_replace('#\[city_desc\]'.$m.'\[/city_desc\]#si', $city, $text);//описание текущего города
		$text = preg_replace('#\{(.*?)\|(.*?)\}#si', ($user[sex]==1?'\1':'\2'), $text);//пол (в том числе и окончания)
		
		
		if($type==0)
		{
        		$text = preg_replace('#\[a(.*?)\](.*?)\[/a\]#si', '<a style="cursor: pointer;" onclick=\'answer("\1","'.$quest_data[step].'");\'>\2</a>', $text); //нужные ответы в зависимости от квеста
        	}

	        if($type==1)
	        {
	           $text = preg_replace('#\[a(.*?)\](.*?)\[/a\]#si', '', $text);
	           $text='<tr>
	           <td align=middle valign=top><b>'.$quest_data[qname].'</b></td><td valign=top>'.$text;
				if($quest_data[qfin]>0)
				{	               $qcounts=mysql_fetch_array(mysql_query('select * from oldbk.beginers_quests_step where owner ='.$_SESSION[uid].' AND quest_id='.$last_q.' ;'));
	               $text=$text.'&nbsp;<b>('.$qcounts['count'].'/'.$quest_data[qfin].')</b>';				}
	
	           $text=$text.'</td><td>';
	           if($quest_data[id]!=7)
	           {
	           	$text=$text.'<b><a onclick="if (!confirm(\'Отказаться от квеста?\')) { return false; } " href="?reject='.$quest_data[id].'&edit=1&effects=1">Отказаться</a></b>';
	           }
	           $text=$text.'</td></tr>';
	        }


		if($quest_data[nps_img]!='' && $type==0)
		{
			$quest_data[nps_img]=str_replace('{s}',($user[sex]==1?'g':'m'),$quest_data[nps_img]);
			$text='</td><td valign=top><img src=http://i.oldbk.com/i/quest/'.$quest_data[nps_img].'></td><td>'.$text;
		} elseif ($quest_data[nps_img] == '' && $type==0) {
			$text='</td><td valign=top>&nbsp;</td><td>'.$text;
		}
		
		return  $text;
	}


	function add_quest_prize($q_data,$status=0,$user,$session)
	{
	   	//$q_data - масив квеста, с которого возьмется приз
		
		$get_ivent=mysql_fetch_array(mysql_query("select * from oldbk.ivents where id=6"));
		//6) Храмовые квесты - за выполнение храмового квеста дается двойная награда. неважно когда квест был взят. главное выполнить его в течение недели эвента.


	   		$sql='';
	   		$dtxt='';
	   		$txt='<font color=red>Внимание!</font> За выполнение задания вы получили ';
	   		$snd=0;

        	if($q_data['exp']>0)
        	{
					if ($get_ivent['stat']==1)
					{
					$q_data['exp']=$q_data['exp']*2;
					$exp_bonus=' (x2 Бонус)';
					}
					
	               $sql.=' exp=exp+'.$q_data['exp'].',';
	               $txt.=$q_data['exp'].$exp_bonus.' опыта, ';
	               if(olddelo==1)
	               {
	               		$dtxt.=$q_data['exp'].'оп,';
	               }
	               $snd=1;
        	}
            	if($q_data['kr']>0)
        	{
					if ($get_ivent['stat']==1)
					{
					$q_data['kr']=$q_data['kr']*2;
					$kr_bonus=' (x2 Бонус)';
					}
                		$rec['owner']=$user[id];
				$rec['owner_login']=$user[login];
				$rec['owner_balans_do']=$user[money];
				$user['money'] += $q_data['kr'];
				$rec['owner_balans_posle']=$user[money];
				$rec['target']=0;
				$rec['target_login']="Квесты";
				$rec['type']=181;//кр за квест
				$rec['sum_kr']=$q_data['kr'];
				$rec['sum_ekr']=0;
				$rec['sum_kom']=0;
				$rec['add_info']=$q_data[qname].$kr_bonus;
				add_to_new_delo($rec);

				$sql.=' money=money+'.$q_data['kr'].',';

               			 $txt.=$q_data['kr'].$kr_bonus.' кредитов, ';
 
               			$snd=1;
        	}
            	if($q_data['repa']>0)
        	{
        	
				        	//дополнительный бонус
						$eff = mysql_fetch_array(mysql_query("SELECT * FROM `effects` WHERE `owner` = '{$user['id']}'  and `type` = '9101' ;")); 	
						if ($eff['id']>0)
							{
							$q_data['repa']+=$q_data['repa']*$eff['add_info'];
							}
        	
		        	if ($get_ivent['stat']==1)
					{
					$q_data['repa']=$q_data['repa']*2;
					$rep_bonus=' (x2 Бонус)';
					}
        	
		mysql_query("INSERT INTO `oldbk`.`users_rep_log` SET `onwer`={$user[id]},`lvl`={$user[level]},`sdate`=NOW(),`rep_hram`=`rep_hram`+{$q_data['repa']} ON DUPLICATE KEY UPDATE `rep_hram`=`rep_hram`+{$q_data['repa']};");
        	
                		$rec['owner']=$user[id];
				$rec['owner_login']=$user[login];
				$rec['owner_balans_do']=$user[money];
				//$user['money'] += $q_data['kr'];
				$rec['owner_balans_posle']=$user[money];
				$rec['owner_rep_do']= $user['repmoney'];
				$user['repmoney']+=$q_data['repa'];
				$rec['owner_rep_posle']=$user['repmoney'];
				$rec['target']=0;
				$rec['target_login']="Квесты";
				$rec['type']=182;//репа за квест
				$rec['sum_kr']=0;
				$rec['sum_ekr']=0;
				$rec['sum_rep']=$q_data['repa'];
				$rec['sum_kom']=0;
				$rec['add_info']=$q_data[qname].$rep_bonus;
				add_to_new_delo($rec);

		               $sql.=' rep=rep+'.$q_data['repa'].', repmoney=repmoney+'.$q_data['repa'].',';
		               $txt.=$q_data['repa'].$rep_bonus.' репутации, ';

               			$snd=1;
        	}

        	if($q_data[effect_prize]!='')
        	{
              		$effect_names=array(4999 => 'Silver account');
              		$effects=explode(';',$q_data[effect_prize]);
			for($jj=0;$jj<count($effects);$jj++)
			{
				$effect=explode(':',$effects[$jj]);
				$no_break_add=true;
				  //fix by Fred
				  //prem:1:4999:604800
				if($effect[2]==4999)//Сильвер
				{
				      // проверяем может у него уже есть сильвер?
				        $i_have_silver=mysql_fetch_array(mysql_query("select * from effects where type=4999 and owner={$user[id]} ;"));
				        if ($i_have_silver[id] > 0)
				      	{
					      	//уже есть
					      	    $sql2="UPDATE effects set `time`=`time`+{$effect[3]} where id={$i_have_silver[id]};";
					   		 mysql_query($sql2);
				      	}
				      	else //проверяем другие премиумы
				      	if($user[prem]>1)
				      	{
				      		//ничего не добавляем, так как есть голд или платинум
				      		$no_break_add=false;
				      	}
				      	else
				      	{
				      	//нету
						    $sql.= ' `'.$effect[0].'` = `'.$effect[0].'`+'.$effect[1].',';
						    $sql2='insert into effects set type='.$effect[2].', name="'.$effect_names[$effect[2]].'",
						    owner='.$user[id].', time='.(time()+$effect[3]);
						    mysql_query($sql2);

				      	}
				}
				else
				{

					    $sql.= ' `'.$effect[0].'` = `'.$effect[0].'`+'.$effect[1].',';
					    $sql2='insert into effects set type='.$effect[2].', name="'.$effect_names[$effect[2]].'",
					    owner='.$user[id].', time='.(time()+$effect[3]);
					    mysql_query($sql2);

				}

				$txt.='<a href="http://oldbk.com/encicl/?/silver.html" target=_blank>'.$effect_names[$effect[2]].'</a>, ';
		                $snd=1;
		                
				if($effect[2]==4999)//Сильвер
				{         //`medals`=concat(`medals`,\'036;\'),
		                        if($no_break_add==true)
		                        {
		                        	$sql.=' `expbonus`=`expbonus`+ 0.1,';
		                        }
					/*
					$money_befor= mysql_fetch_array(mysql_query("SELECT * FROM oldbk.bank where owner='".$user[id]."' LIMIT 1"));
		                        $sql3='update oldbk.bank set ekr=ekr+5 where owner = '.$user[id].' limit 1;';
					
					$rec['owner']=$user[id];
					$rec['owner_login']=$user[login];
					$rec['owner_balans_do']=$user['money'];
					$rec['owner_balans_posle']=$user['money'];
					$rec['target']=0;
					$rec['target_login']='Квесты';
					$rec['type']=1111;
					$rec['sum_kr']=0;
					$rec['sum_ekr']=5;
					$rec['bank_id']=$money_befor[id];
					
					$rec['add_info']='Баланс: '.$money_befor[ekr].'/'.($money_befor[ekr]+5);
					add_to_new_delo($rec); //юзеру

					mysql_query($sql3);
					mysql_query("INSERT INTO oldbk.`dilerdelo` (dilerid,dilername,bank,owner,ekr) 
					values 
					('8','Комментатор','{$money_befor[id]}','{$user['login']}','5');");
					*/
                        
				}
			}
        	}

        	if($sql!='')
        	{
        		//echo $sql;
	            $sql=substr($sql,0,-1);
	            $sql='update users set '.$sql.' WHERE id='.$user[id].';';
	            mysql_query($sql);
            }




            if($q_data['shop_prize']!=0)
            {

	               $dur=0;
	               if($q_data['id']==1)
	               {
		               $dur=2;
	               }
	               $present='';
	               if($q_data[shop_prize_gift]==1)
	               {
	                   $present='Мироздатель';
	               }
    			   $pr_id=explode(';',$q_data['shop_prize']);
	                   for($jj=0;$jj<count($pr_id);$jj++)
	                   {
		                $data = (mysql_query("SELECT * FROM oldbk.`shop` WHERE `id` in (".$pr_id[$jj].");"));
		                while($dress=mysql_fetch_array($data))
		                {
		                  	 $sql="INSERT INTO oldbk.`inventory`
					(`prototype`,`duration`,`owner`,`name`,`type`,`massa`,`cost`,`img`,`maxdur`,`isrep`,
						`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,`needident`,`nsila`,`nlovk`,`ninta`,
						`nintel`,`nmudra`,`nvinos`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,
						`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`bron1`,`bron2`,`bron3`,`bron4`,`maxu`,`minu`,`magic`,`nlevel`,`nalign`,`dategoden`,`goden`,
						`otdel`,`gmp`,`gmeshok`, `group`,`letter`,`present`,`idcity`
					)
					VALUES
					('{$dress['id']}','".$dur."','{$user['id']}','{$dress['name']}','{$dress['type']}',{$dress['massa']},{$dress['cost']},'{$dress['img']}',{$dress['maxdur']},{$dress['isrep']},
					'{$dress['gsila']}','{$dress['glovk']}','{$dress['ginta']}',
					'{$dress['gintel']}','{$dress['ghp']}','{$dress['gnoj']}','{$dress['gtopor']}','{$dress['gdubina']}','{$dress['gmech']}','{$dress['gfire']}','{$dress['gwater']}','{$dress['gair']}',
					'{$dress['gearth']}','{$dress['glight']}','{$dress['ggray']}','{$dress['gdark']}','{$dress['needident']}','{$dress['nsila']}','{$dress['nlovk']}','{$dress['ninta']}',
					'{$dress['nintel']}','{$dress['nmudra']}','{$dress['nvinos']}','{$dress['nnoj']}','{$dress['ntopor']}','{$dress['ndubina']}','{$dress['nmech']}',
					'{$dress['nfire']}','{$dress['nwater']}',
					'{$dress['nair']}','{$dress['nearth']}','{$dress['nlight']}','{$dress['ngray']}','{$dress['ndark']}',
					'{$dress['mfkrit']}','{$dress['mfakrit']}','{$dress['mfuvorot']}','{$dress['mfauvorot']}','{$dress['bron1']}',
					'{$dress['bron2']}','{$dress['bron3']}','{$dress['bron4']}','{$dress['maxu']}','{$dress['minu']}','{$dress['magic']}','{$dress['nlevel']}',
					'{$dress['nalign']}','".(($dress['goden'])?($dress['goden']*24*60*60+time()):"")."','{$dress['goden']}'
					,'{$dress['razdel']}','{$dress['gmp']}','{$dress['gmeshok']}','{$dress['group']}','{$dress['letter']}','{$present}','{$user[id_city]}'
					) ;";
		                   	if(mysql_query($sql))
					{
						$dress[id]=mysql_insert_id();
						$dress[idcity]=$user[id_city];
						$txt.=$dress[name].', ';
	
						$dressid=get_item_fid($dress);
						$rec['owner']=$user[id];
						$rec['owner_login']=$user[login];
						$rec['owner_balans_do']=$user[money];
						//$user['money'] -= $_POST['count']*$dress['cost'];
						$rec['owner_balans_posle']=$user[money];
						$rec['target']=0;
						$rec['target_login']="Квесты";
						$rec['type']=180;//подарок за квест
						$rec['sum_kr']=0;
						$rec['sum_ekr']=0;
						$rec['sum_kom']=0;
						$rec['item_id']=$dressid;
						$rec['item_name']=$dress['name'];
						$rec['item_count']=1;
						$rec['item_type']=$dress['type'];
						$rec['item_cost']=$dress['cost'];
						$rec['item_dur']=$dress['duration'];
						$rec['item_maxdur']=$dress['maxdur'];
						$rec['item_ups']=0;
						$rec['item_unic']=0;
						$rec['item_incmagic']='';
						$rec['item_incmagic_count']='';
						$rec['item_arsenal']='';
						$rec['add_info']=$q_data[qname];
						add_to_new_delo($rec);
						$snd=1;
						$ok=true;
					}

	                    	}
	                }
            }


            if($q_data['eshop_prize']!=0)
            {
                $present='';
                if($q_data[eshop_prize_gift]==1)
                {
                	$present='Мироздатель';
                }

                $pr_id=explode(';',$q_data['eshop_prize']);
                for($jj=0;$jj<count($pr_id);$jj++)
	            {
	               $data = (mysql_query("SELECT * FROM oldbk.`eshop` WHERE `id` in (".$pr_id[$jj].");"));
	                while($dress=mysql_fetch_array($data))
	                {

           			if(mysql_query("INSERT INTO oldbk.`inventory`
				(`ecost`,`prototype`,`owner`,`name`,`type`,`massa`,`cost`,`img`,`maxdur`,`isrep`,
					`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,`needident`,`nsila`,`nlovk`,`ninta`,`nintel`,`nmudra`,`nvinos`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,
					`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`bron1`,`bron2`,`bron3`,`bron4`,`maxu`,`minu`,`magic`,`nlevel`,`nalign`,`dategoden`,`goden`,
					`otdel`,`gmp`,`gmeshok`, `group`,`letter`,`present`,`idcity`
				)
				VALUES
				('{$dress['ecost']}','{$dress['id']}','{$user['id']}','{$dress['name']}','{$dress['type']}',{$dress['massa']},{$dress['cost']},'{$dress['img']}',{$dress['maxdur']},{$dress['isrep']},'{$dress['gsila']}','{$dress['glovk']}','{$dress['ginta']}','{$dress['gintel']}','{$dress['ghp']}','{$dress['gnoj']}','{$dress['gtopor']}','{$dress['gdubina']}','{$dress['gmech']}','{$dress['gfire']}','{$dress['gwater']}','{$dress['gair']}','{$dress['gearth']}','{$dress['glight']}','{$dress['ggray']}','{$dress['gdark']}','{$dress['needident']}','{$dress['nsila']}','{$dress['nlovk']}','{$dress['ninta']}','{$dress['nintel']}','{$dress['nmudra']}','{$dress['nvinos']}','{$dress['nnoj']}','{$dress['ntopor']}','{$dress['ndubina']}','{$dress['nmech']}',
				'{$dress['nfire']}','{$dress['nwater']}',
				'{$dress['nair']}','{$dress['nearth']}','{$dress['nlight']}','{$dress['ngray']}','{$dress['ndark']}',
				'{$dress['mfkrit']}','{$dress['mfakrit']}','{$dress['mfuvorot']}','{$dress['mfauvorot']}','{$dress['bron1']}','{$dress['bron2']}','{$dress['bron3']}','{$dress['bron4']}','{$dress['maxu']}','{$dress['minu']}','{$dress['magic']}','{$dress['nlevel']}',
				'{$dress['nalign']}','".(($dress['goden'])?($dress['goden']*24*60*60+time()):"")."','{$dress['goden']}'
				,'{$dress['razdel']}','{$dress['gmp']}','{$dress['gmeshok']}','{$dress['group']}','{$dress['letter']}','{$present}','{$user[id_city]}'
				) ;"))
				{	
					$dress[id]=mysql_insert_id();
					$dress[idcity]=$user[id_city];
            				$txt.=$dress[name].', ';
            

					$dressid=get_item_fid($dress);
					$rec['owner']=$user[id];
					$rec['owner_login']=$user[login];
					$rec['owner_balans_do']=$user[money];
					//$user['money'] -= $_POST['count']*$dress['cost'];
					$rec['owner_balans_posle']=$user[money];
					$rec['target']=0;
					$rec['target_login']="Квесты";
					$rec['type']=180;//подарок за квест
					$rec['sum_kr']=0;
					$rec['sum_ekr']=0;
					$rec['sum_kom']=0;
					$rec['item_id']=$dressid;
					$rec['item_name']=$dress['name'];
					$rec['item_count']=1;
					$rec['item_type']=$dress['type'];
					$rec['item_cost']=$dress['cost'];
					$rec['item_dur']=$dress['duration'];
					$rec['item_maxdur']=$dress['maxdur'];
					$rec['item_ups']=0;
					$rec['item_unic']=0;
					$rec['item_incmagic']='';
					$rec['item_incmagic_count']='';
					$rec['item_arsenal']='';
					$rec['add_info']=$q_data[qname];
					add_to_new_delo($rec);

					$snd=1;
					$ok=true;
				}

	                }
	            }
            }
	       $txt=substr($txt,0,-2);
	       if($snd==1)
	       {
	       	  	addchp ($txt,'{[]}'.$user['login'].'{[]}',$user['room'],$user['id_city']);
	       }


	      // addchp($q_data[step].'шаг завершен '.$txt,'{[]}A-Tech{[]}');
	       $ok=true;
		return $ok;

	}

    function use_quest_dialogs($q, $s, $fin=0)
    {
		$user = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;"));

         	$last_q=check_last_quest();
	 	 if($_SESSION['beginer_quest'][$last_q])
	  	 {
		       	 $quest_data=take_quest_data($last_q, $_SESSION['beginer_quest'][$last_q][step]);
		       	 if($quest_data['answer_'.$q]>0) //проверить а есть ли дальшешаг
		       	 {
		             if($quest_data['answer_'.$q]==1)
		             {
			       	 	 //шаг закончен. обновляемся на следующий. получаем приз, если таковой есть
			       	 	 $sql='update oldbk.beginers_quests_step set step_f = 0, step=step+1, qtype='.$quest_data[qtype].' where quest_id = '.$last_q.' and owner='.$_SESSION[uid].';';
	
	
			       	 	$wrdata=time().'|| 1st qtype:' .$quest_data[qtype] . '|| qstep:'. $quest_data[step].' || '.$sql;
			       	 	q_error_log($wrdata);
			       	 	 if(mysql_query($sql))
			       	 	 {                    //q_data,1,$user,$session
			       	 	 	if(add_quest_prize($quest_data,0,$user,'ON'))
		                    {
			       	 	 	   $_SESSION['beginer_quest'][$last_q][step]+=1;
			          		   $_SESSION['beginer_quest'][$last_q][qtype]=$quest_data[qtype];
	
			       	 	 	   $prnt=show_quest_dialogs($last_q);
			            	   echo $prnt;
			       	 	 	   //echo 'next step';
			       	 	 	}
			       	 	 	else
			       	 	 	{
			       	 	 		echo 'ошибка 002';
			       	 	 	}
			       	 	 }
			       	 	 else
			       	 	 {
			       	 	 	$sql='e1 ';
			       	 	 	q_error_log($sql);
			       	 	 	echo 'ошибка 001';
			       	 	 }
		             }
		             elseif($quest_data['answer_'.$q]==2) //приняли задание. выполняем. не переходим на следующее/.
		             //закрываем диалоговое окно
		             {
		                $sql='update oldbk.beginers_quests_step set step_f=1, qtype='.$quest_data[qtype].' where quest_id = '.$last_q.' and owner='.$_SESSION[uid].';';
	
		                $wrdata=time().'|| 2nd qtype:' .$quest_data[qtype] . '|| qstep:'. $quest_data[step].' || '.$sql;
			       	 	q_error_log($wrdata);
	
				       	if(mysql_query($sql))
				       	{
	
				       	 	$_SESSION['beginer_quest'][$last_q][step_f]=1;
				       	 	$_SESSION['beginer_quest'][$last_q][step]=$quest_data[step];
				            	$_SESSION['beginer_quest'][$last_q][qtype]=$quest_data[qtype];
			                	echo '<script>$("#quest").css("display","none");</script>';
					}
				       	else
				       	{
			       	 	 	echo 'ошибка 301';
	                        		$sql='prev !!!e2!! ';
			       	 	 	q_error_log($sql);
				       	}
	
		             }
		             elseif($quest_data['answer_'.$q]==10)
		             {
			       	 	 //квест закончен. получаем приз, если таковой есть
		                 if(mysql_query('update oldbk.beginers_quests_step set status=1 where quest_id = '.$last_q.' and owner='.$_SESSION[uid].';'))
			       	 	 {
			       	 	 	if(add_quest_prize($quest_data,1,$user,'ON'))
		                    		{
			       	 	 	   unset($_SESSION['beginer_quest'][$last_q]);
			       	 	 	   echo '<script>$("#quest").css("display","none");</script>';
			       	 	 	   //echo '<script>$("#quest").css("display","none");</script>';
			       	 	 	}
			       	 	 	else
			       	 	 	{
			       	 	 		echo 'ошибка 012';
			       	 	 	}
			       	 	 }
			       	 	 else
			       	 	 {
			       	 	 	echo 'ошибка 011';
			       	 	 }
		             }
		       	 }
		       	 elseif($quest_data['answer_'.$q]==0)
		       	 {
		       	 	//отказались от квеста
				if(mysql_query('update oldbk.beginers_quests_step set status=1, step_f=0 where quest_id = '.$last_q.' and owner='.$_SESSION[uid].';'))
				 {
				    unset($_SESSION['beginer_quest'][$last_q]);
				    //echo '<script>$("#quest").css("display","none");</script>';
				}
				else
				{
					echo 'ошибка 031';
				}
			}
	     }
    }

function system_finish_step($q_data,$user_id=0,$session='ON')
{      //[id] => 1 [step] => 3 [answer_1] => 2 [answer_2] => 0

        if($user_id==0 && $session=='ON' )
        {
			$user = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;"));
        }
        else
        if($user_id>0 && $session=='OFF')
        {        	$user = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `id` = '{$user_id}' LIMIT 1;"));        }
        else
        if($user_id>0 && $session=='ON')
        {
        	$user = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `id` = '{$user_id}' LIMIT 1;"));
        }

	    $last_q=$q_data[id];

	    $sql='update oldbk.beginers_quests_step set step_f=0 where quest_id = '.$last_q.' and owner='.$user[id].';';
        mysql_query($sql);

if ($user_id==14897)
	{
	echo "DD1";
	echo $sql;
	print_r($q_data);
	}        

        //$wrdata=time().'|| 3d qtype:' .$q_data[qtype] . '|| qstep:'. $q_data[step].' || '.$sql;
		//q_error_log($wrdata);

        if($q_data[answer_1]==10)
        {
        	//echo 'Квест завершен';
        	//функция завершения квеста
        	if(mysql_query('update oldbk.beginers_quests_step set status=1, quest_count=quest_count+1 where quest_id = '.$last_q.' and owner='.$user[id].';'))
		{
	       	 	if(add_quest_prize($q_data,1,$user,$session))
                    	{
       	 	 	   if($session=='ON')
       	 	 	   {
       	 	 	   	unset($_SESSION['beginer_quest'][$last_q]);
       	 	 	   }
	       		}
       	 	 	else
       	 	 	{
       	 	 		echo 'ошибка 012';
       	 	 	}
		}
		else
		{
			echo 'ошибка 011';
		}
        }
        else
        {   //следующий шаг
        	//выдаем приз
		    //echo $sql;
	        if(add_quest_prize($q_data,0,$user,$session))
	        {
	        	$q_data=take_quest_data($q_data[id],($q_data[step]+1));
       			$sql='update oldbk.beginers_quests_step set step='.$q_data[step].', qtype='.$q_data[qtype].', step_f=0 where quest_id = '.$last_q.' and owner='.$user[id].';';
          		$wrdata=time().'|| 4th qtype:' .$q_data[qtype] . '|| qstep:'. $q_data[step].' || '.$sql;
				q_error_log($wrdata);
                	if(mysql_query($sql))
	    		{
	        		//unset($_SESSION['beginer_quest'][$last_q]);
	                    //$quest_nm=check_quest_step();
	 	 	  			//$prnt=show_quest_dialogs($last_q);
	                    	if($session=='ON')
		       	 	{
		          		$_SESSION['beginer_quest'][$last_q][step_f]='0';
                    			$_SESSION['beginer_quest'][$last_q][step]=$q_data[step];
		          		$_SESSION['beginer_quest'][$last_q][qtype]=$q_data[qtype];
	                    	}
	                    	if($_SERVER['PHP_SELF'] != '/shop.php' && $session=='ON')
	                    	{
		      	  		echo '<script>location.href="'.$_SERVER['REQUEST_URI'].'";</script>';
		      	  	}
		      	  	if($_SERVER['PHP_SELF'] == '/shop.php' && $session=='ON')
	                    	{
	      	  			echo '<script>location.href="'.$_SERVER['PHP_SELF'].'";</script>';
	      	  		}

	      	  		//добавить системку.
      	    		}
	      	}
        }

       // print_r($q_data);
	}

	function quest_check_type_4($last_q)
	{
 		$user = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;"));
		foreach($last_q as $key=>$value)
		{
	       		$quest_data=take_quest_data($key,$value[step]);
	       		//
			$check_stats_mass=explode(';',$quest_data[qcondition]);
			$checker_all=0; //суммируем выполнение подзаданий для шага
			for($i=0; $i<count($check_stats_mass);$i++)
			{
				$check_stats[$i]=explode(':',$check_stats_mass[$i]);
				
				if($check_stats[$i][1]==0)
				{   //проверка на то чтоб добиться нуля ( на пример распределить статы)
					if($user[$check_stats[$i][0]]==$check_stats[$i][1])
					{
				   		$checker_all+=1;
					}
				}
				
				if($check_stats[$i][1]>0)
				{  //проверка на достижение зар-ки.. на пример получить 50 ловки	echo 'qwe2';
					if($user[$check_stats[$i][0]]>=$check_stats[$i][1])
				   	{
				      		$checker_all+=1;
				    	}
				}
			}
	
		           if(count($check_stats_mass)==$checker_all)
		           {
		           	//	echo 'QUEST_STEP_FINIT';
		           		system_finish_step($quest_data);
		           }
		}
	}

	function quest_check_type_5($last_q) //функция проверяет местонаходждение перса для квестов типа 5
	{
		$user = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;"));
		foreach($last_q as $key=>$value)
		{
			$quest_data=take_quest_data($key,$value[step]);
			$user_room=explode(',',$quest_data[qcondition]);
			if(in_array($user[room],$user_room))
			{
				system_finish_step($quest_data);
			}
		
		}
	}

	function quest_check_type_6($last_q) //функция проверяет наличие счета в банке
	{
		$user = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;"));
		foreach($last_q as $key=>$value)
		{
		    $quest_data=take_quest_data($key,$value[step]);
			$sql = 'SELECT * FROM oldbk.bank WHERE owner = '.$user[id].' limit 1;';
			$data = mysql_fetch_array(mysql_query($sql));
		    if($data[id])
		    {
		        system_finish_step($quest_data);
		    }
		}
	}

	function quest_check_type_7($last_q,$und=0)
	{
		$user = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;"));
        	$ok=0;

     		if ($_POST['sex'] != "0" && $_POST['sex'] != "1") {
			$err.= "Укажите ваш пол! ";
			$stop =1;
		}
		elseif ($_POST['birth_day']<1 || $_POST['birth_day']>31) {
			$err.= "Укажите дату рождения! ";
			$stop =1;
		}
		elseif ($_POST['birth_month']<1 || $_POST['birth_month']>12) {
			$err.= "Укажите месяц рождения! ";
			$stop =1;
		}
		elseif ($_POST['birth_year']<1940 || $_POST['birth_year']>2005) {
			$err.= "Укажите год рождения! ";
			$stop =1;
		}
		elseif ($_POST['psw']=='') {
			$err.= "Введите пароль! ";
			$stop =1;
			//echo 'QWE'. $err;
		}
		elseif ($_POST['psw']!=$_POST['psw2']) {
			$err.= "Пароли не совпадают! ";
			$stop =1;
		}
		elseif (strlen($_POST['psw'])<6 || strlen($_POST['psw'])>20 ) {
			$err.= "Пароль должен быть от 6 до 20 символов! ";
			$stop =1;
		}
		elseif (!preg_match('~^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)+$~i',$_POST['email'])) {
			$err.= "Неверный формат почты! ";
			$stop =1;
		}


		if($stop!=1 && $_SESSION[vk][viewer_id]==$user[vk_user_id] && $user[vk_user_id]>0 /*&& $user[pass]=='' && $user[email]==''*/) 
		{
            		include "alg.php";
            		$birth_day=$_POST['birth_day'];
					$birth_month=$_POST['birth_month'];
					$birth_year=$_POST['birth_year'];
					$birthday=$birth_day."-".$birth_month."-".$birth_year;

	            if(mysql_query("update users set pass='".in_smdp_new($_POST['psw'])."', email='".$_POST['email']."', borndate='".$birthday."', sex='".$_POST['sex']."'
	            WHERE id = ".$user[id].";"))
	            {	            	//ECHO 'OKE!!!';
	                foreach($last_q as $key=>$value)
			{
				    $quest_data=take_quest_data($key,$value[step]);
				    system_finish_step($quest_data);
			}
		            }
	            echo '<font color=red><b>Регистраци завершена</b></font>';
	            echo mysql_error();
		}
		elseif($err!='')
		{			echo '<font color=red><b>'.$err.'</b></font>';		}
       // if()
	}

	function quest_check_type_2($last_q) //функция проверяет наличие вещи в инвентаре для квестов типа 2
	{
		$user = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;"));
		foreach($last_q as $key=>$value)
		{
			$quest_data=take_quest_data($key,$value[step]);
			if($quest_data[q_item]==1)
			{  //перечисляем ID вещей, которые должны быть в инвентаре в поле info                 $data=mysql_fetch_array(mysql_query('select * from oldbk.inventory where owner = '.$_SESSION[uid].' and prototype in ('.$quest_data[info].') limit 1;'));
			//print_r($data);
				$data=mysql_fetch_array(mysql_query('select * from oldbk.inventory where owner = '.$_SESSION[uid].' and prototype in ('.$quest_data[info].') limit 1;'));
				if($data[id])
				{
					system_finish_step($quest_data);
				}
			
			}
			elseif($quest_data[q_item]==2)//для нуб квеста, ремонт щитка
			{   //конкретно для нуб квеста
				$data=mysql_fetch_array(mysql_query('select * from oldbk.inventory where owner = '.$_SESSION[uid].' and prototype = 1105 AND otdel=3 limit 1;'));
				if($data[duration]==0)
				{
					system_finish_step($quest_data);
				}
			}
			elseif($quest_data[q_item]>10)
			{   //тут будут остальные собиралки(квест итемы)
			// echo 'проверяем наличие и кол-во в инвентаре';
			}
		}
	}

	function quest_check_type_9($last_q,$lab=0) //вызывается напрямую
	{
		$user = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;"));
		foreach($last_q as $key=>$value)
		{
		    $quest_data=take_quest_data($key,$value[step]);

		    if($quest_data[id]=='1' && $quest_data[step]=='31' && $user[room]=='34' && $lab==0) //квест 1. подарок ангелу
		    {
		    	system_finish_step($quest_data);
		    }
            		else
		    if($quest_data[id]=='2' && $quest_data[step]=='3' && $user[lab]==0 && $lab==0)   //. Квес 2. Уровень 3. Проверка комплекта
		    {
		    	system_finish_step($quest_data);
		    }
           /* else
			if($quest_data[id]=='3' && $quest_data[step]=='6' && $user[lab]>0 && $lab==1)  //. Квес 3. выход из лабы
		    {
		    	system_finish_step($quest_data);
		    }*/
		}
	}
	
	function quest_check_type_91($last_q) //выход из лабы
	{
		$user = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;"));
		foreach($last_q as $key=>$value)
		{
		    $quest_data=take_quest_data($key,$value[step]);
			if($quest_data[id]=='3' && $quest_data[step]=='6' && $user[lab]>0)  //. Квес 3. выход из лабы
		    {
		    	system_finish_step($quest_data);
		    }
		}
	}

    function quest_check_type_10($last_q,$user_id,$session='ON')
    //спецтип  - бой с исчадием. любой исход боя.
    {        //print_r($last_q);
        $quest_data=take_quest_data($last_q[quest_id],$last_q[step]);
        system_finish_step($quest_data,$user_id,$session);    }

    function quest_check_type_11($last_q,$user_id,$session='ON') //таже хрень что и тип 10..  срабатывает при вызове.
    {
	
	foreach($last_q as $key=>$value)
		{
			$quest_data=take_quest_data($key,$value[step]);
			system_finish_step($quest_data);
		}
    }
    
    function quest_check_type_12($last_q,$user_id,$session='ON')
   //спецтип  - первый бой на арене.
    {
        //print_r($last_q);
        $quest_data=take_quest_data($last_q[quest_id],$last_q[step]);
        system_finish_step($quest_data,$user_id,$session);
    }

    function quest_check_type_20($last_q,$user_id,$session='ON',$ftype=0,$pluscount=1,$call=0)
   //квесты на накопилки в боях
    {
           foreach($last_q as $key=>$value)
           {
               if($value[qftype]==$ftype)
               {
               		//все сессионные вызываются из ласт скрина, кроме БС. там вызывает Арх. Отсюда фикс на сессию

	               	 $quest_data=take_quest_data($key,$value[step]);
			if($key==107)
			{
				//addchp('<font color=red>1 Внимание!</font> ключ:'.$key.' юзер'.$user[id].' взял БС id квеста'.$luquest[quest_id].' сессия:'.$session.' вызов'.$call,'{[]}A-Tech{[]}',-1,0);
			}
			
			if($ftype==6)
			{
				//addchp('<font color=red>1.1 Внимание!</font> ftype:'.$ftype.' юзер'.$user_id.' взял  id квеста'.$quest_data[id].' сессия:'.$session.' вызов'.$call,'{[]}Bred{[]}',-1,0);
			}

			if($ftype==71)
			{
				//addchp('<font color=red>71.0 Внимание!</font> ftype:'.$ftype.' юзер'.$user_id.' взял  id квеста'.$quest_data[id].' сессия:'.$session.' вызов'.$call,'{[]}Bred{[]}',-1,0);
			}			
			
	                if(($_SESSION['beginer_quest'][$key]['count'])<$quest_data['qfin'] && $session=='ON')
		           {
	                        mysql_query('UPDATE oldbk.beginers_quests_step SET count=count+'.$pluscount.'
	                       					WHERE quest_id='.$key.' AND step='.$value['step'].'
	                       					AND step_f=1 AND owner='.$user_id.';');
				if (mysql_affected_rows()>0)
	                        {	                       	    $_SESSION['beginer_quest'][$key]['count']+=$pluscount;
	                  			if($ftype==6)
	                  			{
						//addchp('<font color=red>1.2 Внимание!</font> ftype:'.$ftype.' юзер'.$user_id.' взял  id квеста'.$quest_data[id].' сессия:'.$session.' вызов'.$call,'{[]}Bred{[]}',-1,0);
						}	                        }
	                        else
	                        {

	                  			if($ftype==6)
	                  			{
						//addchp('<font color=red>1.3 Внимание!</font> ftype:'.$ftype.' юзер'.$user_id.' взял  id квеста'.$quest_data[id].' сессия:'.$session.' вызов'.$call,'{[]}Bred{[]}',-1,0);
						}	                        
	                        
	                        }

	                       	if(($_SESSION['beginer_quest'][$key]['count'])>=$quest_data['qfin'])
				{
					system_finish_step($quest_data,$user_id,$session);
				}
	                    }
	                    elseif(($_SESSION['beginer_quest'][$key]['count'])>=$quest_data['qfin']  && $session=='ON')
	                    {
	                    	system_finish_step($quest_data,$user_id,$session);	                    }
	                    elseif($session=='OFF' && ($ftype==100 OR $ftype==71)  ) //это +1 победа в БС или бля ристлке одиночки
	                    {	                            if($value['count']<$quest_data['qfin'])
	                            {	                                mysql_query('UPDATE oldbk.beginers_quests_step SET count=count+'.$pluscount.'
		                       					WHERE quest_id='.$key.' AND step='.$value['step'].'
		                       					AND step_f=1 AND owner='.$user_id.';');
		                       					
						if (mysql_affected_rows()>0)
							{
		                  				if($ftype==71)
		                  				{
								//addchp('<font color=red>71.1 Внимание!</font> ftype:'.$ftype.' юзер'.$user_id.' взял  id квеста'.$quest_data[id].' сессия:'.$session.' вызов'.$call,'{[]}Bred{[]}',-1,0);
								}								
							}		                       						                            }
				    //addchp('<font color=red>2 Внимание!</font> ftype:'.$ftype.' юзер'.$user[id].' взял БС id квеста'.$luquest[quest_id].' сессия:'.$session.' вызов'.$call,'{[]}A-Tech{[]}',-1,0);
			
	                            if(($value['count']+1)>=$quest_data['qfin'])
	                            {	                            	system_finish_step($quest_data,$user_id,$session);
	                            	
		                  				if($ftype==71)
		                  				{
								//addchp('<font color=red>71.2 Внимание!</font> ftype:'.$ftype.' юзер'.$user_id.' взял  id квеста'.$quest_data[id].' сессия:'.$session.' вызов'.$call,'{[]}Bred{[]}',-1,0);
								}		                            	
	                            		                            }	                    }
	                    else
	                    {	                       // addchp('<font color=red>Внимание!</font> Ошибка боевого квеста '.$key.' на пользователе '.$user_id.'.','{[]}Bred{[]}',-1,0);

	                   	                    }

	           }           }
    }

  function quest_check_type_30($last_q,$user_id,$q_sub_type,$pluscount=1)
   //квесты на накопилки
    {
        //  94SrtR9p
           foreach($last_q as $key=>$value)
           {
               if($value[qftype]==$q_sub_type)
               {
	          
	               	$quest_data=take_quest_data($key,$value[step]);

	                if(($_SESSION['beginer_quest'][$key]['count'])<$quest_data['qfin'])
			{
				if(mysql_query('UPDATE oldbk.beginers_quests_step SET count=count+'.$pluscount.'
							WHERE quest_id='.$key.' AND step='.$value['step'].'
							AND step_f=1 AND owner='.$user_id.';'))
				{
					$_SESSION['beginer_quest'][$key]['count']+=$pluscount;
				}
				
				if(($_SESSION['beginer_quest'][$key]['count'])==$quest_data['qfin'])
				{
					system_finish_step($quest_data,$user_id,'ON');
				}
	                }
	                elseif(($_SESSION['beginer_quest'][$key]['count'])==$quest_data['qfin'] )
	                {
	                	system_finish_step($quest_data,$user_id,'ON');
	                }
	                    
	                else
	                {
	                        addchp('<font color=red>Внимание!</font> Ошибка боевого квеста '.$key.' на пользователе '.$user_id.'.','{[]}A-Tech{[]}',-1,0);   
	                }

	           }
           }
    }

?>