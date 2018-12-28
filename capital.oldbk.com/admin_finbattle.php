<?
//////////////////////////////

session_start();
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }

include "connect.php";
include "functions.php";
include "fsystem.php";

if ($user[klan]!='radminion' && $user[klan]!='Adminion') {  die('Страница не найдена...'); }
include_once ROOT_DIR.'/components/Component/Security/check_2fa.php';
?>
<HTML><HEAD>
    <link rel=stylesheet type="text/css" href="http://i.oldbk.com/i/main.css">
    <meta content="text/html; charset=windows-1251" http-equiv=Content-type>
    <META Http-Equiv=Cache-Control Content=no-cache>
    <meta http-equiv=PRAGMA content=NO-CACHE>
    <META Http-Equiv=Expires Content=0>
</HEAD>
<body leftmargin=5 topmargin=5 marginwidth=5 marginheight=5 bgcolor=#e0e0e0>
<br><br>
<b>Окончить бой:</b>
<FORM  method="POST">
    <b>ID боя : </b> <INPUT TYPE=TEXT name=battleid> <br>
    <b>Ничья</b><input  name=win  type=radio value=0 checked ><br>
    <b>Победа 1й команды:</b><input  name=win  type=radio value=1><br>
    <b>Победа 2й команды:</b><input  name=win  type=radio value=2><br>
    <b>Победа 3й команды <small>(если бой на 3х)</small>:</b><input  name=win  type=radio value=3><br>
    <b>Проигравшим выставить 0 HP <small>(если ничья то всем)</small>:</b><INPUT type=checkbox name=hpdown  value=1><br>
    <INPUT TYPE="submit" value="Окончить Бой" name="fin"><br>
</FORM>
<?
//print_r($_POST);
echo "<br>";

if ($_POST['battleid'])
{
	$bid=(int)($_POST['battleid']);
	$win=(int)($_POST['win']);
	$hp=(int)($_POST['hpdown']);

	$data_battle=mysql_fetch_array(mysql_query("SELECT * FROM battle where id={$bid} ; "));

	if ($data_battle['id']>0)
	{
		echo "Бой найден... <br>";
		if ($data_battle['win']==3)
		{
			echo "Бой еще идет... <br>";
			if (($data_battle['status']==0)	and ($data_battle['t1_dead']=='') )
			{
				echo "Бой не в блокировке... <br>";

				if ($win==0)
				{
					//ничья

					$fin_add_log="!:F:".time().":0\n";
					//ставим зарубку в бой о том что сделан последний лог-шоб не дублировать
					mysql_query("update battle set t1_dead='finlog', status=1 where id={$data_battle['id']} and t1_dead='' and status=0  ;");
					if (mysql_affected_rows()>0)
					{

						addlog($data_battle['id'],$fin_add_log);

						$data_battle['win']=0;
						$winrez[0]=finish_battle(0,$data_battle,$data_battle['blood'],$data_battle['type'],$data_battle['fond']);
						addlog($data_battle['id'],get_text_broken($data_battle));

						if ($hp==1)
						{
							//обнуление хп всем
							mysql_query("UPDATE users set hp=0 where last_battle='{$data_battle['id']}' ");
							if (mysql_affected_rows()>0)
							{
								err('Всем выставленно 0 hp!<br>');
							}
						}

						err('Бой id-'.$data_battle['id'].' окончен - ничья!');
					} else {
						err('Бой не окончен ошибка блокировки');
					}


				}
                elseif ($win==1)
				{

					$win_team_hist='t1hist';
					$fin_add_log="!:F:".time().":0:".$data_battle[$win_team_hist]."\n";

					//ставим зарубку в бой о том что сделан последний лог-шоб не дублировать
					mysql_query("update battle set t1_dead='finlog', status=1 where id={$data_battle['id']} and t1_dead='' and status=0  ;");
					if (mysql_affected_rows()>0)
					{

						addlog($data_battle['id'],$fin_add_log);

						$data_battle['win']=1;
						$winrez[0]=finish_battle(1,$data_battle,$data_battle['blood'],$data_battle['type'],$data_battle['fond']);
						if ($data_battle['blood']>0) { 	 addlog($data_battle['id'],get_text_travm($data_battle)); }
						addlog($data_battle['id'],get_text_broken($data_battle));

						if ($hp==1)
						{
							//обнуление хп всем
							mysql_query("UPDATE users set hp=0 where last_battle='{$data_battle['id']}' and battle_t!=1");
							if (mysql_affected_rows()>0)
							{
								err('Всем выставленно 0 hp проигравшим!<br>');
							}
						}

						err('Бой id-'.$data_battle['id'].' окончен - Победа первой команды!');
					}
					else
					{
						err('Бой не окончен ошибка блокировки');
					}

				}
                elseif ($win==2)
				{
					$win_team_hist='t2hist';
					$fin_add_log="!:F:".time().":0:".$data_battle[$win_team_hist]."\n";

					//ставим зарубку в бой о том что сделан последний лог-шоб не дублировать
					mysql_query("update battle set t1_dead='finlog', status=1 where id={$data_battle['id']} and t1_dead='' and status=0  ;");
					if (mysql_affected_rows()>0)
					{

						addlog($data_battle['id'],$fin_add_log);

						$data_battle['win']=2;
						$winrez[0]=finish_battle(2,$data_battle,$data_battle['blood'],$data_battle['type'],$data_battle['fond']);
						if ($data_battle['blood']>0) { 	 addlog($data_battle['id'],get_text_travm($data_battle)); }
						addlog($data_battle['id'],get_text_broken($data_battle));

						if ($hp==1)
						{
							//обнуление хп всем
							mysql_query("UPDATE users set hp=0 where last_battle='{$data_battle['id']}' and battle_t!=2");
							if (mysql_affected_rows()>0)
							{
								err('Всем выставленно 0 hp проигравшим!<br>');
							}
						}

						err('Бой id-'.$data_battle['id'].' окончен - Победа второй команды!');
					}
					else
					{
						err('Бой не окончен ошибка блокировки');
					}
				} elseif (($win==3) and ($data_battle['t3']!='')) {
					$win_team_hist='t3hist';
					$fin_add_log="!:F:".time().":0:".$data_battle[$win_team_hist]."\n";

					//ставим зарубку в бой о том что сделан последний лог-шоб не дублировать
					mysql_query("update battle set t1_dead='finlog', status=1 where id={$data_battle['id']} and t1_dead='' and status=0  ;");
					if (mysql_affected_rows()>0)
					{

						addlog($data_battle['id'],$fin_add_log);

						$data_battle['win']=4;
						$winrez[0]=finish_battle(4,$data_battle,$data_battle['blood'],$data_battle['type'],$data_battle['fond']);
						if ($data_battle['blood']>0) { 	 addlog($data_battle['id'],get_text_travm($data_battle)); }
						addlog($data_battle['id'],get_text_broken($data_battle));

						if ($hp==1)
						{
							//обнуление хп всем
							mysql_query("UPDATE users set hp=0 where last_battle='{$data_battle['id']}' and battle_t!=3");
							if (mysql_affected_rows()>0)
							{
								err('Всем выставленно 0 hp проигравшим!<br>');
							}
						}

						err('Бой id-'.$data_battle['id'].' окончен - Победа 3й команды!');
					}
					else
					{
						err('Бой не окончен ошибка блокировки');
					}

				}
				else
				{
					err('Ошибка 1');
				}



			}
			else
			{
				err('Бой в блокировке!');
			}

		}
		else
		{
			err('Бой уже окончен!');
		}

	}
	else
	{
		err('Бой не найден!');
	}

}

?>
</BODY>
</HTML>