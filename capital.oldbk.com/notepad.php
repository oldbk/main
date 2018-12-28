<?
	session_start();
	if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }
	include "connect.php";
	include "functions.php";

	$notepad_lenght=500;
	
	if ($user[prem]==1)
		{
		$notepad_lenght+=500;
		}
	elseif ($user[prem]==2)
		{
		$notepad_lenght+=1000;
		}
	elseif ($user[prem]==3)
		{
		$notepad_lenght+=1500;
		}		

?>



	<HTML><HEAD>
	<link rel=stylesheet type="text/css" href="i/main.css">
	<meta content="text/html; charset=windows-1251" http-equiv=Content-type>
	<META Http-Equiv=Cache-Control Content="no-cache, max-age=0, must-revalidate, no-store">
	<meta http-equiv=PRAGMA content=NO-CACHE>
	<META Http-Equiv=Expires Content=0>
	<script type="text/javascript" src="/i/globaljs.js"></script>
	<style>
		.row {
			cursor:pointer;
		}
	</style>
	</HEAD>
	<body leftmargin=5 topmargin=5 marginwidth=0 marginheight=0 bgcolor=#e2e0e0><div id=hint3 class=ahint></div>
	<table width=100%>
	<tr><td align=middle width=100%>
		<table>
		<tr><td>
		<?php
		//if($_POST['text'] && $_POST['post_mess'])
		if($_POST['post_mess'])
		    {
                        $sql='';
                        $delmess=mysql_fetch_array(mysql_query('Select * FROM oldbk.`users_notepad` WHERE type=0 and owner = '.$user['id'].' limit 1;'));
                        if($delmess[id]>0)
                        {                           $sql=', id='.$delmess[id];                        }
			        	//добавляем

	        		$text1=$_POST['note_text'];

				if(strlen($text1)>$notepad_lenght)
				{
					echo '<font color=red><b>Сообщение слишком длинное... НЕ сохранено. (укоротите сообщение)</b></font>';
				}
				else
				{
					
					  $text1 = str_replace("\r","",$text1);
                          		  $sql='Insert into oldbk.`users_notepad`
			         	  set owner = '.$user['id'].', txt="'.mysql_real_escape_string($text1).'",type=0, author="'.$user[id].'" '.$sql.'
			         	  ON DUPLICATE KEY UPDATE txt="'.mysql_real_escape_string($text1).'"';
			         	  mysql_query($sql);
			         	  echo '<font color=red><b>Сохранено!</b></font>';
			        }
		    }

				$data=mysql_query('Select * FROM oldbk.`users_notepad` WHERE type=0 and owner = '.$user['id'].' LIMIT 1;');
                		if(mysql_affected_rows()>0)
				{
					while($row=mysql_fetch_array($data))
					{
		                		$txt=$row['txt'];
					}
					if(strlen($text1)>1)
					{
						$txt=$text1;
					}
				}
				$txt = htmlspecialchars($txt,ENT_QUOTES);
				echo "<BR><fieldset ><legend><b>Блокнот: (<span id='txtlen'>".strlen($txt)."</span>/".$notepad_lenght.")</b></legend>";
				echo '<form  action="?" method="post">';
				echo '<b>Добавить/редактировать сообщение </b> <small>не более '.$notepad_lenght.' знаков.</small><br>';
				echo '<textarea onkeyup="OnTxtChange();" id="txtdata" name="note_text" rows=8 cols=85 wrap="on" maxlength="'.$notepad_lenght.'">'.$txt.'</textarea><br>';
				echo '<input type="submit" name="post_mess" value="Сохранить">';
		        echo '</form>';

		        echo "</fieldset>";
?>

	 <script>
		function OnTxtChange() {
			len = document.getElementById('txtdata').value.replace(/\r/g,"").length;
			document.getElementById("txtlen").innerHTML = len;
        	}
    </script>
			</td>
		</tr>
	</table>
			</td>
		</tr>
	</table>