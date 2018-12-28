<?php

//компресия для инфы
///////////////////////////
    if (strpos(' ' . $_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip') !== false) {
    $miniBB_gzipper_encoding = 'x-gzip';
    }
    if (strpos(' ' . $_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false) {
    $miniBB_gzipper_encoding = 'gzip';
    }
    if (isset($miniBB_gzipper_encoding)) {
    ob_start();
    }
    function percent($a, $b) {
    $c = $b/$a*100;
    return $c;
    }
//////////////////////////////
	session_start();
//      	ini_set('display_errors','On');

	$city_name[0]='CapitalCity';
	$city_name[1]='AvalonCity';
	$city_name[2]='AngelsCity';	
	$script_start=microtime();
	if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }
	include "connect.php";

	$user = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;"));

	if ( ($user['naim'] >0 ) and ($user['klan'] == '') )
	{
	//редирект для наймов которые не вклане
		die('<script>location.href = "main.php?edit=1&effects=1&post_attack='.$_GET['post_attack'].')";</script>');
	}
	else
	if ($user['klan'] == '') {
    		die();
	}
	

	include "functions.php";
 	include "bank_functions.php";

	/*if ($user['id'] == 14897) 
	{
	include "klan_functions2.php";
	}
	else*/
	{
	include "klan_functions.php";	
	}

	//include "clan_kazna.php"; тут нинадо надо только в функции по работе перенес туда
	if ($user[klan]=='radminion') {  echo "Admin-info:<!- GZipper_Stats -> <br>"; }
		

		
	//razdel=wars&post_attack=188
	
	//готовим префикс базы для выбора других
	if ($user[id_city]==0) { $db_other_city='avalon.';  $id_other_city=1; }
	else if ($user[id_city]==1) { $db_other_city='oldbk.' ; $id_other_city=0; }
	else { $db_other_city='' ; $id_other_city=0;}

	$klan = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`clans` WHERE `short` = '{$user['klan']}' LIMIT 1;"));
   	$polno = array();
	$polno = unserialize($klan['vozm']);

	if($klan[rekrut_klan]>0)
	{
		$recrut=mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`clans` WHERE `id` = '".$klan[rekrut_klan]."' LIMIT 1;"));
	    $telegr=$recrut[short];
	    $rass_name='рекрутам';
	}
	
	if($klan[base_klan]>0)
	{
		$base_klan=mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`clans` WHERE `id` = '".$klan[base_klan]."' LIMIT 1;"));
	    $telegr=$base_klan[short];
	    $rass_name='основе';
	}

	//небольшой фикс для админов:
	if($user[klan]=='Adminion' || $user[klan]=='radminion')
	{
		if($user[id]==28453 || $user[id]==326 || $user[id]==8540 || $user[id]==102904 )
		{
			$klan[glava]=$user[id];
		}
	}
	



    // клан чат вкл-выкл
	if (isset($_GET['offall'])) {
		if ($_GET['offall'] == 1) {
			$_SESSION['offclanchat'] = 1;
		}
		else
		{
			$_SESSION['offclanchat'] = 0;
		}
	}
    include "clan_kazna.php";
    $klan_kazna=clan_kazna_have($klan['id']);
    //	<meta content="text/html; charset=windows-1251" http-equiv=Content-type>
?>

	<HTML><HEAD>
	<link rel=stylesheet type="text/css" href="i/main.css">
    <link rel="stylesheet" href="/i/btn.css" type="text/css">
	<META Http-Equiv=Cache-Control Content="no-cache, max-age=0, must-revalidate, no-store">
	<meta http-equiv=PRAGMA content=NO-CACHE>
	<META Http-Equiv=Expires Content=0>
	<link rel="stylesheet" type="text/css" href="http://i.oldbk.com/i/jscal/css/jscal2.css" />
	<link rel="stylesheet" type="text/css" href="http://i.oldbk.com/i/jscal/css/border-radius.css" />
	<link rel="stylesheet" type="text/css" href="http://i.oldbk.com/i/jscal/css/steel/steel.css" />
	<script type="text/javascript" src="http://i.oldbk.com/i/jscal/js/jscal2.js"></script>
	<script type="text/javascript" src="http://i.oldbk.com/i/jscal/js/lang/ru2.js"></script>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.1/jquery.min.js"></script>
	<script type="text/javascript" src="i/showthing.js"></script>	
	<script type="text/javascript" src="/i/globaljs.js"></script>
	<SCRIPT type="text/javascript">
	var priem_vigon,change_status,ars_access,kazn_access,priem_vigon,klan_channels,klan_mchannels,status;
	function check_form(id)
	{
	  if($('#priem_vigon_'+id).attr('checked')){priem_vigon='on';}else{priem_vigon='off';}
      if($('#change_status_'+id).attr('checked')){change_status='on';}else{change_status='off';}
      if($('#ars_access_'+id).attr('checked')){ars_access='on';}else{ars_access='off';}
      if($('#kazn_access_'+id).attr('checked')){kazn_access='on';}else{kazn_access='off';}

	  klan_channels=$('#klan_channels_'+id).attr('value');
      klan_mchannels=$('#klan_mchannels_'+id).attr('value');

      status=$('#status_'+id).attr('value');
      dataString='?priem_vigon='+priem_vigon+'&change_status='+change_status+'&ars_access='+ars_access+'&kazn_access='+kazn_access+'&klan_channels='+klan_channels+'&klan_mchannels='+klan_mchannels+'&status='+status;
       $.ajax({
			      type: "POST",
			      url: "klans_r.php",
			      data: dataString,
			      success: function(ans) {
			          $('#rez').html( ans )
					  return false;
	      			}
	   		  });

	}
	</script>
	<style>
		.row {
			cursor:pointer;
		}
		.green{
			background: #F0FFF0;
		}
		.red{
			background: #FFCC99;
		}
	</style>
	<script type="text/javascript">
	  function show(ele) {
	      var srcElement = document.getElementById(ele);
	      if(srcElement != null) {
	          if(srcElement.style.display == "block") {
	            srcElement.style.display= 'none';
	          }
	          else {
	            srcElement.style.display='block';
	          }
	      }
	  }
	function returned2(s){
		if (top.oldlocation != '') { top.frames['main'].location.href=top.oldlocation+'?'+s+'tmp='+Math.random(); top.oldlocation=''; }
		else { top.frames['main'].location.href='main.php?edit='+Math.random() }
	}
	

<?
include("jsfunction.php");
?>
</script>	
<script src="i/jquery.drag.js" type="text/javascript"></script>	
<script>	

			function getformdata(id,param,event)
			{
				if (window.event) 
				{
					event = window.event;
				}
				if (event ) 
				{

				       $.get('kaznapayform.php?id='+id+'&param='+param+'', function(data) {
					  $('#pl').html(data);
					  $('#pl').show(200, function() {
						});
					});
				
				 $('#pl').css({ position:'absolute',left: ($(window).width()-$('#pl').outerWidth())/2, top: '120px'  });	


				}
				
			}
			
			function closeinfo()
			{
			  	$('#pl').hide(200);
			}
			
$(window).resize(function() {
 $('#pl').css({ position:'absolute',left: ($(window).width()-$('#pl').outerWidth())/2, top: '120px'  });
});			

function callcekr(rub,kurs)
{
document.getElementById('qekr').value=(rub/kurs).toFixed(2);
}

function callcekrwmr(rub,kurs)
{
document.getElementById('wmrekr').value=(rub/kurs).toFixed(2);
}

function callcrub(ekr,kurs)
{
document.getElementById('qrub').value=(ekr*kurs).toFixed(2);
}

function callcrubwmr(ekr,kurs)
{
document.getElementById('wmrrub').value=(ekr*kurs).toFixed(2);
}


function callcekrwmz(ekr,kurs)
{
document.getElementById('awmz').value=(ekr*kurs).toFixed(2);
}

function callcwmz(ekr,kurs)
{
document.getElementById('ekwmz').value=(ekr/kurs).toFixed(2);
}
	
</script>	
	
	
<style>
.m {background: #99CCCC;text-align: center;}
.s {background: #BBDDDD;text-align: center;}
.s2 {background: #C0D6D4;text-align: center;}

A.menu {
	FONT-WEIGHT: bold; FONT-SIZE: 8pt; COLOR: #959595; TEXT-DECORATION: none

}
A.menu2 {
	FONT-WEIGHT: bold; FONT-SIZE: 8pt; COLOR: #8F0000; TEXT-DECORATION: none

}
A.menu:hover {
	FONT-WEIGHT: bold; FONT-SIZE: 8pt; COLOR: #8F0000; TEXT-DECORATION: none

}
.menu22{
  FONT-WEIGHT: bold; FONT-SIZE: 8pt; COLOR: #003388; TEXT-DECORATION: none
}
.menu221{
background-color: #A5A5A5;
text-align: center;
}
.menu222{
FONT-WEIGHT: bold; FONT-SIZE: 8pt; COLOR: #000000; TEXT-DECORATION: none
}
</style>
	</HEAD>
	<body leftmargin=5 topmargin=5 marginwidth=0 marginheight=0 bgcolor=#d7d7d7>
	<div id=hint3 class=ahint></div>
	<div id="pl" style="z-index: 300; position: absolute; left: 155px; top: 120px;width: 750px; height:365px; background-color: #eeeeee; border: 1px solid black; display: none;"></div>
	<?
		$_GET['razdel']=(isset($_GET['razdel'])?$_GET['razdel']:'main');
	?>
	<table width=100%>
	<tr>
		<td align=right>
            <div class="btn-control">
				<INPUT class="button-mid btn" TYPE="button" onclick="location.href='main.php';" value="Вернуться" title="Вернуться">
            </div>
         </td>
    </tr>
    <tr>
		<td width=100% valign=top align="center">
<table border=0 width=956>
<tr>
<td width=956 style="background-image: url(http://i.oldbk.com/i/frames/menu_bg33.jpg); background-repeat: no-repeat" >
	<table border=0 cellpadding=4 cellspacing=3>
	<tr height=38>
		<td width="15">&nbsp;</td>
		<td align="center" width=127 valign=top><a class=<?=($_GET['razdel']=='main'?'"menu2"':'"menu"')?> href=klan.php?razdel=main>Главная</a></td>
		<td align="center" width=127 valign=top><a class=<?=($_GET['razdel']=='kazna'?'"menu2"':'"menu"')?> href=klan.php?razdel=kazna>Казна</a></td>
		<td align="center" width=140 valign=top><a class=menu href=klan_arsenal.php>Арсенал</a></td>
		<td align="center" width=143 valign=top><a class=<?=($_GET['razdel']=='wars'?'"menu2"':'"menu"')?> href=klan.php?razdel=wars>Войны и враги</a></td>
		<td align="center" width=140 valign=top><a class=<?=($_GET['razdel']=='message'?'"menu2"':'"menu"')?> href=klan.php?razdel=message>Сообщения</a></td>
		<td align="center" width=132 valign=top><a class=menu href="klan_castles.php">Замки</a></td>
		<td align="center" width=122 valign=top><a class=<?=($_GET['razdel']=='maintains'?'"menu2"':'"menu"')?> href=klan.php?razdel=maintains>Управление</a></td>
		<td width="5">&nbsp;</td>
	</tr>
	</table>
</td>
</tr>
</table>

				    <table border=0 width=1100>
						<tr>
							<td>
								<table width=100% border=0>
								<tr>
									<!--рисуем контаент-->
									<td align="center">
									<?
										if($_GET['razdel']=='main')
										{
			       							$faction='razdel=main';
			       							 echo '
			       							 <table>
			       							 	<tr>
			       							 		<td colspan=3 valign=top align=center>' ;
			       										show_klans_names($_POST,$_GET,$faction);
							                                   echo '</td>
							                                   	</tr>
							                                   	<tr>
							                                   		<td valign=top>';
							                                   			show_use_clan_chat($_POST,$_GET,$faction);
														if($user[in_tower]==4)
						                                   				{
						                                   					
						                                   				}
						                                   				else
						                                   				{
								                                        		use_klan_abils($_POST,$_GET,$faction);
								                                        	}		
							                                  echo '</td>
							                                        <td width=25>&nbsp;</td>
							                                  		<td valign=top align=center> ';
							                                  			show_main_klan_info($_POST,$_GET,$faction);
														   	  echo '</td>
														   		</tr>
										   	</table>';
										}
										else
										if($_GET['razdel']=='kazna')
										{
							                                   $faction='razdel=kazna';
							                                   echo "<table valign=top align=center width=100%>
					                                   			<tr>
				                                   				<td>";
					                                   				if($user[in_tower]==4)
					                                   				{
					                                   					echo '<center><font color=red><b>В темнице пользоваться казной запрещено.</b></font></center>';
					                                   				}
					                                   				else
					                                   				{
						                                   				//if($user[id]==28453 || $user[id]==387139 || $user[id]==396445) 
						                                   				{
						                                   				 	pay_for_clan($_POST,$_GET,$faction);
						                                   				}
						                                   				echo '<br>';
						                                   				show_use_klan_kazna($_POST,$_GET,$faction);
					                                  				}
					                                   			echo "</td>
					                                   			</tr>
				                                   				</table>";
										}
										else
										if($_GET['razdel']=='arsenal')
										{
											if($user[in_tower]==4)
			                                   				{
			                                   					echo '<center><font color=red><b>В темнице пользоваться арсеналом запрещено.</b></font></center>';
			                                   				}
			                                   				else
			                                   				{
												$faction='razdel=arsenal';
												echo "<br><br>";
												show_use_klan_arsenal($_POST,$_GET,$faction);
												echo "<br>";
											}
										}
			                               				else
										if($_GET['razdel']=='wars')
										{
											if($user[in_tower]==4)
			                                   				{
			                                   					echo '<center><font color=red><b>Вы не можете воевать сидя в темнице!</b></font></center>';
			                                   				}
			                                   				else
			                                   				{
												$faction='razdel=wars';
												show_clans_war_rendering($_POST,$_GET,$faction);
											}
										}
										else
										if($_GET['razdel']=='message')
										{
							                                   $faction='razdel=message';
							                                    echo "<table valign=top align=center width=750><tr><td>";
							                                   	show_caln_messages($_POST,$_GET,$faction);
							                                    echo "</td></tr></table>";
										}
										else
										if($_GET['razdel']=='maintains')
										{            /*'.($_GET['und']=='1'?'class="s"':'class="m"').'*/
										
											if($user[in_tower]==4)
			                                   				{
			                                   					echo '<center><font color=red><b>Вы не можете управлять кланом сидя в темнице!</b></font></center>';
			                                   				}
			                                   				else
			                                   				{
												$_GET['und']=(isset($_GET['und'])?$_GET['und']:'1');
												
												if($user[id]==5291)
												{
													print_r($polno[$user['id']]);
												}
							                                   	echo '
												<table cellspacing=0 cellpadding=0 border=0>
													<tr height=31>
														<td background="http://i.oldbk.com/i/frames/left_down.jpg" width=37>
														</td>
														<td align=center width=758 background="http://i.oldbk.com/i/frames/x-bg_down.jpg" valign=top>
															<table cellpadding=3>
																<tr>
																	<td  align="center" valign=top>
																	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class='.($_GET['und']=='1'?'"menu2"':'"menu"').' href=?razdel=maintains&und=1>Управление кланом</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
																	</td>';
																	
																	if($user[id]==$klan[glava] || $polno[$user['id']][0]==1 || $polno[$user['id']][1]==1)
																	{
																		echo '
																		<td  align="center" valign=top>
																		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class='.($_GET['und']=='2'?'"menu2"':'"menu"').' href=?razdel=maintains&und=2>Управление правами</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
																		</td>';
																	}
																	if($user[id]==$klan[glava])
																	{
																		echo '<td align="center" valign=top>
																		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class='.($_GET['und']=='3'?'"menu2"':'"menu"').' href=?razdel=maintains&und=3>Управление реликтами</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
																		</td>';
																		if($user[klan]=='pal' || $user[klan]=='Adminion' || $user[klan]=='radminion')
																		{
																			echo '<td align="center" valign=top>
																			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class='.($_GET['und']=='4'?'"menu2"':'"menu"').' href=?razdel=maintains&und=4>АбилкиПалов</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
																			</td>';
																		}
																	}
																echo'
																</tr>
															</table>
														
														</td>
														<td background="http://i.oldbk.com/i/frames/right_down.jpg" width=37>
														</td>
													</tr>
												</table>
							                                   <br>';
	
	
												if($_GET[und]==1)
												{  
												//прием выгон, каналы
													$faction='razdel=maintains&und=1';
													echo "<table valign=top align=center width=800><tr><td>";
													if($user[id]==$klan[glava] || $polno[$user['id']][7]==1)
													{
														show_use_klan_telegraph($_POST,$_GET,$faction);
													}
												
														show_use_add_drive_members($_POST,$_GET,$faction);
												
													if(($user[id]==$klan[glava] || $polno[$user['id']][0]==1))
													{
														work_with_klan_chennels($_POST,$_GET,$faction);
														if($klan[base_klan]==0)
														{
															klan_reiting($_POST,$_GET,$faction);
														}
														echo "</td></tr></table>";
													}
												}
												else
												if($_GET[und]==2)
												{
													$faction='razdel=maintains&und=2';
													show_change_clans_right($_POST,$_GET,$faction);
												}
												else
												if($_GET[und]==3)
												{
													if($user[id]==$klan[glava])
													{
														$faction='razdel=maintains&und=3';
														if($klan[rekrut_klan])
														{
															echo '<table><tr><td align=center>';
															change_abils_rec($_POST,$_GET,$faction);
															echo '</td></tr></table>';
														}
														change_abils($_POST,$_GET,$faction);
													}
												}
												else
												if($_GET[und]==4)
												{
													if($user[id]==$klan[glava] && ($user[klan]=='pal' || $user[klan]=='Adminion' || $user[klan]=='radminion'))
													{
														$faction='razdel=maintains&und=4';
														pal_change_abils($_POST,$_GET,$faction);
													}
												}
											}
										}

										?>
										</td>
			                           <!--/рисуем контаент-->
									</tr>
								</table>
							</td>
						</tr>
					</table>
          <!--/рисуем красивую картинку-->
		</center>
		</td>
	</tr>
	</table>
	</body>
	</html>
<?

/////////////////////////////////////////////////////
    if (isset($miniBB_gzipper_encoding)) {
    $miniBB_gzipper_in = ob_get_contents();
    $miniBB_gzipper_inlenn = strlen($miniBB_gzipper_in);
    $miniBB_gzipper_out = gzencode($miniBB_gzipper_in, 2);
    $miniBB_gzipper_lenn = strlen($miniBB_gzipper_out);
    $miniBB_gzipper_in_strlen = strlen($miniBB_gzipper_in);
    $gzpercent = percent($miniBB_gzipper_in_strlen, $miniBB_gzipper_lenn);
    $percent = round($gzpercent);
    $miniBB_gzipper_in = str_replace('<!- GZipper_Stats ->', 'Original size: '.strlen($miniBB_gzipper_in).' GZipped size: '.$miniBB_gzipper_lenn.' Сompression: '.$percent.'%<hr>', $miniBB_gzipper_in);
    $miniBB_gzipper_out = gzencode($miniBB_gzipper_in, 2);
    ob_clean();
    header('Content-Encoding: '.$miniBB_gzipper_encoding);
    echo $miniBB_gzipper_out;
    }
/////////////////////////////////////////////////////

?>