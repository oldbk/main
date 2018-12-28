<?php
die();
session_start();
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }

	include "connect.php";
	include "functions.php";
		
		if ($user[room]!=44)
		{
		die("Работает только в тестовой комнате!");
		}
		

echo "<hr>";
if ($_POST[save])
	{
unset($_POST[save]);
$save_string='';	
	foreach ($_POST as $pname => $pval)
		{
		$pval=floatval($pval);
		if (($pname!='') and ($pval>0))
			{
			//echo $pname."::".$pval."<br>";
			$save_string.=$pname."::".$pval."\n";
			}
			else
			{
			echo "<b>Ошибка в  параметре:". $pname."::".$pval."</b><br>";
			$dont_save=true;
			}
		
		}

	if (!($dont_save))
	{
		
		$f = fopen('params.txt','w');
		fwrite($f,$save_string);
		fclose($f); 
	}

	}

///load params
	$file_params= file ('params.txt');
	foreach ($file_params as $line_num => $line)
	{
		 $par=explode("::",$line);
		 if (($par[0]!='') and ($par[1]!=''))
		 {
		 $PARAMS[$par[0]]=floatval($par[1]);
		 }
	}

?>
<form name=""  method="post">
<fieldset><legend>Праметры бонусов на Силу (% действует на силу удара)</legend>
Сила 25:<input name="sila25" type="text" value="<?=$PARAMS[sila25]?>"><br>
Сила 50:<input name="sila50" type="text" value="<?=$PARAMS[sila50]?>"><br>
Сила 75:<input name="sila75" type="text" value="<?=$PARAMS[sila75]?>"><br>
Сила 100:<input name="sila100" type="text" value="<?=$PARAMS[sila100]?>"><br>
Сила 125:<input name="sila125" type="text" value="<?=$PARAMS[sila125]?>"><br>
Сила 150:<input name="sila150" type="text" value="<?=$PARAMS[sila150]?>"><br>
Сила 175:<input name="sila175" type="text" value="<?=$PARAMS[sila175]?>"><br>
Сила 200:<input name="sila200" type="text" value="<?=$PARAMS[sila200]?>"><br>
Сила 225:<input name="sila225" type="text" value="<?=$PARAMS[sila225]?>"><br>
Сила 250:<input name="sila250" type="text" value="<?=$PARAMS[sila250]?>"><br>
Сила 275:<input name="sila275" type="text" value="<?=$PARAMS[sila275]?>"><br>
Сила 300:<input name="sila300" type="text" value="<?=$PARAMS[sila300]?>"><br>
</fieldset>
<fieldset><legend>Праметры бонусов на Ловкость (% действует на уворот)</legend>
Ловкость 25:<input name="lovk25" type="text" value="<?=$PARAMS[lovk25]?>"><br>
Ловкость 50:<input name="lovk50" type="text" value="<?=$PARAMS[lovk50]?>"><br>
Ловкость 75:<input name="lovk75" type="text" value="<?=$PARAMS[lovk75]?>"><br>
Ловкость 100:<input name="lovk100" type="text" value="<?=$PARAMS[lovk100]?>"><br>
Ловкость 125:<input name="lovk125" type="text" value="<?=$PARAMS[lovk125]?>"><br>
Ловкость 150:<input name="lovk150" type="text" value="<?=$PARAMS[lovk150]?>"><br>
Ловкость 175:<input name="lovk175" type="text" value="<?=$PARAMS[lovk175]?>"><br>
Ловкость 200:<input name="lovk200" type="text" value="<?=$PARAMS[lovk200]?>"><br>
Ловкость 225:<input name="lovk225" type="text" value="<?=$PARAMS[lovk225]?>"><br>
Ловкость 250:<input name="lovk250" type="text" value="<?=$PARAMS[lovk250]?>"><br>
Ловкость 275:<input name="lovk275" type="text" value="<?=$PARAMS[lovk275]?>"><br>
Ловкость 300:<input name="lovk300" type="text" value="<?=$PARAMS[lovk300]?>"><br>
</fieldset>

<fieldset><legend>Праметры бонусов на Интуицию (% действует на крит)</legend>
Интуиция 25:<input name="inta25" type="text" value="<?=$PARAMS[inta25]?>"><br>
Интуиция 50:<input name="inta50" type="text" value="<?=$PARAMS[inta50]?>"><br>
Интуиция 75:<input name="inta75" type="text" value="<?=$PARAMS[inta75]?>"><br>
Интуиция 100:<input name="inta100" type="text" value="<?=$PARAMS[inta100]?>"><br>
Интуиция 125:<input name="inta125" type="text" value="<?=$PARAMS[inta125]?>"><br>
Интуиция 150:<input name="inta150" type="text" value="<?=$PARAMS[inta150]?>"><br>
Интуиция 175:<input name="inta175" type="text" value="<?=$PARAMS[inta175]?>"><br>
Интуиция 200:<input name="inta200" type="text" value="<?=$PARAMS[inta200]?>"><br>
Интуиция 225:<input name="inta225" type="text" value="<?=$PARAMS[inta225]?>"><br>
Интуиция 250:<input name="inta250" type="text" value="<?=$PARAMS[inta250]?>"><br>
Интуиция 275:<input name="inta275" type="text" value="<?=$PARAMS[inta275]?>"><br>
Интуиция 300:<input name="inta300" type="text" value="<?=$PARAMS[inta300]?>"><br>
</fieldset>


<fieldset><legend>Праметры бонусов на Выносливость (% действует на броню)</legend>
Выносливость 25:<input name="vinos25" type="text" value="<?=$PARAMS[vinos25]?>"><br>
Выносливость 50:<input name="vinos50" type="text" value="<?=$PARAMS[vinos50]?>"><br>
Выносливость 75:<input name="vinos75" type="text" value="<?=$PARAMS[vinos75]?>"><br>
Выносливость 100:<input name="vinos100" type="text" value="<?=$PARAMS[vinos100]?>"><br>
Выносливость 125:<input name="vinos125" type="text" value="<?=$PARAMS[vinos125]?>"><br>
Выносливость 150:<input name="vinos150" type="text" value="<?=$PARAMS[vinos150]?>"><br>
Выносливость 175:<input name="vinos175" type="text" value="<?=$PARAMS[vinos175]?>"><br>
Выносливость 200:<input name="vinos200" type="text" value="<?=$PARAMS[vinos200]?>"><br>
Выносливость 225:<input name="vinos225" type="text" value="<?=$PARAMS[vinos225]?>"><br>
Выносливость 250:<input name="vinos250" type="text" value="<?=$PARAMS[vinos250]?>"><br>
Выносливость 275:<input name="vinos275" type="text" value="<?=$PARAMS[vinos275]?>"><br>
Выносливость 300:<input name="vinos300" type="text" value="<?=$PARAMS[vinos300]?>"><br>
</fieldset>


<input name="save" type="submit" value="СОХРАНИТЬ!!!">
</form>



