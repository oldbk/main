<?
session_start();
include "connect.php";
include "functions.php";
include_once ROOT_DIR.'/components/Component/Security/check_2fa.php';
if(!ADMIN)
{
	die('Страница не найдена...');
}

if($_POST[action]>0)
{


/*
action => 349
shop_id => 248
cap_shop_count => 172899
ava_shop_count => 194299
eshop_id => 5204
cap_eshop_count => 9997423
ava_eshop_count => 9999422
cshop_id => 1005204
cap_cshop_count => 14002
ava_cshop_count => 1443 

*/
		if(isset($_POST[shop_id]))
		{
			mysql_query("update oldbk.shop set `count`='".(int)$_POST[cap_shop_count]."',avacount='".(int)$_POST[ava_shop_count]."' WHERE id='".(int)$_POST[shop_id]."' ");
		}
		if(isset($_POST[eshop_id]))
		{
			mysql_query("update oldbk.eshop set `count`='".(int)$_POST[cap_eshop_count]."',avacount='".(int)$_POST[ava_eshop_count]."' WHERE id='".(int)$_POST[eshop_id]."' ");
		}
		if(isset($_POST[cshop_id]))
		{
			mysql_query("update oldbk.cshop set `count`='".(int)$_POST[cap_cshop_count]."',avacount='".(int)$_POST[ava_cshop_count]."' WHERE id='".(int)$_POST[cshop_id]."' ");
		}
		//echo $sql1.'<br>'.$sql2.'<br>'.$sql3;
	
}
else
{
	?>
	<HTML><HEAD>
	<link rel=stylesheet type="text/css" href="http://i.oldbk.com/i/main.css">
	<meta content="text/html; charset=windows-1251" http-equiv=Content-type>
	<META Http-Equiv=Cache-Control Content=no-cache>
	<meta http-equiv=PRAGMA content=NO-CACHE>
	<META Http-Equiv=Expires Content=0>
	<style>
	
		.green{
			background: #F0FFF0;
		}
		.red{
			background: #FFCC99;
		}
		.blue{
			background: #B0E0E6;
		}
		.nochange
		{
			background: #EBEBEB;
		}
		
	
	</style>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.1/jquery.min.js"></script>
	<script type="text/javascript">
	function changed(line_id)
	{	
		$('#'+line_id).removeClass( 'nochange' ).addClass( 'red' );
		$('#but_'+line_id).removeAttr('disabled');
	}
	
	function save_change(line_id)
	{
		var string='';
		var shop_id=$('#shop_id_'+line_id).val();
		var cap_shop_count = $('#cap_shop_'+line_id).val();	
		var ava_shop_count = $('#ava_shop_'+line_id).val();

		var shopbanner = $('#shopbanner_'+line_id).val();
		
		if(shop_id>0)
		{
			string='shop_id=' + shop_id+ '&cap_shop_count=' +cap_shop_count+ '&ava_shop_count=' +ava_shop_count+'&shopbanner='+shopbanner+'&';
		}
		var eshop_id=$('#eshop_id_'+line_id).val();
		var cap_eshop_count = $('#cap_eshop_'+line_id).val();
		var ava_eshop_count = $('#ava_eshop_'+line_id).val();
		
		var eshopbanner = $('#eshopbanner_'+line_id).val();
		
		if(eshop_id>0)
		{
			string=string+'eshop_id=' +eshop_id+ '&cap_eshop_count=' +cap_eshop_count+'&ava_eshop_count=' +ava_eshop_count+'&eshopbanner='+eshopbanner+'&';
		}
		
		var cshop_id=$('#cshop_id_'+line_id).val();
		var cap_cshop_count = $('#cap_cshop_'+line_id).val();
		var ava_cshop_count = $('#ava_cshop_'+line_id).val();
		
		var cshopbanner = $('#cshopbanner_'+line_id).val();		
		
		if(cshop_id>0)
		{
			string=string+'cshop_id=' +cshop_id+ '&cap_cshop_count=' +cap_cshop_count + '&ava_cshop_count=' +ava_cshop_count+'&cshopbanner='+cshopbanner+'&';
		}

			dataString='action='+line_id+'&'+string;
	             	$.ajax({
				      type: "POST",
				      url: "zavoz.php",
				      data: dataString,
				      success: function(ans) {
				      		$('#hidden').html( ans );
				      		if(ans)
				      		{
				      			$('#'+line_id).removeClass().addClass( 'green' );
				      			$('#but_'+line_id).attr('disabled',true);
				      			cap_shop_count = $('#cap_shop_'+line_id).val();	
							ava_shop_count = $('#ava_shop_'+line_id).val();
				      			
				      			
				      			if(cap_shop_count>0 && ava_shop_count>0)
				      			{
				      				$('#ts'+line_id).removeClass().addClass( 'green' );
				      			}
				      			elseif((cap_shop_count==0 && ava_shop_count>0) || (cap_shop_count>0 && ava_shop_count==0))
				      			{
				      				$('#ts'+line_id).removeClass().addClass( 'blue' );
				      			}
				      			elseif(cap_shop_count==0 || ava_shop_count==0)
				      			{
				      				$('#ts'+line_id).removeClass().addClass( 'red' );
				      			}
				      		}
				         	
						  return false;
		      			}
		   		  });
	
	}
	</script>
	</HEAD>
	
	<body leftmargin=20 topmargin=20 marginwidth=20 marginheight=20 >
	<div id=hidden></div>
	
	<table  width=100%><tr><td align=left>
	  <form action="?" METHOD=GET>
		<select name=otdel onChange="this.form.submit()">
		  <option><B>Отделы магазина</B></option>
		  <option value=1  <?=($_GET[otdel]==1?'selected':'')?> >&nbsp;Оружие: кастеты,ножи</option>
		  <option value=11 <?=($_GET[otdel]==11?'selected':'')?>>&nbsp;&nbsp;&nbsp;топоры</option>
		  <option value=12 <?=($_GET[otdel]==12?'selected':'')?>> &nbsp;&nbsp;&nbsp;дубины,булавы</option>
		  <option value=13 <?=($_GET[otdel]==13?'selected':'')?>>&nbsp;&nbsp;&nbsp;мечи</option>
		  
		  <option value=2 <?=($_GET[otdel]==2?'selected':'')?>>&nbsp;Одежда: сапоги</option>
		  <option value=21 <?=($_GET[otdel]==21?'selected':'')?>>&nbsp;&nbsp;&nbsp;перчатки</option>
		  <option value=22 <?=($_GET[otdel]==22?'selected':'')?>>&nbsp;&nbsp;&nbsp;легкая броня</option>
		  <option value=23 <?=($_GET[otdel]==23?'selected':'')?>>&nbsp;&nbsp;&nbsp;тяжелая броня</option>
		  <option value=24 <?=($_GET[otdel]==24?'selected':'')?>>&nbsp;&nbsp;&nbsp;шлемы</option>
		  
		  <option value=3 <?=($_GET[otdel]==3?'selected':'')?>>&nbsp;Щиты</option>
		  
		  <option value=4 <?=($_GET[otdel]==4?'selected':'')?> >&nbsp;Ювелирные товары: серьги</option>
		  <option value=41 <?=($_GET[otdel]==41?'selected':'')?>>&nbsp;&nbsp;&nbsp;ожерелья</option>
		  <option value=42 <?=($_GET[otdel]==42?'selected':'')?>>&nbsp;&nbsp;&nbsp;кольца</option>
		  
		  <option value=5 <?=($_GET[otdel]==5?'selected':'')?>>&nbsp;Заклинания: нейтральные</option>
		  <option value=51 <?=($_GET[otdel]==51?'selected':'')?>>&nbsp;&nbsp;&nbsp;боевые и защитные</option>
		  <option value=52 <?=($_GET[otdel]==52?'selected':'')?>>&nbsp;&nbsp;&nbsp;сервисные</option>
		
		  <option value=6 <?=($_GET[otdel]==6?'selected':'')?>>&nbsp;Амуниция</option>
		  <option value=61 <?=($_GET[otdel]==61?'selected':'')?>>&nbsp;&nbsp;&nbsp;Еда</option> 
		  <option value=60 <?=($_GET[otdel]==60?'selected':'')?>>&nbsp;&nbsp;&nbsp;Молитвенные предметы</option>
		  <option value=62 <?=($_GET[otdel]==62?'selected':'')?>>&nbsp;Производство: Ресурсы</option>		  
		  <option value=63 <?=($_GET[otdel]==63?'selected':'')?>>&nbsp;&nbsp;&nbsp;Инструменты</option>		  		  
		   
		</select>
	</form>
	</td><td align=right>	
	<form action="?" METHOD=GET>
	 	<table>
		<td>Кол-во</td><td align=center> Гос(кеп)|Гос(Ава) </td><td> Ешоп </td><td> цшоп </td><td> комок </td><td> везде </td><td></td></tr>
		<tr><td><input type=text name=count onkeyup="this.value=this.value.replace(/\D/, '')" value=<?=($_GET['count']?$_GET['count']:1000)?>></td>
		<td align=center><input type=checkbox name=shopc value=1 <?=(isset($_GET[shopc])?'checked':'')?>>|<input type=checkbox name=shopa value=1 <?=(isset($_GET[shopa])?'checked':'')?>></td>
		<td><input type=checkbox name=eshop value=1 <?=(isset($_GET[eshop])?'checked':'')?>></td>
		<td><input type=checkbox name=cshop value=1 <?=(isset($_GET[cshop])?'checked':'')?>></td>
		<td><input type=checkbox name=comok value=1 <?=(isset($_GET[comok])?'checked':'')?>></td>
		<td><input type=checkbox name=all value=1 <?=(isset($_GET[all])?'checked':'')?>></td>
		<td><input type=submit value=искать></td></tr>	
		</table>
	</form>
	</td></tr></table>
	<hr>
	
	<?
	
	{
		//print_r($_GET);
	}
	

	if($_GET[otdel] || (int)$_GET['count']>0)
	{
		?>
		
		<TABLE BORDER=0 CELLSPACING="4" CELLPADDING="2" BGCOLOR="#A5A5A5">
		<?
			if($_GET[otdel]==5 || $_GET[otdel]==52 || $_GET[otdel]==51)
			{
				$sql=' sname';
			}
			else
			{
				$sql=' scost, ecost';
			}
			
			$items=array();
			$ids=array();
			if($_GET[otdel])
			{
				$data=mysql_query('
				select z.*,s.cost as scost,e.cost as ecost, s.name as sname from oldbk.zavoz z
					left join shop s
					on s.id=z.item_id
					left join eshop e
					on e.id=z.item_id
					where z.razdel="'.(int)$_GET[otdel].'"
					order by '.$sql);
			}
			else
			if($_GET['count'])
			{
				$order='scount';
				
				if(isset($_GET[shopc]))
				{
					$shopc_sql_1=' s.`count` as scount , ';
					$shopc_sql_2=' left join shop s on s.id=z.shop ';
					$shopc_where_sql=' ((s.`count`<'.(int)$_GET['count'].' AND s.`count` is not null) AND z.shop>0) OR';
					$sorderc='scount DESC,';
				}
				if(isset($_GET[shopa]))
				{
					$shopa_sql_1=' s.`avacount` as savacount, ';
					$shopa_sql_2=' left join shop s on s.id=z.shop ';
					$shopa_where_sql=' (( s.`avacount`<'.(int)$_GET['count'].' AND s.`avacount` is not null) AND z.shop>0) OR';
					$sordera='savacount DESC,';
				}
				if(isset($_GET[eshop]))
				{
					$eshop_sql_1=' e.`count` as ecount ,e.`avacount` as eavacount, ';
					$eshop_sql_2=' left join eshop e on e.id=z.eshop ';
					$eshop_where_sql=' ((e.`count`<'.(int)$_GET['count'].' AND e.`count` is not null OR e.`avacount`<'.(int)$_GET['count'].' AND e.`avacount` is not null) AND z.eshop>0) OR';
					$eorder='ecount DESC,eavacount DESC,';
				}
				if(isset($_GET[cshop]))
				{
					$cshop_sql_1=' c.`count` as ccount ,c.`avacount` as cavacount, ';
					$cshop_sql_2=' left join cshop c on c.id=z.cshop ';
					$cshop_where_sql=' ((c.`count`<'.(int)$_GET['count'].' AND c.`count` is not null OR c.`avacount`<'.(int)$_GET['count'].' AND c.`avacount` is not null) AND z.cshop>0) OR';
					$corder='ccount DESC,cavacount DESC,';
				}
				if($shopc_where_sql!='' || $shopa_where_sql || $eshop_where_sql !='' || $cshop_where_sql!='')
				{
					
					$where= ' WHERE '.$shopc_where_sql.$shopa_where_sql.$eshop_where_sql.$cshop_where_sql;
					$where=substr($where,0,-2);
				}
				$sql1=substr($shopc_sql_1.$shopa_sql_1.$eshop_sql_1.$cshop_sql_1,0,-2);
				$sql_order=substr($sorderc.$sordera.$eorder.$corder,0,-1);
				
				
				$sql='select z.*,'.$sql1.'   
				from oldbk.zavoz z 
				'.$shopc_sql_2.$shopa_sql_2.$eshop_sql_2.$cshop_sql_2.$where.' order by '.$sql_order;
				echo $sql;
				$data=mysql_query($sql);
				
			}
							
			while($row=mysql_fetch_assoc($data))
			{
				$items[$row[id]]=$row;
				
				$ids[$row[item_id]]=$row[item_id];
				$shop_ids[$row[shop]]=$row[shop];
				$eshop_ids[$row[eshop]]=$row[eshop];
				$cshop_ids[$row[cshop]]=$row[cshop];
			}
			
			//echo mysql_error();
			//проверяем гос магаизн
			$shop=array();
			$data=mysql_query("SELECT * FROM oldbk.shop where id in (".implode(',',$shop_ids).")");
			if(mysql_num_rows($data)>0)
			{
				while($row=mysql_fetch_assoc($data))
				{
					$shop[$row[id]]=$row;		
				}
			}
			//проверяем березу
			$eshop=array();
			$data=mysql_query("SELECT * FROM oldbk.eshop where id in (".implode(',',$eshop_ids).")");
			if(mysql_num_rows($data)>0)
			{
				while($row=mysql_fetch_assoc($data))
				{
					$eshop[$row[id]]=$row;		
				}
			}
			
			
			//проверяем храмовую
			$cshop=array();
			$data=mysql_query("SELECT * FROM oldbk.cshop where id in (".implode(',',$cshop_ids).")");
			if(mysql_num_rows($data)>0)
			{
				while($row=mysql_fetch_assoc($data))
				{
					$cshop[$row[id]]=$row;		
				}
			}
			
			$comshop=array();
			$com_id=($shop_ids?implode(',',$shop_ids):'').($eshop_ids?','.implode(',',$eshop_ids):'').($cshop_ids?implode(',',$cshop_ids):'');
			$data=mysql_query("SELECT prototype, id_city, count(`id`) as `count`, min(cost) as min_setsale, max(cost) as max_setsale
						FROM oldbk.`comission_indexes` 
						WHERE  prototype in (".$com_id.")
						group by prototype,id_city");
			if(mysql_num_rows($data)>0)
			{
				while($row=mysql_fetch_assoc($data))
				{
					$comshop[$row[id_city]][$row[prototype]]=$row;		
				}
			}
			//print_r($_GET);
			echo '<tr  BGCOLOR="#e0e0e0" align=center><td ><b> &nbsp;&nbsp;Название вещи &nbsp;&nbsp;</b></td><td width=150><b> &nbsp;&nbsp;Гос. магазин&nbsp;&nbsp; </b></td><td width=150><b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Березка&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </b></td><td width=150><b> &nbsp;&nbsp;Храм. лавка&nbsp;&nbsp; </b></td><td width=250><b> &nbsp;&nbsp;Комиссионный магазин&nbsp;&nbsp; </b></td><td><b>Сохранить</b></td></tr>';
			foreach($items as $zv_id => $zv_sh_data)
			{
				echo '<tr bgcolor="#EBEBEB" >';
					//данные о шмотке
					echo '<td  class="nochange"><div id="'.$zv_id.'">';	//&& $shop[$zv_sh_data[shop]][razdel]==$_GET[otdel]
						if($shop[$zv_sh_data[shop]] )
						{
							// нужно для верного отражения шмоток.
							echo '<table><tr><td>из shop<br>';
							$bcb_cap_count=$shop[$zv_sh_data[shop]]['count'];
							$bcb_ava_count=$shop[$zv_sh_data[shop]]['avacount'];
							$shop[$zv_sh_data[shop]]['count']=1;
							$shop[$zv_sh_data[shop]]['avacount']=1;
							echo "&nbsp;&nbsp;&nbsp;&nbsp;<IMG SRC=http://i.oldbk.com/i/sh/".$shop[$zv_sh_data[shop]]['img']." BORDER=0>&nbsp;&nbsp;&nbsp;&nbsp;";
							echo '</td><td>';
							echo 'ID прототипа: '.$zv_sh_data[shop].'<br>';
							showitem($shop[$zv_sh_data[shop]]);
							$shop[$zv_sh_data[shop]]['count']=$bcb_cap_count;
							$shop[$zv_sh_data[shop]]['avacount']=$bcb_ava_count;
							echo '</td></tr></table>';
						}
						else//&& $eshop[$zv_sh_data[eshop]][razdel]==$_GET[otdel]
						if($eshop[$zv_sh_data[eshop]] )
						{
							// нужно для верного отражения шмоток.
							echo '<table><tr><td>из eshop<br>';
							$bcb_cap_count=$eshop[$zv_sh_data[eshop]]['count'];
							$bcb_ava_count=$eshop[$zv_sh_data[eshop]]['avacount'];
							$eshop[$zv_sh_data[eshop]]['count']=1;
							$eshop[$zv_sh_data[eshop]]['avacount']=1;
							echo "&nbsp;&nbsp;&nbsp;&nbsp;<IMG SRC=http://i.oldbk.com/i/sh/".$eshop[$zv_sh_data[eshop]]['img']." BORDER=0>&nbsp;&nbsp;&nbsp;&nbsp;";
							echo '</td><td>';
							echo 'ID прототипа: '.$zv_sh_data[eshop].'<br>';
							showitem($eshop[$zv_sh_data[eshop]]);
							$eshop[$zv_sh_data[eshop]]['count']=$bcb_cap_count;
							$eshop[$zv_sh_data[eshop]]['avacount']=$bcb_ava_count;
							echo '</td></tr></table>';
						}
						else //&& $cshop[$zv_sh_data[cshop]][razdel]==$_GET[otdel]
						if($cshop[$zv_sh_data[cshop]] )
						{
						// нужно для верного отражения шмоток.
							echo '<table><tr><td>из cshop<br>';
							$bcb_cap_count=$cshop[$zv_sh_data[cshop]]['count'];
							$bcb_ava_count=$cshop[$zv_sh_data[cshop]]['avacount'];
							$cshop[$zv_sh_data[cshop]]['count']=1;
							$cshop[$zv_sh_data[cshop]]['avacount']=1;
							echo "&nbsp;&nbsp;&nbsp;&nbsp;<IMG SRC=http://i.oldbk.com/i/sh/".$cshop[$zv_sh_data[cshop]]['img']." BORDER=0>&nbsp;&nbsp;&nbsp;&nbsp;";
							echo '</td><td>';
							echo 'ID прототипа: '.$zv_sh_data[cshop].'<br>';
							showitem($cshop[$zv_sh_data[cshop]]);
							$cshop[$zv_sh_data[cshop]]['count']=$bcb_cap_count;
							$cshop[$zv_sh_data[cshop]]['avacount']=$bcb_ava_count;
							echo '</td></tr></table>';
						}
						else
						{
							echo '<table><tr><td>-4-';
							echo 'ID прототипа: '.$zv_sh_data[cshop].'<br>';
							echo '<font color=red><b>этот прототип НЕ НАЙДЕН в магазинах (раздел '.$_GET[otdel].')!!! (но есть в библиотеке)</b></font>';
							echo '</td></tr></table>';
						}
				
						if(!$zv_sh_data[shop])
						{
						    $color='class="nochange"';
						}	
						else	
						if($shop[$zv_sh_data[shop]]['count']==0 && $shop[$zv_sh_data[shop]]['avacount']==0)
						{
							$color='class="red"';
						}
						else
						if($shop[$zv_sh_data[shop]]['count']==0 && $shop[$zv_sh_data[shop]]['avacount']>0 || $shop[$zv_sh_data[shop]]['count']>0 && $shop[$zv_sh_data[shop]]['avacount']==0)	
						{
							$color='class="blue"';
						}
						else
						if($shop[$zv_sh_data[shop]]['count']>0 && $shop[$zv_sh_data[shop]]['avacount']>0)	
						{
							$color='class="green"';
						}
						else
						{
							$color='nochange';
						}
				echo '</td></div>
				
				<td><div align=center id="ts'.$zv_id.'" '.$color.' >';
					
					if($zv_sh_data[shop]>0)
					{
						echo 'id: '.$zv_sh_data[shop].'<br>';
						echo '<input type=hidden id="shop_id_'.$zv_id.'" value='.$zv_sh_data[shop].'>';
						echo 'Кэпитал: <input onkeyup="this.value=this.value.replace(/\D/, \'\')" onblur="changed('.$zv_id.')" id="cap_shop_'.$zv_id.'" size=8 type=text value='.$shop[$zv_sh_data[shop]]['count'].'><br>';
						echo 'Авалон&nbsp;: <input onkeyup="this.value=this.value.replace(/\D/, \'\')" onblur="changed('.$zv_id.')" id="ava_shop_'.$zv_id.'" size=8 type=text value='.$shop[$zv_sh_data[shop]]['avacount'].'><br>';	
						
						echo 'Баннер&nbsp;:<select name="shopbanner" id="shopbanner_'.$zv_id.'" onChange="changed('.$zv_id.')"><option value="0" '.($shop[$zv_sh_data[shop]]['shopbanner']==0?'selected':'').'>нет</option><option  value="1" '.($shop[$zv_sh_data[shop]]['shopbanner']==1?'selected':'').' >Новинка!</option><option  value="2" '.($shop[$zv_sh_data[shop]]['shopbanner']==2?'selected':'').'>Акция!</option></select><br>';						
						
					}
					else
					{
						echo '<img src=http://i.oldbk.com/i/clear.gif>';
					}	
				
					if(!$zv_sh_data[eshop])
					{
					  $color='class="nochange"';
					}	
					else	
					if($eshop[$zv_sh_data[eshop]]['count']==0 && $eshop[$zv_sh_data[eshop]]['avacount']==0)
					{
						$color='class="red"';
					}
					else
					if($eshop[$zv_sh_data[eshop]]['count']==0 && $eshop[$zv_sh_data[eshop]]['avacount']>0 || $eshop[$zv_sh_data[shop]]['count']>0 && $eshop[$zv_sh_data[shop]]['avacount']==0)	
					{
						$color='class="blue"';
					}
					else
					if($eshop[$zv_sh_data[eshop]]['count']>0 && $eshop[$zv_sh_data[eshop]]['avacount']>0)	
					{
						$color='class="green"';
					}
					else
					{
						$color='class="nochange"';
					}	
				echo '</div></td>
				<td><div align=center id="te'.$zv_id.'" '.$color.' >';
					
					if($zv_sh_data[eshop]>0)
					{
						echo 'id: '.$zv_sh_data[eshop].'<br>';
						echo '<input type=hidden id="eshop_id_'.$zv_id.'" value='.$zv_sh_data[eshop].'>';
						echo 'Кэпитал: <input onkeyup="this.value=this.value.replace (/\D/, \'\')" onblur="changed('.$zv_id.')" id="cap_eshop_'.$zv_id.'" size=8 type=text value='.$eshop[$zv_sh_data[eshop]]['count'].'><br>';
						echo 'Авалон&nbsp;: <input onkeyup="this.value=this.value.replace (/\D/, \'\')" onblur="changed('.$zv_id.')" id="ava_eshop_'.$zv_id.'"  size=8 type=text value='.$eshop[$zv_sh_data[eshop]]['avacount'].'><br>';
						echo 'Баннер&nbsp;:<select name="eshopbanner" id="eshopbanner_'.$zv_id.'" onChange="changed('.$zv_id.')"><option value="0" '.($shop[$zv_sh_data[eshop]]['shopbanner']==0?'selected':'').'>нет</option><option  value="1" '.($shop[$zv_sh_data[eshop]]['shopbanner']==1?'selected':'').' >Новинка!</option><option  value="2" '.($shop[$zv_sh_data[eshop]]['shopbanner']==2?'selected':'').'>Акция!</option></select><br>';						
					}
					else
					{
						echo '<img src=http://i.oldbk.com/i/clear.gif>';
					}
					if(!$zv_sh_data[cshop])
					{
					    $color='class="nochange"';
					}	
					else	
					if($cshop[$zv_sh_data[cshop]]['count']==0 && $cshop[$zv_sh_data[cshop]]['avacount']==0)
					{
						$color='class="red"';
					}
					else
					if($cshop[$zv_sh_data[cshop]]['count']==0 && $cshop[$zv_sh_data[cshop]]['avacount']>0 || $cshop[$zv_sh_data[shop]]['count']>0 && $cshop[$zv_sh_data[shop]]['avacount']==0)	
					{
						$color='class="blue"';
					}
					else
					if($cshop[$zv_sh_data[cshop]]['count']>0 && $cshop[$zv_sh_data[cshop]]['avacount']>0)	
					{
						$color='class="green"';
					}
					else
					{
						$color='class="nochange"';
					}	
				echo '</div></td><td><div align=center id="tc'.$zv_id.'" '.$color.' >';
					
					if($zv_sh_data[cshop]>0)
					{
						echo 'id: '.$zv_sh_data[cshop].'<br>';
						echo '<input type=hidden id="cshop_id_'.$zv_id.'" value='.$zv_sh_data[cshop].'>';
						echo 'Кэпитал: <input onkeyup="this.value=this.value.replace(/\D/, \'\')" onblur="changed('.$zv_id.')" id="cap_cshop_'.$zv_id.'"  size=8 type=text value='.$cshop[$zv_sh_data[cshop]]['count'].'><br>';
						echo 'Авалон&nbsp;: <input onkeyup="this.value=this.value.replace(/\D/, \'\')" onblur="changed('.$zv_id.')" id="ava_cshop_'.$zv_id.'"  size=8 type=text value='.$cshop[$zv_sh_data[cshop]]['avacount'].'><br>';	
						echo 'Баннер&nbsp;:<select name="cshopbanner" id="cshopbanner_'.$zv_id.'" onChange="changed('.$zv_id.')"><option value="0" '.($shop[$zv_sh_data[cshop]]['shopbanner']==0?'selected':'').'>нет</option><option  value="1" '.($shop[$zv_sh_data[cshop]]['shopbanner']==1?'selected':'').' >Новинка!</option><option  value="2" '.($shop[$zv_sh_data[cshop]]['shopbanner']==2?'selected':'').'>Акция!</option></select><br>';						
					
					}
					else
					{
						echo '<img src=http://i.oldbk.com/i/clear.gif>';
					}	
					
		//выводит построчно комки в городах, если прототипы отличаются в таблицах шопов			
		//if (shop!=0 && cshop!=0 && shop !=cshop) || (shop!=0 && eshop!=0 && shop!=eshop) || (cshop!=0 && eshop!=0 && cshop!=eshop)
					echo '</div></td><td><div align=center id="tk'.$zv_id.'" >';
						if (!$comshop[0][$zv_sh_data[shop]]['count']) {$comshop[0][$zv_sh_data[shop]]['count']=0;}
						if (!$comshop[1][$zv_sh_data[shop]]['count']) {$comshop[1][$zv_sh_data[shop]]['count']=0;}
						
						if (!$comshop[0][$zv_sh_data[eshop]]['count']) {$comshop[0][$zv_sh_data[eshop]]['count']=0;}
						if (!$comshop[1][$zv_sh_data[eshop]]['count']) {$comshop[1][$zv_sh_data[eshop]]['count']=0;}
						
						if (!$comshop[0][$zv_sh_data[cshop]]['count']) {$comshop[0][$zv_sh_data[cshop]]['count']=0;}
						if (!$comshop[1][$zv_sh_data[cshop]]['count']) {$comshop[1][$zv_sh_data[cshop]]['count']=0;}
			
					     
					     	if (($zv_sh_data[shop]!=0 && $zv_sh_data[cshop]!=0 && $zv_sh_data[shop] !=$zv_sh_data[cshop]) || 
						($zv_sh_data[shop]!=0 && $zv_sh_data[eshop]!=0 && $zv_sh_data[shop]!=$zv_sh_data[eshop]) || 
						($zv_sh_data[cshop]!=0 && $zv_sh_data[eshop]!=0 && $zv_sh_data[cshop]!=$zv_sh_data[eshop]))
						{
							echo '<b>Гос вещь</b><br><br>';
							
							if($zv_sh_data[shop]>0)
							{
								echo 'id: '.$zv_sh_data[shop].'<br>';
								echo 'Кэпитал: <input size=8 disabled type=text value='.$comshop[0][$zv_sh_data[shop]]['count'].'><br><small>Цены: от '.$comshop[0][$zv_sh_data[shop]]['min_setsale'].'кр. до '.$comshop[0][$zv_sh_data[shop]]['max_setsale'].'кр.</small><br><br>';
								echo 'Авалон&nbsp;: <input size=8 disabled type=text value='.$comshop[1][$zv_sh_data[shop]]['count'].'><br><small>Цены: от '.$comshop[1][$zv_sh_data[shop]]['min_setsale'].'кр. до '.$comshop[1][$zv_sh_data[shop]]['max_setsale'].'кр.</small><br><hr>'; 
								
							}
							else
							{
								echo '<img src=http://i.oldbk.com/i/clear.gif><br><br>';
							}
							echo '<b>Екр вещь</b><br><br>';
							
							if($zv_sh_data[eshop]>0)
							{
								echo 'id: '.$zv_sh_data[eshop].'<br>';
								echo 'Кэпитал: <input size=8 disabled type=text value='.$comshop[0][$zv_sh_data[eshop]]['count'].'><br><small>Цены: от '.$comshop[0][$zv_sh_data[eshop]]['min_setsale'].'кр. до '.$comshop[0][$zv_sh_data[eshop]]['max_setsale'].'кр.</small><br><br>';
								echo 'Авалон&nbsp;: <input size=8 disabled type=text value='.$comshop[1][$zv_sh_data[eshop]]['count'].'><br><small>Цены: от '.$comshop[1][$zv_sh_data[eshop]]['min_setsale'].'кр. до '.$comshop[1][$zv_sh_data[eshop]]['max_setsale'].'кр.</small><br><hr>'; 
								
							}
							else
							{
								echo '<img src=http://i.oldbk.com/i/clear.gif><br><br>';
							}
							echo '<b>Храм вещь</b><br><br>';
							if($zv_sh_data[cshop])
							{
								echo 'id: '.$zv_sh_data[cshop].'<br>';
								echo 'Кэпитал: <input size=8 disabled type=text value='.$comshop[0][$zv_sh_data[cshop]]['count'].'><br><small>Цены: от '.$comshop[0][$zv_sh_data[cshop]]['min_setsale'].'кр. до '.$comshop[0][$zv_sh_data[cshop]]['max_setsale'].'кр.</small><br><br>';
								echo 'Авалон&nbsp;: <input size=8 disabled type=text value='.$comshop[1][$zv_sh_data[cshop]]['count'].'><br><small>Цены: от '.$comshop[1][$zv_sh_data[cshop]]['min_setsale'].'кр. до '.$comshop[1][$zv_sh_data[cshop]]['max_setsale'].'кр.</small><br><hr>';	
							}
							else
							{
								echo '<img src=http://i.oldbk.com/i/clear.gif><br><br>';
							}
						}
						else
						{	
							if($zv_sh_data[shop]>0)
							{
								$iid=$zv_sh_data[shop];
							}
							else
							if($zv_sh_data[eshop]>0)
							{
								$iid=$zv_sh_data[eshop];
							}
							else
							if($zv_sh_data[cshop]>0)
							{
								$iid=$zv_sh_data[cshop];
							}
							echo 'Кэпитал: <input size=8 disabled type=text value='.$comshop[0][$iid]['count'].'><br><small>Цены: от '.$comshop[0][$iid]['min_setsale'].'кр. до '.$comshop[0][$iid]['max_setsale'].'кр.</small><br><br>';
							echo 'Авалон&nbsp;: <input size=8 disabled type=text value='.$comshop[1][$iid]['count'].'><br><small>Цены: от '.$comshop[1][$iid]['min_setsale'].'кр. до '.$comshop[1][$iid]['max_setsale'].'кр.</small><br>';	
						}
							
					echo '</div></td>';
					echo '<td> 
					<input type="button" disabled="disabled" id="but_'.$zv_id.'" value="Сохранить" onclick="save_change('.$zv_id.')">
					</td>';
				echo '</tr>';			
			}
		?>
		</table>
		<?
		
	}
	
	
	?>
	</body>
	</html>
	
	<?
}
?>


