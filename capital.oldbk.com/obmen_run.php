<?
session_start();
include "connect.php";
include "functions.php";

include_once ROOT_DIR.'/components/Component/Security/check_2fa.php';
function show_admin_runs($telo)
{
$get_items=mysql_query("select * from oldbk.inventory where owner='{$telo[id]}' and sowner!='{$telo[id]}' and type=30 and up_level>4 and  dressed=0 ORDER BY `update` DESC");
   if(mysql_num_rows($get_items)>0)
	{
	while ($result = mysql_fetch_assoc($get_items))
			{
			$result[count]=1;
			if ($i==0) { $i = 1; $color = '#C7C7C7';} else { $i = 0; $color = '#D5D5D5';}
			echo "<TR bgcolor={$color}><TD align=center style='width:150px'><IMG SRC=\"http://i.oldbk.com/i/sh/{$result['img']}\" BORDER=0>";
			echo "<br><small>(".get_item_fid($result).")</small>";
			echo "</TD>";
			echo "<TD valign=top width='400'>";
			echo "<table> <div align=center><b>Старая руна</b></div><br>";
			showitem ($result);
			echo "</table>";
			echo "</TD>";
			echo "<TD valign=top width='400'>  <div align=center><b>Создать новую</b></div><br>";
			show_new_run($result);
			echo "</TD>";						
			echo "</TR>";
			}
	}
	else
	{
	echo "<br>НЕТ РУН НА ОБМЕН<br>";
	}

}

function new_run_lvl($nl)
{
	if ($nl>9) { return 10; }
	elseif ($nl>4) { return 5; }
	else return false;
}


function show_new_run($old)
{

			echo "<form method=post>";
			echo '
			<select name="newruns" >
			<option value=0 selected >------------------------------------------------------------</option>';
			$get_r=mysql_query("select * from cshop where type=30 order by id");
			while ($r = mysql_fetch_assoc($get_r))
				{
				echo "<option value='{$r[id]}'>{$r[name]}</option>";
				}
			echo '</select><br>';
			echo "Уровень:".new_run_lvl($old[up_level]) ;
			echo "<br><input type=hidden name=oldid value='".$old[id]."'>";			
			echo "<input type=submit name=mkruns value='создать и передать' >";			
			echo "</form>";
}


