<?php
	session_start();
	if ($_SESSION['uid'] == null)
	{
		header("Location: index.php");
		die;
	}

	include "connect.php";
	include "functions.php";
	
	$lz=(int)$_GET['lz'];
	if (($lz<=9) or ($lz>12))
		{
		$lz=9;
		}
	
	$tab1='active'; $tab2='';
	 if ($_GET['lz'])
	 	{
		$tab2='active'; $tab1='';	 	
	 	}

	if ($user['room'] != 20)  { header("Location: main.php");    die(); }
	if ($user['battle'] != 0) { header('location: fbattle.php'); die(); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title></title>
    <link rel="StyleSheet" href="newstyle_loc4.css" type="text/css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
</head>
<body>
<div id="page-wrapper">
    <div id="buttons" class="clearfix">
        <? //<a class="button-dark-mid btn" href="javascript:void(0);" title="Подсказка">Подсказка</a> ?>
        <a class="button-mid btn" href="javascript:void(0);" title="Обновить" onclick="location.href='doska.php?refresh=<?echo mt_rand(1111,9999);?>';" >Обновить</a>
        <a class="button-mid btn" href="javascript:void(0);" title="Вернуться" onclick="location.href='main.php';">Вернуться</a>
    </div>
    <div id="board">
        <img class="bg" src="http://i.oldbk.com/i/images/doska/cpDesk_BG80.jpg">
        <div class="wrapp">
            <div class="buttons">
                <div id="other" class="active" data-for="tab1"></div>
                <div id="castle" data-for="tab2"></div>
            </div>
            <div class="content">
                <div id="tab1" class="tab <?=$tab1;?>">
                    <ul>
                        <li class="title">
				Лотерея ОлдБк
                        </li>                    
                    <?
		   $get_lot=mysql_fetch_array(mysql_query("select * from oldbk.item_loto_ras where status=1 LIMIT 1;"));
		   if ($get_lot[id] >0)
		  {
			   echo "<li class=\"center\">Следующий тираж <B>№ $get_lot[id] </B> состоится <span class=date>".date("d-m-Y H:i",$get_lot[lotodate])."</span> </li>";
			  }
			   else
			   {
			   echo "<li class=\"center\"><i>Нет данных</i></li>";
			   }
                    ?>
                    </ul>
                    <div class="separator"></div>
                     <ul>               
                        <li class="title">
                            Ближайшая атака:
                        </li>
                        <li class="center">
                            <?
				$t=mysql_query_cache("select * from oldbk.variables where var='bots_start_time' ",false,60);
				$t=$t[0];
				
				$freedomt=$t[value];
				if ($freedomt-time() > 0)
				   {
				    echo "Монстры нападут через:".floor(($freedomt-time())/60/60)." ч. ".round((($freedomt-time())/60)-(floor(($freedomt-time())/3600)*60))." мин.\" ";
				    }
				    else
				    {
				       echo "Монстры атакуют город";
				    }
                            ?>
                        </li>
                    </ul>
                    <div class="separator"></div>
                    <ul>
                        <li class="title">
                            Ближайшие бои на Арене Богов СВЕТ VS ТЬМА:
                        </li>
                        <?
			   // арена
			     $data=mysql_query('select * from place_zay order by start limit 3');
			   while($row=mysql_fetch_array($data))
			   {
			   echo '
			             <li>
                            		<div class="larena">Уровень '.($row[t1min]==$row[t1max]?$row[t1min]:$row[t1min].'-'.$row[t1max]).':</div> '.date('d-m-Y', $row[start]).' в '.date('H:i:s', $row[start]).' ('.$row[coment].')
		                     </li>';

			   }
                        ?>
                    </ul>
                    <div class="separator"></div>
                    <ul>
                        <li class="title">
                            Бои с Исчадием Хаоса:
                        </li>
                        <li class="center">
				<?
				$sqlget="select * from variables where var='ghost_all_time' ; ";
				$q_get=mysql_query($sqlget);
		       		if (mysql_affected_rows() > 0)
				{
				$t=mysql_fetch_array($q_get);
				$freedomt=$t[value];
				if ($freedomt-time() > 0)
				   {
		   		    $get_bot_next=mysql_fetch_array(mysql_query("select *  from users where id=(select value from variables where var='ghost_next_id');"));
				    echo $get_bot_next[login]."<a target=_blank href=/inf.php?".$get_bot_next[id]."><img src=http://i.oldbk.com/i/inf.gif></a>- вырвусь на свободу через:".floor(($freedomt-time())/60/60)." ч. ".round((($freedomt-time())/60)-(floor(($freedomt-time())/3600)*60))." мин.";
				    }
				    else
				    {
				    $get_bot_next=mysql_fetch_array(mysql_query("select *  from users_clons where id_user=(select value from variables where var='ghost_next_id');"));
					echo $get_bot_next[login]."<a target=_blank href=/inf.php?".$get_bot_next[id]."><img src=http://i.oldbk.com/i/inf.gif></a>- Online";
				    }
				}

				?>
                        </li>
                    </ul>
                    <div class="separator"></div>
                    <ul>
                        <li class="title">
                            Начало ближайшей Башни Смерти:
                        </li>
                        <li class="center">
			<?
			   $bs=mysql_fetch_array(mysql_query('select * from `dt_var` where var="nextdt"'));
			   $cur_bs=mysql_fetch_array(mysql_query('select * from `dt_map` where active=1'));
			   $bs_type = mysql_fetch_assoc(mysql_query('select * from `dt_var` where var="nextdttype"'));
			   $txt=($cur_bs?'<font color=red>Турнир уже начался</font>':date("d-m-Y в H:i", $bs['valint']));
			
				echo $txt.($bs_type['valint']>0?'(артовая)':'');

			?>
                        </li>
                    </ul>
                </div>
                <div id="tab2" class="tab <?=$tab2;?>">
                    <ul>
                        <li class="title">
                            Статусы Замков:
                            <?
                             for ($ii=14;$ii<=14;$ii++)
                             	{
                             	echo "<a href=?lz=$ii>[$ii]</a>";
                             	}
                            ?>
                        </li>
                        <li class="center">
				<?
				require_once "castles_config.php";
				require_once "castles_functions.php";
				$data=mysql_query("SELECT * FROM oldbk.castles WHERE id != 155 and nlevel='{$lz}'");
			
				if (mysql_num_rows($data) > 0) 
				{
			    		while($row=mysql_fetch_array($data)) 
			    		{
			    		echo "                        <li class=\"center\">";
				   	echo $castles_config[$row['num']]['name']." [".$row['nlevel']."] ";
				   	$sta=GetCastleStatus(array(),$row);
				   	$sta=str_replace("Замок принадлежит клану","клан -",$sta);
				   	$sta=str_replace("Защищен от нападения до","Защищен до",$sta);				   	
				   	$sta=str_replace("Замок никому не принадлежит","<strong style=\"color:#F00;\">Свободен</strong>",$sta);				   					   	
				   	
				   	echo $sta;
			    		echo "</li>";				   	
				   	}
			    		
			   	}   
				
				?>
                        </li>
                        
                    </ul>
                    
                    <div class="separator"></div>
                      <li class="center">
                    <?
                    	$q = mysql_query('SELECT count(*) as cc FROM oldbk.castles WHERE clanshort = ""');
			$qq = mysql_fetch_assoc($q);
			echo 'Всего Свободных замков: <strong>'.$qq['cc'].'</strong>';
			?>
                    </li>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function(){
        $(document.body).on('click', '#board .buttons div', function(){
            var $self = $(this);
            $self.closest('.buttons').find('div').removeClass('active');
            $self.addClass('active');
            $('#board .content .tab').removeClass('active');
            $('#board .content #' + $self.data('for')).addClass('active');
        });
    });
</script>
</body>
</html>