function mk_new_runs($proto,$newlvl,$ownerid,$chto)
{
global $runs_exp_table, $runs_5lvl_param,$user;

$dress=mysql_fetch_assoc(mysql_query("select * from oldbk.cshop  where id='{$proto}' and type=30 ;"));

if (  (($newlvl==5) OR ($newlvl==10)) AND ($dress[id]>0))
	{
		
			
		for ($i=1;$i<=$newlvl;$i++)
		{
		$add_cost+=30;
		//готовим нужные статы и параметры 
			$pre=$runs_exp_table[$i];
			foreach($pre as $k=>$v)
				{
				$new_data[$k]+=$v;
				}

		}
					$dress['cost']+=$add_cost;
					$dress['ghp']+=$new_data[ghp];
					$dress['bron1']+=$new_data[bron];
					$dress['bron2']+=$new_data[bron];
					$dress['bron3']+=$new_data[bron];
					$dress['bron4']+=$new_data[bron];
					$dress['gfire']+=$new_data[smast];
					$dress['gwater']+=$new_data[smast];
					$dress['gair']+=$new_data[smast];
					$dress['gearth']+=$new_data[smast];
					$dress['gmp']+=$new_data[gmp];
					$dress['gintel']+=$new_data[gintel];
					$dress['minu']+=$new_data[minu];
					$dress['maxu']+=$new_data[maxu];
					$dress['stbonus']+=$new_data[stbonus];
					$dress['mfbonus']+=$new_data[mfbonus];
					$dress['add_time']=$pre[next];
					$dress['ups']=($runs_exp_table[($newlvl-1)][next]+1);
					$dress['up_level']=$newlvl;
					$arr_add_param=$runs_5lvl_param[$proto];
					$dress['ab_mf']=$arr_add_param[ab_mf];
					$dress['ab_bron']=$arr_add_param[ab_bron];
					$dress['ab_uron']=$arr_add_param[ab_uron];

			//удаляем старую
			mysql_query("DELETE FROM oldbk.`inventory` where id='{$chto[id]}' LIMIT 1;");		
			if(mysql_affected_rows()>0)		
					{
				//создаем новую сразу персу
				mysql_query("INSERT INTO oldbk.`inventory`
						(`prototype`,`owner`,`name`,`type`,`massa`,`cost`, `repcost`, `img`,`maxdur`,`isrep`,
							`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,
							`ggray`,`gdark`,`needident`,`nsila`,`nlovk`,`ninta`,`nintel`,`nmudra`,`nvinos`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nfire`,
							`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`bron1`,
							`bron2`,`bron3`,`bron4`,`maxu`,`minu`,`magic`,`nlevel`,`nalign`,`otdel`,`group` ,`gmp`,`gmeshok`,`ecost`,`mfbonus`,`sowner`,`up_level`,`idcity`,`ab_mf`,`ab_bron`,`ab_uron` ,`add_time` , `present` ,`stbonus`, `ups`
						)
						VALUES
						('{$dress['id']}','{$ownerid}','{$dress['name']}','{$dress['type']}',{$dress['massa']},{$dress['cost']}, {$dress['repcost']},
						'{$dress['img']}',{$dress['maxdur']},{$dress['isrep']},'{$dress['gsila']}','{$dress['glovk']}','{$dress['ginta']}','{$dress['gintel']}',
						'{$dress['ghp']}','{$dress['gnoj']}','{$dress['gtopor']}','{$dress['gdubina']}','{$dress['gmech']}','{$dress['gfire']}','{$dress['gwater']}',
						'{$dress['gair']}','{$dress['gearth']}','{$dress['glight']}','{$dress['ggray']}','{$dress['gdark']}','{$dress['needident']}','{$dress['nsila']}',
						'{$dress['nlovk']}','{$dress['ninta']}','{$dress['nintel']}','{$dress['nmudra']}','{$dress['nvinos']}','{$dress['nnoj']}','{$dress['ntopor']}',
						'{$dress['ndubina']}','{$dress['nmech']}','{$dress['nfire']}','{$dress['nwater']}','{$dress['nair']}','{$dress['nearth']}','{$dress['nlight']}',
						'{$dress['ngray']}','{$dress['ndark']}', '{$dress['mfkrit']}','{$dress['mfakrit']}','{$dress['mfuvorot']}','{$dress['mfauvorot']}',
						'{$dress['bron1']}','{$dress['bron2']}','{$dress['bron3']}','{$dress['bron4']}','{$dress['maxu']}','{$dress['minu']}','{$dress['magic']}',
						'{$dress['nlevel']}','{$dress['nalign']}','{$dress['razdel']}','{$dress['group']}' ,
						'{$dress['gmp']}','{$dress['gmeshok']}','{$dress['ecost']}','{$dress['mfbonus']}','{$ownerid}','{$dress['up_level']}','0','{$dress['ab_mf']}',
						'{$dress['ab_bron']}','{$dress['ab_uron']}', '{$dress['add_time']}' , 'Мироздатель' , '{$dress['stbonus']}', '{$dress['ups']}') ");
						
				$dress['id']=mysql_insert_id();
				
				
				//пишем лог
				mysql_query("INSERT INTO `oldbk`.`obmen_run` SET `komu`='{$ownerid}',`kto`='{$user[id]}',`chto`='".get_item_fid($chto).":".$chto[name].":".$chto[up_level].":".$chto[ups]."',`nachto`='cap".$dress['id'].":".$dress['name'].":".$dress['up_level'].":".$dress['ups']."';");
				
				//пишем в дело
					$telo=check_users_city_data($ownerid);
				
		       	                //new_delo
  		    			$rec['owner']=$telo[id];
					$rec['owner_login']=$telo[login];
					$rec['owner_balans_do']=$telo['money'];
					$rec['owner_balans_posle']=$telo['money'];
					$rec['target']=$user[id];
					$rec['target_login']=$user[login];
					$rec['type']=314; //?????
					$rec['sum_kr']=$dress[cost];
					$rec['sum_ekr']=0;
					$rec['sum_kom']=0;//комиссия
					$rec['sum_rep']=$dress[repcost];
					$rec['item_id']=get_item_fid($dress);
					$rec['item_name']=$dress[name];
					$rec['item_count']=1;
					$rec['item_type']=$dress['type'];
					$rec['item_cost']=$dress['cost'];
					$rec['item_dur']=$dress['duration'];
					$rec['item_maxdur']=$dress['maxdur'];
					$rec['item_ups']=$dress[ups];
					$rec['item_unic']=0;
					$rec['item_incmagic']='';
					$rec['item_incmagic_count']='';
					$rec['item_arsenal']='';
					$rec['bank_id']=0;
					$rec['add_info']='Обмен руны:'.get_item_fid($chto).':'.$chto[name].':'.$chto[up_level].':'.$chto[ups];
					add_to_new_delo($rec); //юзеру

		       	                //new_delo
  		    			$rec['owner']=$user[id];
					$rec['owner_login']=$user[login];
					$rec['owner_balans_do']=$user['money'];
					$rec['owner_balans_posle']=$user['money'];
					$rec['target']=$telo[id];
					$rec['target_login']=$telo[login];
					$rec['type']=315; //?????
					$rec['sum_kr']=$dress[cost];
					$rec['sum_ekr']=0;
					$rec['sum_kom']=0;//комиссия
					$rec['sum_rep']=$dress[repcost];
					$rec['item_id']=get_item_fid($dress);
					$rec['item_name']=$dress[name];
					$rec['item_count']=1;
					$rec['item_type']=$dress['type'];
					$rec['item_cost']=$dress['cost'];
					$rec['item_dur']=$dress['duration'];
					$rec['item_maxdur']=$dress['maxdur'];
					$rec['item_ups']=$dress[ups];
					$rec['item_unic']=0;
					$rec['item_incmagic']='';
					$rec['item_incmagic_count']='';
					$rec['item_arsenal']='';
					$rec['bank_id']=0;
					$rec['add_info']='Обмен руны:'.get_item_fid($chto).':'.$chto[name].':'.$chto[up_level].':'.$chto[ups];
					add_to_new_delo($rec); //юзеру
				
					//пишем системку
					telepost_new($telo,'<font color=red>Внимание!</font> Вы получили <b>'.$dress[name].'</b>, в обмен на <b>'.$chto[name].'</b>.');	
					
					return true;
					}
					else
					{
					echo "ОШИБКА УДАЛЕНИЯ СТАРОЙ РУНЫ";
					return false;					
					}
		
	}
	else
	{
	return false;	
	}

}

if ($user[klan]=='Adminion' || $user[klan]=='radminion')
	{
?>
<HTML><HEAD><TITLE>Обмен Рун</TITLE>
<META http-equiv=Content-type content="text/html; charset=windows-1251">
<META http-equiv=Pragma content=no-cache>
<META http-equiv=Cache-control content=private>
<META http-equiv=Expires content=0>
<script type="text/javascript" src="/i/globaljs.js"></script>
<LINK href="i/main.css" type=text/css rel=stylesheet>
</HEAD>
<body>
<CENTER>
<h4> Обмен Рун </h4>
<?

	if (($_POST[mkruns]) AND ($_POST[newruns]>0) AND ($_POST[oldid]>0))
		{
		$test_item=mysql_fetch_assoc(mysql_query("select * from oldbk.inventory  where owner='{$user[id]}' and type=30 and sowner!='{$user[id]}' and id='{$_POST[oldid]}' and dressed=0"));
		if ($test_item[id]>0)
			{
				// создаем новыю нужного уровня и со статами+удаляем старую
				$newlvl=new_run_lvl($test_item[up_level]);
				if (mk_new_runs($_POST[newruns],$newlvl,$test_item[sowner],$test_item))
					{
					echo "Руна успешно доставлена!";
					}
					else
					{
					echo "Ошибка обмена руны!";
					}
					
			}
		}
	
		echo ' <TABLE BORDER=0 WIDTH=80% CELLSPACING="1" CELLPADDING="2" BGCOLOR="#A5A5A5">';
		show_admin_runs($user);
		echo "</table>";
	
	
	echo '</CENTER></body></HTML>';	
	}
	else
	{
	 die('Страница не найдена :)');
	}
?>