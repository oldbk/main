<?
//Настройки покупки артов для старъевщика
$prsale[210]=0.6; //Щит Пророчества
$prsale[209]=0.6;//Щит Откровения
$prsale[208]=0.5;//Шлем Ангела
$prsale[206]=1;//Топор Вихря
$prsale[205]=0.6;//Панцирь Злости
$prsale[204]=1; //Меч Кромуса
$prsale[203]=1;//Меч Героев
$prsale[202]=0.5; //Лучшие Ботинки
$prsale[201]=0.5; //Кольцо Жизни
$prsale[200]=0.5; //Закрытый шлем Развития
$prsale[199]=1;//Дубинка Радости
$prsale[198]=0.6;//Доспех Хаоса
$prsale[197]=0.6; //Доспех -Броня Титанов-
$prsale[196]=0.6; //Броня Ангела
$prsale[195]=0.5; //Великое Кольцо Жизни
/////////////////////////////
$sc_name[15561]='Осколок статуи Мусорщика';    $sc_kol[15561]=10;
$sc_name[15562]='Осколок статуи Мироздателя';   $sc_kol[15562]=10;
$sc_name[15563]='Осколок статуи Удачи';    $sc_kol[15563]=10;
$sc_name[15564]='Осколок статуи Исчадия Хаоса'; $sc_kol[15564]=10;
$sc_name[15565]='Осколок статуи Духа Форума';     $sc_kol[15565]=10;
$sc_name[15566]='Осколок статуи Пятницы';      $sc_kol[15566]=10;
$sc_name[15567]='Осколок статуи Лорда Разрушителя';   $sc_kol[15567]=10;
$sc_name[15568]='Осколок статуи Хранителя';   $sc_kol[15568]=10;

$sc_name[15551]='Статуя Мусорщика';    $sc_kol[15551]=5;
$sc_name[15552]='Статуя Мироздателя';   $sc_kol[15552]=5;
$sc_name[15553]='Статуя Удачи';    $sc_kol[15553]=5;
$sc_name[15554]='Статуя Исчадия Хаоса'; $sc_kol[15554]=5;
$sc_name[15555]='Статуя Духа Форума';     $sc_kol[15555]=5;
$sc_name[15556]='Статуя Пятницы';      $sc_kol[15556]=5;
$sc_name[15557]='Статуя Лорда Разрушителя';   $sc_kol[15557]=5;
$sc_name[15558]='Статуя Хранителя';   $sc_kol[15558]=5;

$sc_name[56661]='Cвиток Чарования [I]'; 
$sc_name[56662]='Cвиток Чарования [II]'; 
$sc_name[56663]='Cвиток Чарования [III]'; 


function print_my_cards($s,$f)
{
global $user;
global $dial, $T_BOT ;
$need=3;

	$get_list=mysql_query("select * from oldbk.inventory where (prototype>=".$s." and prototype<=".$f.")  and owner='{$user[id]}' and (sowner=0 or sowner='{$user[id]}') and setsale=0 order by prototype ");


$kol=mysql_num_rows($get_list);
	 if ($kol >=$need)
	 {
	 echo '
	 <script>
		function SetKol(v){
		kol=document.getElementById("kol").value;
		if (v==true)		
		 	{
		 	kol++;
		 	}
		 	else
		 	{
		 	kol--;
		 	}
      		document.getElementById("kol").value=kol;
		if (kol=='.$need.') 
			{
	      		document.getElementById("sendscroll").disabled=false;
			}     		
			else
			{
      			document.getElementById("sendscroll").disabled=true;			
			}
		if (kol>'.$need.') 
		        {
		        alert("Вы уже выбрали больше '.$need.' карт! ");
		        }
		}
	</script>';
	echo ' <TABLE BORDER=0 WIDTH=100% CELLSPACING="1" CELLPADDING="2" BGCOLOR="#A5A5A5">';	 
	echo '</form>';
	echo "<form method=post>";
	echo "<input type=hidden name=talk value=\"".$T_BOT."\">";	 
	echo "<input type=hidden name=dial value=\"".$dial."\">";	 
	echo "<input type=hidden id=kol name=kol value='' >";	
	 while ($result = mysql_fetch_array($get_list)) 
 		{
 	//$result[count]=1;
 	  $result[GetShopCount()] = 1;
 	  
			$result[no]=1;
			if ($i==0) { $i = 1; $color = '#C7C7C7';} else { $i = 0; $color = '#D5D5D5';}
			echo "<TR bgcolor={$color}><TD align=center style='width:150px'><IMG SRC=\"http://i.oldbk.com/i/sh/{$result['img']}\" BORDER=0>";
			echo "<br><input name=\"scrol[".$result[id]."]\" type=\"checkbox\" value=\"1\" onClick=\"SetKol(this.checked)\"  />Выбрать для обмена";
			echo "</TD>";
			echo "<TD valign=top>";
			showitem ($result);
			echo "</TD></TR>";
 		}
	echo "<TR><TD valign=top colspan=2 align=center>";			
	echo "<input type=submit id='sendscroll' name='sendscroll' value='Обменять' disabled=\"disabled\" >";
	echo "</TR>";
	echo "</form>";	
	echo "</table>"; 	
	echo "<form method=post>";	
 	 }
 	 else
 	 if ($kol >0)
 	 {
 	 echo "У вас не хватает карт. Необходимо <b>$need</b> карты.";
 	 }
 	 else
 	 {
 	 echo " У вас нет необходимых карт";
 	 }
}


function print_my_charks($need,$prot)
{
global $user;
global $dial, $T_BOT ;

	$get_list=mysql_query("select * from oldbk.inventory where prototype =".$prot." and owner='{$user[id]}' and (sowner=0 or sowner='{$user[id]}') and setsale=0 order by prototype ");


$kol=mysql_num_rows($get_list);
	 if ($kol >=$need)
	 {
	 echo '
	 <script>
		function SetKol(v){
		kol=document.getElementById("kol").value;
		if (v==true)		
		 	{
		 	kol++;
		 	}
		 	else
		 	{
		 	kol--;
		 	}
      		document.getElementById("kol").value=kol;
		if (kol=='.$need.') 
			{
	      		document.getElementById("sendscroll").disabled=false;
			}     		
			else
			{
      			document.getElementById("sendscroll").disabled=true;			
			}
		if (kol>'.$need.') 
		        {
		        alert("Вы уже выбрали больше '.$need.' чарок! ");
		        }
		}
	</script>';
	echo ' <TABLE BORDER=0 WIDTH=100% CELLSPACING="1" CELLPADDING="2" BGCOLOR="#A5A5A5">';	 
	echo '</form>';
	echo "<form method=post>";
	echo "<input type=hidden name=talk value=\"".$T_BOT."\">";	 
	echo "<input type=hidden name=dial value=\"".$dial."\">";	 
	echo "<input type=hidden id=kol name=kol value='' >";	
	 while ($result = mysql_fetch_array($get_list)) 
 		{
 	//$result[count]=1;
 	  $result[GetShopCount()] = 1;
 	  
			$result[no]=1;
			if ($i==0) { $i = 1; $color = '#C7C7C7';} else { $i = 0; $color = '#D5D5D5';}
			echo "<TR bgcolor={$color}><TD align=center style='width:150px'><IMG SRC=\"http://i.oldbk.com/i/sh/{$result['img']}\" BORDER=0>";
			echo "<br><input name=\"scrol[".$result[id]."]\" type=\"checkbox\" value=\"1\" onClick=\"SetKol(this.checked)\"  />Выбрать для обмена";
			echo "</TD>";
			echo "<TD valign=top>";
			showitem ($result);
			echo "</TD></TR>";
 		}
	echo "<TR><TD valign=top colspan=2 align=center>";			
	echo "<input type=submit id='sendscroll' name='sendscroll' value='Обменять' disabled=\"disabled\" >";
	echo "</TR>";
	echo "</form>";	
	echo "</table>"; 	
	echo "<form method=post>";	
 	 }
 	 else
 	 if ($kol >0)
 	 {
 	 echo "У вас не хватает чарок. Необходимо <b>$need</b> чарки.";
 	 }
 	 else
 	 {
 	 echo " У вас нет необходимых чарок";
 	 }
}


function print_my_scrols($need,$toget=0)
{
global $user;
global $dial, $T_BOT ;

	if ($toget>0)
	{
	$toget+=10; // для соответствия статуй к их осколкам
	$get_list=mysql_query("select * from oldbk.inventory where prototype ='{$toget}' and owner={$user[id]} and setsale=0");	
	}
	else
	{
	$get_list=mysql_query("select * from oldbk.inventory where prototype >=15561 and prototype <=15568 and owner={$user[id]} and setsale=0 order by prototype ");
	}

$kol=mysql_num_rows($get_list);
	 if ($kol >=$need)
	 {
	 echo '
	 <script>
		function SetKol(v){
		kol=document.getElementById("kol").value;
		if (v==true)		
		 	{
		 	kol++;
		 	}
		 	else
		 	{
		 	kol--;
		 	}
      		document.getElementById("kol").value=kol;
		if (kol=='.$need.') 
			{
	      		document.getElementById("sendscroll").disabled=false;
			}     		
			else
			{
      			document.getElementById("sendscroll").disabled=true;			
			}
		if (kol>'.$need.') 
		        {
		        alert("Вы уже выбрали больше '.$need.' свитков! ");
		        }
		}
	</script>';
	echo ' <TABLE BORDER=0 WIDTH=100% CELLSPACING="1" CELLPADDING="2" BGCOLOR="#A5A5A5">';	 
	echo '</form>';
	echo "<form method=post>";
	echo "<input type=hidden name=talk value=\"".$T_BOT."\">";	 
	echo "<input type=hidden name=dial value=\"".$dial."\">";	 
	echo "<input type=hidden id=kol name=kol value='' >";	
	 while ($result = mysql_fetch_array($get_list)) 
 		{
 	//$result[count]=1;
 	  $result[GetShopCount()] = 1;
 	  
			$result[no]=1;
			if ($i==0) { $i = 1; $color = '#C7C7C7';} else { $i = 0; $color = '#D5D5D5';}
			echo "<TR bgcolor={$color}><TD align=center style='width:150px'><IMG SRC=\"http://i.oldbk.com/i/sh/{$result['img']}\" BORDER=0>";
			echo "<br><input name=\"scrol[".$result[id]."]\" type=\"checkbox\" value=\"1\" onClick=\"SetKol(this.checked)\"  />Выбрать для обмена";
			echo "</TD>";
			echo "<TD valign=top>";
			showitem ($result);
			echo "</TD></TR>";
 		}
	echo "<TR><TD valign=top colspan=2 align=center>";			
	echo "<input type=submit id='sendscroll' name='sendscroll' value='Обменять' disabled=\"disabled\" >";
	echo "</TR>";
	echo "</form>";	
	echo "</table>"; 	
	echo "<form method=post>";	
 	 }
 	 else
 	 if ($kol >0)
 	 {
 	 echo "У вас не хватает осколков. Необходимо <b>$need</b> осколков.";
 	 }
 	 else
 	 {
 	 echo " У вас нет необходимых осколков";
 	 }
}


function present_scroll($proto)
{
global $user;
$needpres='Лабиринт';
 $dress = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`eshop` WHERE `id` = '{$proto}' LIMIT 1;"));
 mysql_query("INSERT INTO oldbk.`inventory`
				(`prototype`,`owner`,`name`,`type`,`massa`,`cost`,`img`,`maxdur`,`isrep`,
					`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,`needident`,`nsila`,`nlovk`,`ninta`,`nintel`,`nmudra`,`nvinos`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,
					`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`bron1`,`bron2`,`bron3`,`bron4`,`maxu`,`minu`,`magic`,`nlevel`,`nalign`,`dategoden`,`goden`,`nsex`,`otdel`,`labonly`,`labflag`,`present`,`group`,`letter`,`sowner`
				)
				VALUES
				('{$dress['id']}','{$user['id']}','{$dress['name']}','{$dress['type']}',{$dress['massa']},{$dress['cost']},'{$dress['img']}',{$dress['maxdur']},{$dress['isrep']},'{$dress['gsila']}','{$dress['glovk']}','{$dress['ginta']}','{$dress['gintel']}','{$dress['ghp']}','{$dress['gnoj']}','{$dress['gtopor']}','{$dress['gdubina']}','{$dress['gmech']}','{$dress['gfire']}','{$dress['gwater']}','{$dress['gair']}','{$dress['gearth']}','{$dress['glight']}','{$dress['ggray']}','{$dress['gdark']}','{$dress['needident']}','{$dress['nsila']}','{$dress['nlovk']}','{$dress['ninta']}','{$dress['nintel']}','{$dress['nmudra']}','{$dress['nvinos']}','{$dress['nnoj']}','{$dress['ntopor']}','{$dress['ndubina']}','{$dress['nmech']}','{$dress['nfire']}','{$dress['nwater']}','{$dress['nair']}','{$dress['nearth']}','{$dress['nlight']}','{$dress['ngray']}','{$dress['ndark']}',
				'{$dress['mfkrit']}','{$dress['mfakrit']}','{$dress['mfuvorot']}','{$dress['mfauvorot']}','{$dress['bron1']}','{$dress['bron2']}','{$dress['bron3']}','{$dress['bron4']}','{$dress['maxu']}','{$dress['minu']}','{$dress['magic']}','{$dress['nlevel']}','{$dress['nalign']}','".(($dress['goden'])?($dress['goden']*24*60*60+time()):"")."','{$dress['goden']}','{$dress['nsex']}','{$dress['razdel']}','0','0','{$needpres}','{$dress['group']}','{$dress['letter']}','0'
				) ;");		
$dress[id]=mysql_insert_id();
return $dress;
}

function present_charka($proto)
{
global $user;
$needpres='Лабиринт';

 $dress = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`shop` WHERE `id` = '{$proto}' LIMIT 1;"));
 mysql_query("INSERT INTO oldbk.`inventory`
				(`prototype`,`owner`,`name`,`type`,`massa`,`cost`,`img`,`maxdur`,`isrep`,
					`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,`needident`,`nsila`,`nlovk`,`ninta`,`nintel`,`nmudra`,`nvinos`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,
					`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`bron1`,`bron2`,`bron3`,`bron4`,`maxu`,`minu`,`magic`,`nlevel`,`nalign`,`dategoden`,`goden`,`nsex`,`otdel`,`labonly`,`labflag`,`present`,`group`,`letter`,`sowner`
				)
				VALUES
				('{$dress['id']}','{$user['id']}','{$dress['name']}','{$dress['type']}',{$dress['massa']},{$dress['cost']},'{$dress['img']}',{$dress['maxdur']},{$dress['isrep']},'{$dress['gsila']}','{$dress['glovk']}','{$dress['ginta']}','{$dress['gintel']}','{$dress['ghp']}','{$dress['gnoj']}','{$dress['gtopor']}','{$dress['gdubina']}','{$dress['gmech']}','{$dress['gfire']}','{$dress['gwater']}','{$dress['gair']}','{$dress['gearth']}','{$dress['glight']}','{$dress['ggray']}','{$dress['gdark']}','{$dress['needident']}','{$dress['nsila']}','{$dress['nlovk']}','{$dress['ninta']}','{$dress['nintel']}','{$dress['nmudra']}','{$dress['nvinos']}','{$dress['nnoj']}','{$dress['ntopor']}','{$dress['ndubina']}','{$dress['nmech']}','{$dress['nfire']}','{$dress['nwater']}','{$dress['nair']}','{$dress['nearth']}','{$dress['nlight']}','{$dress['ngray']}','{$dress['ndark']}',
				'{$dress['mfkrit']}','{$dress['mfakrit']}','{$dress['mfuvorot']}','{$dress['mfauvorot']}','{$dress['bron1']}','{$dress['bron2']}','{$dress['bron3']}','{$dress['bron4']}','{$dress['maxu']}','{$dress['minu']}','{$dress['magic']}','{$dress['nlevel']}','{$dress['nalign']}','".(($dress['goden'])?($dress['goden']*24*60*60+time()):"")."','{$dress['goden']}','{$dress['nsex']}','{$dress['razdel']}','0','0','{$needpres}','{$dress['group']}','{$dress['letter']}','{$user['id']}'
				) ;");		
$dress[id]=mysql_insert_id();
return $dress;
}

if ($OPEN_FROM_LAB==TRUE)
{

if ($user[id]==14897)
{
print_r($_POST);
print_r($_POST[scrol]);
}
	
 	$dial=(int)($_REQUEST[dial]);

	require_once('fsystem.php');
	///end functions//////////////////////////////////////

//загружаем мои шмотки все кроме магий они подарков и всякой херни надо добавить
// загруженный масив буит нужен для расчетов и отображения
$my_wearItems=load_mass_items_by_id($user); // загрузка
$my_magicItems=$my_wearItems[incmagic]; // распаковка магии
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		if ((int)($T_BOT)==0)
		{
		$T_BOT=75;
		}
	$nexten= mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `id` ='{$T_BOT}' LIMIT 1;"));
    	$en_wearItems=load_mass_items_by_id($nexten); // загрузка
    	$en_magicItems=$en_wearItems[incmagic];
//////////////==================HTMLs
?>
<HTML>
<HEAD>
<link rel=stylesheet type="text/css" href="http://i.oldbk.com/i/main.css">
<meta content="text/html; charset=windows-1251" http-equiv=Content-type>
<META Http-Equiv=Cache-Control Content=no-cache>
<meta http-equiv=PRAGMA content=NO-CACHE>
<META Http-Equiv=Expires Content=0>
<script type="text/javascript" src="http://i.oldbk.com/i/popup/ZeroClipboard.js"></script>
<SCRIPT LANGUAGE="JavaScript" SRC="http://i.oldbk.com/i/sl2.js"></SCRIPT>
<SCRIPT LANGUAGE="JavaScript" SRC="http://i.oldbk.com/i/ch.js"></SCRIPT>
<script type="text/javascript" src="/i/globaljs.js"></script>

<SCRIPT>
var Hint3Name = '';

function setDEF()
{
if (document.getElementById('fdefend') !=null )
	{
	document.getElementById('fdefend').value=document.getElementById('txtblockzone').value;
	}

if (document.getElementById('fenemy') !=null )
	{
	document.getElementById('fenemy').value=document.getElementById('penemy').value;
	}
}
// Заголовок, название скрипта, имя поля с логином
function findlogin(title, script, name){
	document.all("hint3").innerHTML = '<form action="'+script+'" method=POST><table width=100% cellspacing=1 cellpadding=0 bgcolor=CCC3AA><tr><td align=center><B>'+title+'</td><td width=20 align=right valign=top style="cursor: pointer" onclick="closehint3();"><BIG><B>x</td></tr><tr><td colspan=2>'+
	'<table width=100% cellspacing=0 cellpadding=2 bgcolor=FFF6DD><tr><INPUT TYPE=hidden name=sd4 value="6"><td colspan=2>'+
	'Укажите логин персонажа:<small><BR>(можно щелкнуть по логину в чате)</TD></TR><TR><TD width=50% align=right><INPUT type=hidden id=fenemy name=enemy> <INPUT type=hidden id=fdefend name=defend><INPUT TYPE=text NAME="'+name+'"></TD><TD width=50%><INPUT TYPE="submit" value=" »» "></TD></TR></TABLE></td></tr></table></form>';
	document.all("hint3").style.visibility = "visible";
	document.all("hint3").style.left = 100;
	document.all("hint3").style.top = 100;
	setDEF();
	document.all(name).focus();
	Hint3Name = name;
}


function comment_fight(title, script, name){
	document.all("hint3").innerHTML = '<form action="'+script+'" method=POST><table width=100% cellspacing=1 cellpadding=0 bgcolor=CCC3AA><tr><td align=center><B>'+title+'</td><td width=20 align=right valign=top style="cursor: pointer" onclick="closehint3();"><BIG><B>x</td></tr><tr><td colspan=2>'+
	'<table width=100% cellspacing=0 cellpadding=2 bgcolor=FFF6DD><tr><form action="'+script+'" method=POST><INPUT TYPE=hidden name=sd4 value="6"><td colspan=2>'+
	'Введите текст комментария:<small><BR>(смайлики работают)</TD></TR><TR><TD width=80% align=right><INPUT TYPE=text NAME="'+name+'"></TD><TD width=20%><INPUT TYPE="submit" value=" »» "></TD></TR></TABLE></td></tr></table></form>';
	document.all("hint3").style.visibility = "visible";
	document.all("hint3").style.left = 100;
	document.all("hint3").style.top = 100;
	document.all(name).focus();
	Hint3Name = name;
}

function okno(title, script, name,coma){
	document.all("hint3").innerHTML = '<form action="'+script+'" method=POST><table width=100% cellspacing=1 cellpadding=0 bgcolor=CCC3AA><tr><td align=center><B>'+title+'</td><td width=20 align=right valign=top style="cursor: pointer" onclick="closehint3();"><BIG><B>x</td></tr><tr><td colspan=2>'+
	'<table width=100% cellspacing=0 cellpadding=2 bgcolor=FFF6DD><tr><INPUT TYPE=hidden name=sd4 value="6"><td colspan=2>'+
	'введите название предмета</TD></TR><TR><TD width=50% align=right><INPUT TYPE=text NAME="'+name+'"></TD><TD width=50%><INPUT TYPE="submit" value=" »» "></TD></TR></TABLE></td></tr></table></form>';
	document.all("hint3").style.visibility = "visible";
	document.all("hint3").style.left = 100;
	document.all("hint3").style.top = 100;
	document.all(name).focus();
	Hint3Name = name;
}

var attack=false;
var defend=false;

function check(f,r)
{
   if (((! attack) || (! defend)) && (r!=1))
   {
   		alert('Блок или удар не выбран.');
   		return false;
   }
   else
   {
	   	if (r=='1' && ((! attack) || (! defend)))
	   	{
	   		return false;
	   	}
        else
        {
   			f.go.disabled = 1;
   			f.submit();
   			return true;
   		}
   }
}
function Prv(logins)
{
	top.frames['bottom'].window.document.F1.text.focus();
	top.frames['bottom'].document.forms[0].text.value = logins + top.frames['bottom'].document.forms[0].text.value;
}
function setattack(f) {
	attack=true;
	if(f){
		check(f,1);
	}
}
function setdefend(zone,f)
{
	defend=true
	document.getElementById('txtblockzone').value = zone;
	setDEF();
	if(f){
		check(f,1);
	}
}

function defPosition(event) {
      var x = y = 0;
      if (document.attachEvent != null) { // Internet Explorer & Opera
            x = window.event.clientX + (document.documentElement.scrollLeft ? document.documentElement.scrollLeft : document.body.scrollLeft);
            y = window.event.clientY + (document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop);
			if (window.event.clientY + 72 > document.body.clientHeight) { y-=38 } else { y-=2 }
      } else if (!document.attachEvent && document.addEventListener) { // Gecko
            x = event.clientX + window.scrollX;
            y = event.clientY + window.scrollY;
			if (event.clientY + 72 > document.body.clientHeight) { y-=38 } else { y-=2 }
      } else {
            // Do nothing
      }
      return {x:x, y:y};
}

function OpisShmot(evt,s){
	menu=document.createElement("div");
	menu.style.border='1px solid black';
	menu.innerHTML = s;
	menu.id='ShowInfoShmot';
	menu.style.background='#FFFFE1';
	menu.style.fontsize='8px';
	menu.style.position='absolute';
    menu.style.top = defPosition(evt).y + "px";
    menu.style.left = defPosition(evt).x + "px";

	showSH=setTimeout(function(){
					document.body.appendChild(menu);
			   }, 1000);
}

function HideOpisShmot(){
	try{
		ids=document.getElementById('ShowInfoShmot');
		ids.parentNode.removeChild(ids);
	}
	catch (err){
		clearTimeout(showSH);
	}
}


</SCRIPT>
<script>
			function refreshPeriodic()
			{
				<?
			//	if($data_battle[status] == 0)
			//	{
			//	echo "location.href='".$_SERVER['PHP_SELF']."?batl=".$_REQUEST['batl']."';//reload();";
			//	}
				?>
				timerID=setTimeout("refreshPeriodic()",30000);
			}
			timerID=setTimeout("refreshPeriodic()",30000);
</script>
<style type="text/css">
.menu {
  background-color: #d2d0d0;
  border-color: #ffffff #626060 #626060 #ffffff;
  border-style: solid;
  border-width: 1px;
  position: absolute;
  left: 0px;
  top: 0px;
  visibility: hidden;
}

a.menuItem {
  border: 0px solid #000000;
  color: #003388;
  display: block;
  font-family: MS Sans Serif, Arial, Tahoma,sans-serif;
  font-size: 8pt;
  font-weight: bold;
  padding: 2px 12px 2px 8px;
  text-decoration: none;
}

a.menuItem:hover {
  background-color: #a2a2a2;
  color: #0066FF;
}
span {
  FONT-SIZE: 10pt;
  FONT-FAMILY: Verdana, Arial, Helvetica, Tahoma, sans-serif;
  text-decoration: none;
  FONT-WEIGHT: bold;
  cursor: pointer;
}
.my_clip_button {   border: 0px solid #000000;
  color: #003388;
  display: block;
  font-family: MS Sans Serif, Arial, Tahoma,sans-serif;
  font-size: 8pt;
  font-weight: bold;
  padding: 2px 12px 2px 8px;
  text-decoration: none; }
.my_clip_button.hover { background-color: #a2a2a2; color: #0066FF; }
</style>

</HEAD>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 bgcolor=e2e0e0 onLoad="top.setHP(<?=$user['hp']?>,<?=$user['maxhp']?>)">
<div id=hint3 class=ahint></div>
<FORM action="<?=$_SERVER['PHP_SELF']?>" method=POST>
<TABLE width=100% cellspacing=0 cellpadding=0 border=0>
<input type=hidden value='<?=($user['battle']?$user['battle']:$_REQUEST['batl'])?>' name=batl><input type=hidden value='<?=$enemy?>' name=enemy1>
<INPUT TYPE=hidden name=myid value="<?=time();?>">
<TR><TD valign=top>
<TABLE width=250 cellspacing=0 cellpadding=0><TR>
<TD valign=top width=250 nowrap><CENTER>

<?
showtelo($user,$my_wearItems,$my_magicItems);

$nazva_diala[75]='Разговор';
$nazva_diala[73]='Разговор';
?>

</TD></TR>
</TABLE>

</td>
<td  valign=top width=80%>

				<TABLE width=100% cellspacing=0 cellpadding=0><TR><TD colspan=2><h3><?=$nazva_diala[$T_BOT]?></TD></TR>
					<TR><TD><font color=660000><B>  </TD>
					<TD align=right>&nbsp;</TD>
				</TR></TABLE>

					<CENTER>
					<br>
				
					<?
					if  (($dial==0)and($T_BOT==73))
					{
					echo "
					<TABLE cellspacing=0 cellpadding=0 border=0 width=70%><TR><TD>
					<font style=\"COLOR:#8f0000;FONT-SIZE:10pt\">&nbsp;&nbsp;Приветствую тебя, ".$user['login']."!<br>
					&nbsp;&nbsp;Не ожидал я уже увидеть здесь живых людей. Признаться честно - удивлен.<br>
					&nbsp;&nbsp;Не смотри на мой дряхлый балахон и постаревший вид, я здесь неспроста.<br>
					&nbsp;&nbsp;Я могу обналичить любой чек, найденный тут.</font>
					<br><br><br></TD></TR>";
					echo "<TR><TD bgcolor=f2f0f0><a href=?talk=73&dial=2>Обналичить чек.</a><br></TD></TR>";					
					echo "<TR><TD bgcolor=f2f0f0><a href=?talk=73&dial=22>Обменять осколки статуй</a><br></TD></TR>";					
					//echo "<TR><TD bgcolor=f2f0f0><a href=?talk=73&dial=24>Обменять карты (три одинаковые карты, на одну другую бесплатно)</a><br></TD></TR>";																	
					echo "<TR><TD bgcolor=f2f0f0><a href=?talk=73&dial=25>Обменять карты (три любые карты, на одну другую за 100 кр)</a><br></TD></TR>";																						
					echo "<TR><TD bgcolor=f2f0f0><a href=?talk=73&dial=23>Собрать из осколков статую</a><br></TD></TR>";					
					echo "<TR><TD bgcolor=f2f0f0><a href=?talk=73&dial=41>Соединить чарки</a><br></TD></TR>";
					echo "<TR><TD bgcolor=f2f0f0><a href=?talk=73&dial=88>Спасибо тебе, Старьевщик, - я надеюсь, что еще встречусь с тобой.</a></TD></TR>";
					echo "</TABLE>";
					}
					else
					if  (($dial==0)and($T_BOT==75))
					{
					echo "
					<TABLE cellspacing=0 cellpadding=0 border=0 width=70%><TR><TD>
					<font style=\"COLOR:#8f0000;FONT-SIZE:10pt\">&nbsp;&nbsp;Приветствую тебя, ".$user['login']."!<br>
					&nbsp;&nbsp;Не ожидал я уже увидеть здесь живых людей. Признаться честно - удивлен.<br>
					&nbsp;&nbsp;Не смотри на мой дряхлый балахон и постаревший вид, я здесь неспроста. Наверняка, у тебя есть что-то ненужное из того, что ты нашел в Коридорах этого Лабиринта.<br>
					&nbsp;&nbsp;За символическую плату я готов выкупить все, чем ты богат.<br>
					&nbsp;&nbsp;Да, совсем забыл… (старость наверное) - я также могу  обналичить любой чек.</font>
					<br><br><br></TD></TR>";
					echo "<TR><TD bgcolor=f2f0f0><a href=?talk=75&dial=1>Предложить товар на продажу.</a><br></TD></TR>";
					echo "<TR><TD bgcolor=f2f0f0><a href=?talk=75&dial=2>Обналичить чек.</a><br></TD></TR>";	
					echo "<TR><TD bgcolor=f2f0f0><a href=?talk=75&dial=22>Обменять осколки статуй</a><br></TD></TR>";															
					//echo "<TR><TD bgcolor=f2f0f0><a href=?talk=75&dial=24>Обменять карты (три одинаковые карты, на одну другую бесплатно)</a><br></TD></TR>";																
					echo "<TR><TD bgcolor=f2f0f0><a href=?talk=75&dial=25>Обменять карты (три любые карты, на одну другую 100 кр)</a><br></TD></TR>";																					
					echo "<TR><TD bgcolor=f2f0f0><a href=?talk=75&dial=23>Собрать из осколков статую</a><br></TD></TR>";
					echo "<TR><TD bgcolor=f2f0f0><a href=?talk=75&dial=41>Соединить чарки</a><br></TD></TR>";
					echo "<TR><TD bgcolor=f2f0f0><a href=?talk=75&dial=88>Спасибо тебе, Старьевщик, - я надеюсь, что еще встречусь с тобой.</a></TD></TR>";
					echo "</TABLE>";
					}
					else
					if  (($dial==1)and($T_BOT==75))
					{
					$sale=(int)($_GET[sale]);
					if ($sale>0)
						{
						//проверяем подходимость предмета
					
						$sale_it=mysql_query("select * from oldbk.inventory where id='{$sale}' and  owner='{$user[id]}' and dressed=0 and prototype in (210,209,208,206,205,204,203,202,201,200,199,198,197,196,195) and labonly=1; ");
						
						if (mysql_num_rows($sale_it) ==1)
							{
							$sale_ita=mysql_fetch_array($sale_it);
							$add_cost=$prsale[$sale_ita[prototype]];
							if ((mysql_query("UPDATE `users` set money=money+".$add_cost." where id=75 or id='".$user['id']."'"))
							   AND (mysql_query("DELETE from oldbk.inventory where owner=".$user[id]." and id=".$sale_ita[id]." ; ")) )
								{
								$bot_message1='Отличная сделка, держи <b>'.$add_cost.' кр</b>';	
								$bot_message2='<br>Отличная сделка, держи <b>'.$add_cost.' кр</b> <br>Если будет еще - заходи...';	
								addchp ('<font color=red>Внимание!</font> Персонаж "'.$nexten['login'].'" передал вам <B>'.$add_cost.' кр</B>.   ','{[]}'.$user['login'].'{[]}',$user['room'],$user['id_city']);
								 
					//new_delo
  		    			$rec['owner']=$user[id]; 
					$rec['owner_login']=$user[login];
					$rec['owner_balans_do']=$user['money'];
					$rec['owner_balans_posle']=$user['money']+$add_cost;
					$rec['target']=$nexten['id'];
					$rec['target_login']=$nexten['login'];
					$rec['type']=12;//передача кредитов
					$rec['sum_kr']=$add_cost;
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
					add_to_new_delo($rec); //юзеру

								
								 if (olddelo==1)
								 {
								 mysql_query("INSERT INTO oldbk.`delo`(`id` , `author` ,`pers`, `text`, `type`, `date`) VALUES ('','0','{$user[id]}','Переведены кредиты ".$add_cost." от \"".$nexten['login']."\" к \"".$user['login']."\" (за ".$sale_ita[name]." в лабиринте) ','1','".time()."');");
								 }
								}
							}
							else
							{
								$bot_message1='Не дури меня!...';	
								$bot_message2='И не дури меня!...';
							}
						
						
						
						
						
						
						}
					$get_mcheck=mysql_query("select * from oldbk.inventory where owner='{$user[id]}' and prototype in (210,209,208,206,205,204,203,202,201,200,199,198,197,196,195) and labonly=1;");
					if (mysql_num_rows($get_mcheck) > 0)
						{
						echo "
					<TABLE cellspacing=0 cellpadding=0 border=0 width=70%><TR><TD>
					<font style=\"COLOR:#8f0000;FONT-SIZE:10pt\">&nbsp;&nbsp;-Давай-ка, посмотрим, что у тебя есть для меня...<br>".$bot_message1."</font>
					<br><br><br></TD></TR>";

						while ($mchrow = mysql_fetch_array($get_mcheck)) 
							{
							echo "<TR><TD bgcolor=f2f0f0><a href=?talk=75&dial=1&sale=".$mchrow[id].">Продать «".$mchrow[name]."» за ".$prsale[$mchrow[prototype]]."кр.</a></TD></TR>";
							}
						}
						else
						{
						echo "
					<TABLE cellspacing=0 cellpadding=0 border=0 width=70%><TR><TD>
					<font style=\"COLOR:#8f0000;FONT-SIZE:10pt\">&nbsp;&nbsp;-У тебя нет интересных для меня предметов!...".$bot_message2."<br></font>
					<br><br><br></TD></TR>";	
						}
					echo "<TR><TD bgcolor=f2f0f0><a href=?talk=".$T_BOT.">Я передумал, давай еще поговорим...</a></TD></TR>";						
					echo "<TR><TD bgcolor=f2f0f0><a href=?talk=75&dial=88>Спасибо тебе, Старьевщик, - я надеюсь, что еще встречусь с тобой.</a></TD></TR>";
					echo "</TABLE>";					
					}
					else
					if  (  ($dial==2) and (($T_BOT==75) OR($T_BOT==73))  )
					{
					$sale=(int)($_GET[sale]);
					if ($sale>0)
						{
						//проверяем наличие чека
						$sale_it=mysql_query("select * from oldbk.inventory where id='{$sale}' and  owner='{$user[id]}' and setsale=0 and prototype>3100 and prototype<3300; ");

						if (mysql_num_rows($sale_it) ==1)
							{
							$sale_ita=mysql_fetch_array($sale_it);
							if ((mysql_query("UPDATE `users` set money=money+".$sale_ita[cost]." where id='{$T_BOT}' or id='".$user['id']."'"))
							   AND (mysql_query("DELETE from oldbk.inventory where owner=".$user[id]." and id=".$sale_ita[id]." ; ")) )
								{
								$bot_message1='Отличная сделка, держи <b>'.$sale_ita[cost].' кр</b>';	
								$bot_message2=' еще ';
								addchp ('<font color=red>Внимание!</font> Персонаж "'.$nexten['login'].'" передал вам <B>'.$sale_ita[cost].' кр</B>.   ','{[]}'.$user['login'].'{[]}',$user['room'],$user['id_city']);
								//new_delo
  		    			$rec['owner']=$user[id]; 
					$rec['owner_login']=$user[login];
					$rec['owner_balans_do']=$user['money'];
					$rec['owner_balans_posle']=$user['money']+$sale_ita[cost];
					$rec['target']=$nexten['id'];
					$rec['target_login']=$nexten['login'];
					$rec['type']=13;//передача кредитов за чеки
					$rec['sum_kr']=$sale_ita[cost];
					$rec['sum_ekr']=0;
					$rec['sum_kom']=0;
					$rec['item_id']=get_item_fid($sale_ita);
					$rec['item_name']=$sale_ita[name];
					$rec['item_count']=1;
					$rec['item_type']=$sale_ita[type];
					$rec['item_cost']=$sale_ita[cost];
					$rec['item_dur']=$sale_ita[duration];
					$rec['item_maxdur']=$sale_ita[maxdur];
					$rec['item_ups']=$sale_ita[ups];
					$rec['item_unic']=$sale_ita[unic];
					$rec['item_incmagic']=$sale_ita[includemagicname];
					$rec['item_incmagic_count']=$sale_ita[includemagicuses];
					$rec['item_arsenal']='';
					add_to_new_delo($rec); //юзеру

								
								 if (olddelo==1)
								 {								
								  mysql_query("INSERT INTO oldbk.`delo`(`id` , `author` ,`pers`, `text`, `type`, `date`) VALUES ('','0','{$user[id]}','Переведены кредиты ".$sale_ita[cost]." от \"".$nexten['login']."\" к \"".$user['login']."\" (за чек в лабиринте) ','1','".time()."');");
								 }
								}
							}
							else
							{
								$bot_message1='Не дури меня!...';	
								$bot_message2='И не дури меня!...';
							}
						
						
						}
					$get_mcheck=mysql_query("select * from oldbk.inventory where owner='{$user[id]}' and setsale=0 and prototype>3100 and prototype<3300;");
					if (mysql_num_rows($get_mcheck) > 0)
						{
					echo "
					<TABLE cellspacing=0 cellpadding=0 border=0 width=70%><TR><TD>
					<font style=\"COLOR:#8f0000;FONT-SIZE:10pt\">&nbsp;&nbsp;-Давай меняться ты мне чек - я тебе деньги...<br>".$bot_message1."</font>
					<br><br><br></TD></TR>";
						
						while ($mchrow = mysql_fetch_array($get_mcheck)) 
							{
							echo "<TR><TD bgcolor=f2f0f0><a href=?talk=".$T_BOT."&dial=2&sale=".$mchrow[id].">Отдать ".$mchrow[name]."</a></TD></TR>";
							}
						}
					else  {
					echo "
					<TABLE cellspacing=0 cellpadding=0 border=0 width=70%><TR><TD>
					<font style=\"COLOR:#8f0000;FONT-SIZE:10pt\">&nbsp;&nbsp;-Да я могу поменять чеки. Приноси!...".$bot_message2."<br></font>
					<br><br><br></TD></TR>";					

						}
					if ($T_BOT==75) { echo "<TR><TD bgcolor=f2f0f0><a href=?talk=75&dial=1>У меня есть на продажу вещи.</a><br></TD></TR>"; }
					echo "<TR><TD bgcolor=f2f0f0><a href=?talk=".$T_BOT.">Я передумал, давай еще поговорим...</a></TD></TR>";
					echo "<TR><TD bgcolor=f2f0f0><a href=?talk=".$T_BOT."&dial=88>Спасибо тебе, Старьевщик, - я надеюсь, что еще встречусь с тобой.</a></TD></TR>";
					echo "</TABLE>";
					
					}
					else
					if  (($dial==3)and(($T_BOT==75) OR ($T_BOT==73)))
					{
					echo "
					<TABLE cellspacing=0 cellpadding=0 border=0 width=70%><TR><TD>
					<font style=\"COLOR:#8f0000;FONT-SIZE:10pt\">&nbsp;&nbsp;Удачи тебе!!! Всегда рад помочь. Обращайся!<br>
					&nbsp;&nbsp;<br>
					&nbsp;&nbsp;<br>
					&nbsp;&nbsp;<br>
					&nbsp;&nbsp;</font>
					<br><br><br></TD></TR>";
					echo "<TR><TD bgcolor=f2f0f0>&nbsp;&nbsp;<br>&nbsp;&nbsp;<br><a href=?talk=".$T_BOT.">Я передумал, давай еще поговорим...</a></TD></TR>";
					echo "<TR><TD bgcolor=f2f0f0>&nbsp;&nbsp;<br>&nbsp;&nbsp;<br><a href=?talk=".$T_BOT."&dial=88>Окончить разговор...</a></TD></TR>";
					echo "</TABLE>";
					}
					else
					if  (($dial==22)and(($T_BOT==75) OR ($T_BOT==73)))
					{
					echo "
					<TABLE cellspacing=0 cellpadding=0 border=0 width=70%><TR><TD>
					<font style=\"COLOR:#8f0000;FONT-SIZE:10pt\">&nbsp;&nbsp;Выбери какой осколок ты хочешь получить?<br>
					&nbsp;&nbsp;</font>
					<br></TD></TR>";
					
					$get_oskol=mysql_query("select * from oldbk.shop where id in (15561,15562,15563,15564,15565,15566,15567,15568)  ;");
						while ($osk = mysql_fetch_array($get_oskol)) 
						{
						echo "<TR><TD bgcolor=f2f0f0><a href=?talk=".$T_BOT."&dial={$osk['id']}><img src=http://i.oldbk.com/i/sh/{$osk['img']}> - {$osk['name']} </a></TD></TR>";
						}
									
					echo "<TR><TD bgcolor=f2f0f0>&nbsp;&nbsp;<br>&nbsp;&nbsp;<br>&nbsp;&nbsp;<a href=?talk=".$T_BOT.">Я передумал, давай еще поговорим...</a></TD></TR>";																
					echo "<TR><TD bgcolor=f2f0f0>&nbsp;&nbsp;<br>&nbsp;&nbsp;<br>&nbsp;&nbsp;<a href=?talk=".$T_BOT."&dial=88>Окончить разговор...</a></TD></TR>";
					echo "</TABLE>";
					}
					else
					if  (($dial==23)and(($T_BOT==75) OR ($T_BOT==73)))
					{
					echo "
					<TABLE cellspacing=0 cellpadding=0 border=0 width=70%><TR><TD>
					<font style=\"COLOR:#8f0000;FONT-SIZE:10pt\">&nbsp;&nbsp;Выбери какую статую ты хочешь собрать?<br>
					&nbsp;&nbsp;</font>
					<br></TD></TR>";
					
					$get_oskol=mysql_query("select * from oldbk.shop where id in (15551,15552,15553,15554,15555,15556,15557,15558)  ;");
						while ($osk = mysql_fetch_array($get_oskol)) 
						{
						echo "<TR><TD bgcolor=f2f0f0><a href=?talk=".$T_BOT."&dial={$osk['id']}><img src=http://i.oldbk.com/i/sh/{$osk['img']}> - {$osk['name']} </a></TD></TR>";
						}
					echo "<TR><TD bgcolor=f2f0f0>&nbsp;&nbsp;<br>&nbsp;&nbsp;<br> &nbsp;&nbsp;<a href=?talk=".$T_BOT.">Я передумал, давай еще поговорим...</a></TD></TR>";											
					echo "<TR><TD bgcolor=f2f0f0>&nbsp;&nbsp;<br>&nbsp;&nbsp;<br> &nbsp;&nbsp;<a href=?talk=".$T_BOT."&dial=88>Окончить разговор...</a></TD></TR>";
					echo "</TABLE>";
					}
					elseif  (($dial==25) and ((int)($_GET['col'])>=1)and ((int)($_GET['col'])<=3) and(($T_BOT==75) OR ($T_BOT==73)))
					{//выбор карт из выбранной кол. №2
					$col=(int)($_GET['col']);
						require_once("cards_config.php");
						$p='coll'.$col;
						$arr=$$p;
						$skey='11'.$col.'001';
						$fkey='11'.$col.'009';
						
					if ($user['money']>=100)
					{
					$showform=true;
							if ($_POST['scrol'])
							{
									foreach($_POST[scrol] as $sckey=>$scqv)
						                        {
						                        $sobr[]=(int)($sckey);
						                        }
								//проверяем/выдаем
								$sql = "SELECT  *  FROM oldbk.`inventory` WHERE id in (".implode(",",$sobr).") and  `owner` = '".$_SESSION['uid']."' and (sowner=0 or sowner='{$user[id]}')  AND  (prototype>=".$skey." and prototype<=".$fkey.")  AND `setsale` = 0 AND bs_owner = ".$user['in_tower']."  LIMIT 3";				
								$q = mysql_query($sql);
								$cckol=0;
								$card_name='';
								while($row = mysql_fetch_assoc($q)) 
								{
									$test_card[]=$row['id'];
									$card_name[]=$row['name'];
									$test_proto[]=$row['prototype'];
									$cckol++;
								}

								if ($cckol==3)
									{
								mysql_query("DELETE FROM oldbk.inventory where owner = '{$_SESSION['uid']}' and id in (".implode(",",$test_card).")  LIMIT 3 ");
									if (mysql_affected_rows()==3)
										{
										//делаем рандом 
									        $dress = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.shop WHERE (id>=".$skey." and id<=".$fkey.") and id not in (".implode(",",$test_proto).")  ORDER BY RAND() LIMIT 1;"));
									        if ($dress['id']>0)
											{
											
											if (mysql_query("INSERT INTO oldbk.`inventory`
											(`prototype`,`owner`,`sowner`,`name`,`type`,`massa`,`cost`,`img`,`maxdur`,`isrep`,
												`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,`needident`,`nsila`,`nlovk`,`ninta`,`nintel`,`nmudra`,`nvinos`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,
												`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`bron1`,`bron2`,`bron3`,`bron4`,`maxu`,`minu`,`magic`,`nlevel`,`nalign`,`dategoden`,`goden`,`nsex`,`otdel`,`present`,`labonly`,`labflag`,`group`,`idcity`,`letter`
											)
											VALUES
											('{$dress['id']}','{$user['id']}','0','{$dress['name']}','{$dress['type']}',{$dress['massa']},{$dress['cost']},'{$dress['img']}',{$dress['maxdur']},{$dress['isrep']},'{$dress['gsila']}','{$dress['glovk']}','{$dress['ginta']}','{$dress['gintel']}','{$dress['ghp']}','{$dress['gnoj']}','{$dress['gtopor']}','{$dress['gdubina']}','{$dress['gmech']}','{$dress['gfire']}','{$dress['gwater']}','{$dress['gair']}','{$dress['gearth']}','{$dress['glight']}','{$dress['ggray']}','{$dress['gdark']}','{$dress['needident']}','{$dress['nsila']}','{$dress['nlovk']}','{$dress['ninta']}','{$dress['nintel']}','{$dress['nmudra']}','{$dress['nvinos']}','{$dress['nnoj']}','{$dress['ntopor']}','{$dress['ndubina']}','{$dress['nmech']}','{$dress['nfire']}','{$dress['nwater']}','{$dress['nair']}','{$dress['nearth']}','{$dress['nlight']}','{$dress['ngray']}','{$dress['ndark']}',
											'{$dress['mfkrit']}','{$dress['mfakrit']}','{$dress['mfuvorot']}','{$dress['mfauvorot']}','{$dress['bron1']}','{$dress['bron2']}','{$dress['bron3']}','{$dress['bron4']}','{$dress['maxu']}','{$dress['minu']}','{$dress['magic']}','{$dress['nlevel']}','{$dress['nalign']}','".$dress['dategoden']."','{$dress['goden']}','{$dress['nsex']}','{$dress['razdel']}','Старьeвщик','0','0','{$dress['group']}','{$user['id_city']}','{$dress['letter']}'
											) ;") )
										     	{
										     		$dress['id']=mysql_insert_id();
												
												//-100 кр
												mysql_query_100("UPDATE `users` set money=money-100 where id='".$user['id']."'" );

												// пишем в дело
								 				$rec=array();
								 				$rec['owner']=$user['id'];
												$rec['owner_login']=$user['login'];
												$rec['owner_balans_do']=$user['money'];
												$rec['owner_balans_posle']=($user['money']-100);
								 				$rec['target'] = $T_BOT;
												$rec['target_login'] = 'Старьeвщик';
												$rec['type']=1101;
												$rec['sum_kr']=100;
												$rec['sum_ekr']=0;
												$rec['sum_kom']=0;
												$rec['item_count']=0;
												$rec['item_id']=get_item_fid($dress);
												$rec['item_name']=$dress['name'];
												$rec['item_count']=1;
												$rec['item_type']=$dress['type'];
												$rec['item_cost']=$dress['cost'];
												$rec['item_dur']=$dress['duration'];
												$rec['item_maxdur']=$dress['maxdur'];
												$rec['add_info']="Отдал карты:(".implode(",",$test_card).") ";
												add_to_new_delo($rec); //юзеру
								 				$rec=array();
								 				
								 				addchp ('<font color=red>Внимание!</font> Вы отдали <b>"'.implode("\",\"",$card_name).'"</b> ! ','{[]}'.$user[login].'{[]}',$user['room'],$user['id_city']);
												addchp ('<font color=red>Внимание!</font> Персонаж "'.$nexten['login'].'" подарил вам <B>"'.$dress['name'].'" (x1)</B>.   ','{[]}'.$user['login'].'{[]}',$user['room'],$user['id_city']);
								 				$showform=false;
												echo "
												<TABLE cellspacing=0 cellpadding=0 border=0 width=70%><TR><TD>
												<font style=\"COLOR:#8f0000;FONT-SIZE:10pt\">&nbsp;&nbsp;Хорошая сделка!
												&nbsp;&nbsp; Вот держи, твоя \"".$dress['name']."\"  !<br>
												&nbsp;&nbsp;</font>
												<br></TD></TR>";
												echo "<TR><TD bgcolor=f2f0f0>";
												echo "</TD></TR>";
								 				
								 			}
											}
											
										}
									}
									
							}
					
						if ($showform)
							{
							echo "
							<TABLE cellspacing=0 cellpadding=0 border=0 width=70%><TR><TD>
							<font style=\"COLOR:#8f0000;FONT-SIZE:10pt\">&nbsp;&nbsp;Я могу обменять <b>три любые карты одной коллекции</b> карты, на <b>одну другую</b>, <b> за 100кр</b>!
							<br> Скажи какие карты ты готов отдать?<br>
							&nbsp;&nbsp;</font>
							<br></TD></TR>";
							echo "<TR><TD bgcolor=f2f0f0>";
							print_my_cards($skey,$fkey);
							echo "</TD></TR>";
							}
					}
						else
						{
							echo "
							<TABLE cellspacing=0 cellpadding=0 border=0 width=70%><TR><TD>
							<font style=\"COLOR:#8f0000;FONT-SIZE:10pt\">&nbsp;&nbsp;Я могу обменять <b>три любые карты одной коллекции</b> карты, на <b>одну другую</b>, <b> за 100кр</b>!<br> Это стоит 100 кр! Ровно сто кр! Приходи когда будут!<br>
							&nbsp;&nbsp;</font>
							<br></TD></TR>";
							}
														
					echo "<TR><TD bgcolor=f2f0f0>&nbsp;&nbsp;<br>&nbsp;&nbsp;<br> &nbsp;&nbsp;<a href=?talk=".$T_BOT.">Я передумал, давай еще поговорим...</a></TD></TR>";											
					echo "<TR><TD bgcolor=f2f0f0>&nbsp;&nbsp;<br>&nbsp;&nbsp;<br> &nbsp;&nbsp;<a href=?talk=".$T_BOT."&dial=88>Окончить разговор...</a></TD></TR>";
					echo "</TABLE>";
					
					}
					elseif  (($dial==25)and(($T_BOT==75) OR ($T_BOT==73)))
					{ // выбор кол. для обмена №1
					echo "
					<TABLE cellspacing=0 cellpadding=0 border=0 width=70%><TR><TD>
					<font style=\"COLOR:#8f0000;FONT-SIZE:10pt\">&nbsp;&nbsp;Выбери с какой коллекции будем меняться?<br>
					&nbsp;&nbsp;</font>
					<br></TD></TR>";
					
					require_once("cards_config.php");

					for($k=1;$k<=3;$k++)
						{
						
						$p='coll'.$k;
						$key='11'.$k.'000';
						$arr=$$p;
						echo "<TR><TD bgcolor=f2f0f0><a href=?talk=".$T_BOT."&dial=25&col={$k}><img src=http://i.oldbk.com/i/sh/".$arr[$key]['img']."> - ".$arr[$key]['name']." </a></TD></TR>";
						}
					
					echo "<TR><TD bgcolor=f2f0f0>&nbsp;&nbsp;<br>&nbsp;&nbsp;<br> &nbsp;&nbsp;<a href=?talk=".$T_BOT.">Я передумал, давай еще поговорим...</a></TD></TR>";											
					echo "<TR><TD bgcolor=f2f0f0>&nbsp;&nbsp;<br>&nbsp;&nbsp;<br> &nbsp;&nbsp;<a href=?talk=".$T_BOT."&dial=88>Окончить разговор...</a></TD></TR>";
					echo "</TABLE>";
					}

					/*										
					elseif  (($dial>=111001) and ($dial<=111009)  and (($T_BOT==75) OR ($T_BOT==73)))
					{
					echo "<TABLE cellspacing=0 cellpadding=0 border=0 width=70%>";
						//обмен
						$sql = "SELECT  *  FROM oldbk.`inventory` WHERE `owner` = '".$_SESSION['uid']."' AND (prototype='{$dial}')  AND `setsale` = 0 AND bs_owner = ".$user['in_tower']."  LIMIT 3";				
						$q = mysql_query($sql);
						$cckol=0;
						$card_name='';
						while($row = mysql_fetch_assoc($q)) 
						{
							$test_card[]=$row['id'];
							$card_name=$row['name'];
							$cckol++;
						}

						
							if ($cckol==3)
								{
								//берем только три
								mysql_query("DELETE FROM oldbk.inventory where owner = '{$_SESSION['uid']}' and id in (".implode(",",$test_card).")  LIMIT 3 ");
									if (mysql_affected_rows()==3)
										{
										// делаем рандом но не та что была
										$rand_array=array(111001,111002,111003,111004,111005,111006,111007,111008,111009); //прототипы всех карт
										$get_key = array_search($dial, $rand_array);  // ищем в масиве тот прототип которые сдает чел
										unset($rand_array[$get_key]); // удаляем из масива
										shuffle($rand_array); // рандом
										$new_proto=$rand_array[0]; // прото который будем давать
									        $dress = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.shop WHERE `id` = '{$new_proto}' LIMIT 1;"));
									        if ($dress['id']>0)
											{
											
											if (mysql_query("INSERT INTO oldbk.`inventory`
											(`prototype`,`owner`,`sowner`,`name`,`type`,`massa`,`cost`,`img`,`maxdur`,`isrep`,
												`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,`needident`,`nsila`,`nlovk`,`ninta`,`nintel`,`nmudra`,`nvinos`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,
												`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`bron1`,`bron2`,`bron3`,`bron4`,`maxu`,`minu`,`magic`,`nlevel`,`nalign`,`dategoden`,`goden`,`nsex`,`otdel`,`present`,`labonly`,`labflag`,`group`,`idcity`,`letter`
											)
											VALUES
											('{$dress['id']}','{$user['id']}','0','{$dress['name']}','{$dress['type']}',{$dress['massa']},{$dress['cost']},'{$dress['img']}',{$dress['maxdur']},{$dress['isrep']},'{$dress['gsila']}','{$dress['glovk']}','{$dress['ginta']}','{$dress['gintel']}','{$dress['ghp']}','{$dress['gnoj']}','{$dress['gtopor']}','{$dress['gdubina']}','{$dress['gmech']}','{$dress['gfire']}','{$dress['gwater']}','{$dress['gair']}','{$dress['gearth']}','{$dress['glight']}','{$dress['ggray']}','{$dress['gdark']}','{$dress['needident']}','{$dress['nsila']}','{$dress['nlovk']}','{$dress['ninta']}','{$dress['nintel']}','{$dress['nmudra']}','{$dress['nvinos']}','{$dress['nnoj']}','{$dress['ntopor']}','{$dress['ndubina']}','{$dress['nmech']}','{$dress['nfire']}','{$dress['nwater']}','{$dress['nair']}','{$dress['nearth']}','{$dress['nlight']}','{$dress['ngray']}','{$dress['ndark']}',
											'{$dress['mfkrit']}','{$dress['mfakrit']}','{$dress['mfuvorot']}','{$dress['mfauvorot']}','{$dress['bron1']}','{$dress['bron2']}','{$dress['bron3']}','{$dress['bron4']}','{$dress['maxu']}','{$dress['minu']}','{$dress['magic']}','{$dress['nlevel']}','{$dress['nalign']}','".$dress['dategoden']."','{$dress['goden']}','{$dress['nsex']}','{$dress['razdel']}','Старьeвщик','0','0','{$dress['group']}','{$user['id_city']}','{$dress['letter']}'
											) ;") )
										     	{
										     		$dress['id']=mysql_insert_id();
 	      	            

												// пишем в дело
								 				$rec=array();
								 				$rec['owner']=$user['id'];
												$rec['owner_login']=$user['login'];
												$rec['owner_balans_do']=$user['money'];
												$rec['owner_balans_posle']=$user['money'];
								 				$rec['target'] = $T_BOT;
												$rec['target_login'] = 'Старьeвщик';
												$rec['type']=1101;
												$rec['sum_kr']=0;
												$rec['sum_ekr']=0;
												$rec['sum_kom']=0;
												$rec['item_count']=0;
												$rec['item_id']=get_item_fid($dress);
												$rec['item_name']=$dress['name'];
												$rec['item_count']=1;
												$rec['item_type']=$dress['type'];
												$rec['item_cost']=$dress['cost'];
												$rec['item_dur']=$dress['duration'];
												$rec['item_maxdur']=$dress['maxdur'];
												$rec['add_info']="Отдал карты:(".implode(",",$test_card).") ";
												add_to_new_delo($rec); //юзеру
								 				$rec=array();
								 				
								 				addchp ('<font color=red>Внимание!</font> Вы отдали <b>"'.$card_name.'" (x3)</b> ! ','{[]}'.$user[login].'{[]}',$user['room'],$user['id_city']);
												addchp ('<font color=red>Внимание!</font> Персонаж "'.$nexten['login'].'" подарил вам <B>"'.$dress['name'].'" (x1)</B>.   ','{[]}'.$user['login'].'{[]}',$user['room'],$user['id_city']);
								 				
												echo "
												<TR><TD><font style=\"COLOR:#8f0000;FONT-SIZE:10pt\">&nbsp;&nbsp;Удачный обмен!
												&nbsp;&nbsp; Держи свою \"".$dress['name']."\"  !<br>
												&nbsp;&nbsp;</font>
												<br></TD></TR>";
												echo "<TR><TD bgcolor=f2f0f0>";
												echo "</TD></TR>";
												echo "<TR><TD bgcolor=f2f0f0>&nbsp;&nbsp;<br>&nbsp;&nbsp;<br><a href=?talk=".$T_BOT."&dial=24>Выбрать другой обмен...</a></TD></TR>";
								 				
								 			}
											}
										}
								
								}
								else
								{
								// мало
								echo '<TR><TD bgcolor=f2f0f0><font style=\"COLOR:#8f0000;FONT-SIZE:10pt\">&nbsp;&nbsp;Дурить меня вздумал?<br>&nbsp;&nbsp;Не хорошо...</font></TD></TR>';
								echo "<TR><TD bgcolor=f2f0f0>&nbsp;&nbsp;<br>&nbsp;&nbsp;<br><a href=?talk=".$T_BOT."&dial=24>Выбрать другой обмен...</a></TD></TR>";								
								}
								
						echo "<TR><TD bgcolor=f2f0f0>&nbsp;&nbsp;<br>&nbsp;&nbsp;<br> &nbsp;&nbsp;<a href=?talk=".$T_BOT.">Я передумал, давай еще поговорим...</a></TD></TR>";											
						echo "<TR><TD bgcolor=f2f0f0>&nbsp;&nbsp;<br>&nbsp;&nbsp;<br> &nbsp;&nbsp;<a href=?talk=".$T_BOT."&dial=88>Окончить разговор...</a></TD></TR>";
						echo "</TABLE>";
					}*/
					else
					if  (($dial==41)and(($T_BOT==75) OR ($T_BOT==73)))
					{
					echo "
					<TABLE cellspacing=0 cellpadding=0 border=0 width=70%><TR><TD>
					<font style=\"COLOR:#8f0000;FONT-SIZE:10pt\">&nbsp;&nbsp;Выбери какую чарку ты хочешь собрать?<br>
					&nbsp;&nbsp;</font>
					<br></TD></TR>";
					
					$get_oskol=mysql_query("select * from oldbk.shop where id in (56662,56663)  ;");
						while ($osk = mysql_fetch_array($get_oskol)) 
						{
						echo "<TR><TD bgcolor=f2f0f0><a href=?talk=".$T_BOT."&dial={$osk['id']}><img src=http://i.oldbk.com/i/sh/{$osk['img']}> - {$osk['name']} </a></TD></TR>";
						}
														
					echo "<TR><TD bgcolor=f2f0f0>&nbsp;&nbsp;<br>&nbsp;&nbsp;<br> &nbsp;&nbsp;<a href=?talk=".$T_BOT.">Я передумал, давай еще поговорим...</a></TD></TR>";																						
					echo "<TR><TD bgcolor=f2f0f0>&nbsp;&nbsp;<br>&nbsp;&nbsp;<br> &nbsp;&nbsp;<a href=?talk=".$T_BOT."&dial=88>Окончить разговор...</a></TD></TR>";
					echo "</TABLE>";
					}	
					else
					if  (($dial==56662 )and(($T_BOT==75) OR ($T_BOT==73)))
					{
					$kol=3;
					  if ($_POST[scrol])
					  	{
					  	$kcount=0;
					  	$sobr='';
					  	foreach($_POST[scrol] as $sckey=>$scqv)
					                        {
					                        $kcount++;
					                        $sobr.=(int)($sckey).",";
					                        }
					        $sobr=substr($sobr,0,-1);
					        //testing 
				                $get_test=mysql_fetch_array(mysql_query("select count(*), name from oldbk.inventory where id in (".$sobr.") and  prototype =56661  and owner={$user[id]} and setsale=0"));  

					        if ($get_test[0]==$kol)
					        	{
					        	//вссе хорошо
							mysql_query("delete from oldbk.inventory where id in (".$sobr.") and  prototype =56661 and owner={$user[id]} and setsale=0;");
							addchp ('<font color=red>Внимание!</font> Вы отдали <b>\"'.$get_test['name'].'\"</b>  (x'.$kol.')','{[]}'.$user[login].'{[]}',$user['room'],$user['id_city']);
							$item=present_charka($dial);
							addchp ('<font color=red>Внимание!</font> Персонаж "'.$nexten['login'].'" подарил вам <B>'.$sc_name[$dial].'</B>.   ','{[]}'.$user['login'].'{[]}',$user['room'],$user['id_city']);
							//new_delo
  		    			$rec['owner']=$user[id]; 
					$rec['owner_login']=$user[login];
					$rec['owner_balans_do']=$user['money'];
					$rec['owner_balans_posle']=$user['money'];
					$rec['target']=$nexten['id'];
					$rec['target_login']=$nexten['login'];
					$rec['type']=14;
					$rec['sum_kr']=0;
					$rec['sum_ekr']=0;
					$rec['sum_kom']=0;
					$rec['item_id']=get_item_fid($item);
					$rec['item_name']=$item['name'];
					$rec['item_count']=1;
					$rec['item_type']=$item['type'];
					$rec['item_cost']=$item['cost'];
					$rec['item_dur']=$item['duration'];
					$rec['item_maxdur']=$item['maxdur'];
					$rec['item_ups']=$item['ups'];
					$rec['item_unic']=$item['unic'];
					$rec['item_incmagic']=$item['includemagicname'];
					$rec['item_incmagic_count']=$item['includemagicuses'];
					$rec['item_arsenal']='';
					add_to_new_delo($rec); //юзеру


							echo "
							<TABLE cellspacing=0 cellpadding=0 border=0 width=70%><TR><TD>
							<font style=\"COLOR:#8f0000;FONT-SIZE:10pt\">&nbsp;&nbsp;Удачный обмен! Держи свой \"".$sc_name[$dial]."\".<br>
							&nbsp;&nbsp;</font>
							<br></TD></TR>";
							echo "<TR><TD bgcolor=f2f0f0>";
							echo "</TD></TR>";
							echo "<TR><TD bgcolor=f2f0f0>&nbsp;&nbsp;<br>&nbsp;&nbsp;<br><a href=?talk=".$T_BOT."&dial=41>Выбрать другой обмен...</a></TD></TR>";
							echo "<TR><TD bgcolor=f2f0f0>&nbsp;&nbsp;<br>&nbsp;&nbsp;<br> &nbsp;&nbsp;<a href=?talk=".$T_BOT.">Я передумал, давай еще поговорим...</a></TD></TR>";																		
							echo "<TR><TD bgcolor=f2f0f0>&nbsp;&nbsp;<br><a href=?talk=".$T_BOT."&dial=88>Окончить разговор...</a></TD></TR>";
							echo "</TABLE>";						
					        	}
					        	else
					        	{
					        	//все плохо
							echo "
							<TABLE cellspacing=0 cellpadding=0 border=0 width=70%><TR><TD>
							<font style=\"COLOR:#8f0000;FONT-SIZE:10pt\">&nbsp;&nbsp;Дурить меня вздумал?<br>
							&nbsp;&nbsp;Не хорошо...</font>
							<br></TD></TR>";
							echo "<TR><TD bgcolor=f2f0f0>";
							echo "</TD></TR>";
							echo "<TR><TD bgcolor=f2f0f0>&nbsp;&nbsp;<br>&nbsp;&nbsp;<br><a href=?talk=".$T_BOT."&dial=41>Выбрать другой обмен...</a></TD></TR>";
							echo "<TR><TD bgcolor=f2f0f0>&nbsp;&nbsp;<br>&nbsp;&nbsp;<br> &nbsp;&nbsp;<a href=?talk=".$T_BOT.">Я передумал, давай еще поговорим...</a></TD></TR>";																		
							echo "<TR><TD bgcolor=f2f0f0>&nbsp;&nbsp;<br><a href=?talk=".$T_BOT."&dial=88>Окончить разговор...</a></TD></TR>";
							echo "</TABLE>";
					        	}
					  	
					  	}
					else
					  {
						//
					
					echo "
					<TABLE cellspacing=0 cellpadding=0 border=0 width=70%><TR><TD>
					<font style=\"COLOR:#8f0000;FONT-SIZE:10pt\">&nbsp;&nbsp;Я готов обменять \"Cвиток Чарования [I]\" <b>(x".$kol.")</b> на \"".$sc_name[$dial]."\" ... <br>
					&nbsp;&nbsp;Выберите свитки для обмена:<br>
					&nbsp;&nbsp;</font>
					<br></TD></TR>";
					echo "<TR><TD bgcolor=f2f0f0>";
					print_my_charks($kol,56661);
					echo "</TD></TR>";
					echo "<TR><TD bgcolor=f2f0f0>&nbsp;&nbsp;<br>&nbsp;&nbsp;<br><a href=?talk=".$T_BOT."&dial=41>Выбрать другой обмен...</a></TD></TR>";
					echo "<TR><TD bgcolor=f2f0f0>&nbsp;&nbsp;<br>&nbsp;&nbsp;<br><a href=?talk=".$T_BOT.">Я передумал, давай еще поговорим...</a></TD></TR>";																
					echo "<TR><TD bgcolor=f2f0f0>&nbsp;&nbsp;<br><a href=?talk=".$T_BOT."&dial=88>Окончить разговор...</a></TD></TR>";
					echo "</TABLE>";						
					 	}
					 }
					else
					if  (($dial==56663 )and(($T_BOT==75) OR ($T_BOT==73)))
					{
					$kol=3;
					  if ($_POST[scrol])
					  	{
					  	$kcount=0;
					  	$sobr='';
					  	foreach($_POST[scrol] as $sckey=>$scqv)
					                        {
					                        $kcount++;
					                        $sobr.=(int)($sckey).",";
					                        }
					        $sobr=substr($sobr,0,-1);
					        //testing 
				                $get_test=mysql_fetch_array(mysql_query("select count(*), name  from oldbk.inventory where id in (".$sobr.") and (sowner=0 or sowner='{$user[id]}') and  prototype =56662  and owner={$user[id]} and setsale=0"));  

					        if ($get_test[0]==$kol)
					        	{
					        	//вссе хорошо
							mysql_query("delete from oldbk.inventory where id in (".$sobr.") and  prototype =56662 and (sowner=0 or sowner='{$user[id]}') and owner={$user[id]} and setsale=0;");
							addchp ('<font color=red>Внимание!</font> Вы отдали <b>\"'.$get_test['name'].'\"</b>  (x'.$kol.')','{[]}'.$user[login].'{[]}',$user['room'],$user['id_city']);
							
							$item=present_charka($dial);
							addchp ('<font color=red>Внимание!</font> Персонаж "'.$nexten['login'].'" подарил вам <B>'.$sc_name[$dial].'</B>.   ','{[]}'.$user['login'].'{[]}',$user['room'],$user['id_city']);
							//new_delo
  		    			$rec['owner']=$user[id]; 
					$rec['owner_login']=$user[login];
					$rec['owner_balans_do']=$user['money'];
					$rec['owner_balans_posle']=$user['money'];
					$rec['target']=$nexten['id'];
					$rec['target_login']=$nexten['login'];
					$rec['type']=14;
					$rec['sum_kr']=0;
					$rec['sum_ekr']=0;
					$rec['sum_kom']=0;
					$rec['item_id']=get_item_fid($item);
					$rec['item_name']=$item['name'];
					$rec['item_count']=1;
					$rec['item_type']=$item['type'];
					$rec['item_cost']=$item['cost'];
					$rec['item_dur']=$item['duration'];
					$rec['item_maxdur']=$item['maxdur'];
					$rec['item_ups']=$item['ups'];
					$rec['item_unic']=$item['unic'];
					$rec['item_incmagic']=$item['includemagicname'];
					$rec['item_incmagic_count']=$item['includemagicuses'];
					$rec['item_arsenal']='';
					add_to_new_delo($rec); //юзеру


							echo "
							<TABLE cellspacing=0 cellpadding=0 border=0 width=70%><TR><TD>
							<font style=\"COLOR:#8f0000;FONT-SIZE:10pt\">&nbsp;&nbsp;Удачный обмен! Держи свой \"".$sc_name[$dial]."\".<br>
							&nbsp;&nbsp;</font>
							<br></TD></TR>";
							echo "<TR><TD bgcolor=f2f0f0>";
							echo "</TD></TR>";
							echo "<TR><TD bgcolor=f2f0f0>&nbsp;&nbsp;<br>&nbsp;&nbsp;<br><a href=?talk=".$T_BOT."&dial=41>Выбрать другой обмен...</a></TD></TR>";
							echo "<TR><TD bgcolor=f2f0f0>&nbsp;&nbsp;<br>&nbsp;&nbsp;<br> &nbsp;&nbsp;<a href=?talk=".$T_BOT.">Я передумал, давай еще поговорим...</a></TD></TR>";																		
							echo "<TR><TD bgcolor=f2f0f0>&nbsp;&nbsp;<br><a href=?talk=".$T_BOT."&dial=88>Окончить разговор...</a></TD></TR>";
							echo "</TABLE>";						
					        	}
					        	else
					        	{
					        	//все плохо
							echo "
							<TABLE cellspacing=0 cellpadding=0 border=0 width=70%><TR><TD>
							<font style=\"COLOR:#8f0000;FONT-SIZE:10pt\">&nbsp;&nbsp;Дурить меня вздумал?<br>
							&nbsp;&nbsp;Не хорошо...</font>
							<br></TD></TR>";
							echo "<TR><TD bgcolor=f2f0f0>";
							echo "</TD></TR>";
							echo "<TR><TD bgcolor=f2f0f0>&nbsp;&nbsp;<br>&nbsp;&nbsp;<br><a href=?talk=".$T_BOT."&dial=41>Выбрать другой обмен...</a></TD></TR>";
							echo "<TR><TD bgcolor=f2f0f0>&nbsp;&nbsp;<br>&nbsp;&nbsp;<br> &nbsp;&nbsp;<a href=?talk=".$T_BOT.">Я передумал, давай еще поговорим...</a></TD></TR>";																		
							echo "<TR><TD bgcolor=f2f0f0>&nbsp;&nbsp;<br><a href=?talk=".$T_BOT."&dial=88>Окончить разговор...</a></TD></TR>";
							echo "</TABLE>";
					        	}
					  	
					  	}
					else
					  {
						//
					
					echo "
					<TABLE cellspacing=0 cellpadding=0 border=0 width=70%><TR><TD>
					<font style=\"COLOR:#8f0000;FONT-SIZE:10pt\">&nbsp;&nbsp;Я готов обменять \"Cвиток Чарования [II]\" <b>(x".$kol.")</b> на \"".$sc_name[$dial]."\" ... <br>					
					&nbsp;&nbsp;Выберите свитки для обмена:<br>
					&nbsp;&nbsp;</font>
					<br></TD></TR>";
					echo "<TR><TD bgcolor=f2f0f0>";
					print_my_charks($kol,56662);			
					echo "</TD></TR>";
					echo "<TR><TD bgcolor=f2f0f0>&nbsp;&nbsp;<br>&nbsp;&nbsp;<br><a href=?talk=".$T_BOT."&dial=41>Выбрать другой обмен...</a></TD></TR>";
					echo "<TR><TD bgcolor=f2f0f0>&nbsp;&nbsp;<br>&nbsp;&nbsp;<br><a href=?talk=".$T_BOT.">Я передумал, давай еще поговорим...</a></TD></TR>";																
					echo "<TR><TD bgcolor=f2f0f0>&nbsp;&nbsp;<br><a href=?talk=".$T_BOT."&dial=88>Окончить разговор...</a></TD></TR>";
					echo "</TABLE>";						
					 	}
					 }
					else
					if  (($dial>=15561 AND $dial<=15568)and(($T_BOT==75) OR ($T_BOT==73)))
					{
					$kol=$sc_kol[$dial];
					  if ($_POST[scrol])
					  	{
					  	if ($user[id]==14897)
					  	{
					  	echo "DeBUG:";
					  	 print_r($_POST[scrol]);
					  	 }
					  	$kcount=0;
					  	$sobr='';
					  	foreach($_POST[scrol] as $sckey=>$scqv)
					                        {
					                        $kcount++;
					                        $sobr.=(int)($sckey).",";
					                        }
					        $sobr=substr($sobr,0,-1);
					        //testing 
				                $get_test=mysql_fetch_array(mysql_query("select count(*) from oldbk.inventory where id in (".$sobr.") and (sowner=0 or sowner='{$user[id]}') and  prototype >=15561 and prototype <=15568 and owner={$user[id]} and setsale=0"));  
				                //echo "T1";
					        if ($get_test[0]==$kol)
					        	{
					        	//вссе хорошо
							mysql_query("delete from oldbk.inventory where id in (".$sobr.") and (sowner=0 or sowner='{$user[id]}') and  prototype >=15561 and prototype <=15568 and owner={$user[id]} and setsale=0;");
							addchp ('<font color=red>Внимание!</font> Вы отдали '.$kol.' осколков статуй! ','{[]}'.$user[login].'{[]}',$user['room'],$user['id_city']);
							$item=present_scroll($dial);
							addchp ('<font color=red>Внимание!</font> Персонаж "'.$nexten['login'].'" подарил вам <B>'.$sc_name[$dial].'</B>.   ','{[]}'.$user['login'].'{[]}',$user['room'],$user['id_city']);
							//new_delo
  		    			$rec['owner']=$user[id]; 
					$rec['owner_login']=$user[login];
					$rec['owner_balans_do']=$user['money'];
					$rec['owner_balans_posle']=$user['money'];
					$rec['target']=$nexten['id'];
					$rec['target_login']=$nexten['login'];
					$rec['type']=14;
					$rec['sum_kr']=0;
					$rec['sum_ekr']=0;
					$rec['sum_kom']=0;
					$rec['item_id']=get_item_fid($item);
					$rec['item_name']=$item['name'];
					$rec['item_count']=1;
					$rec['item_type']=$item['type'];
					$rec['item_cost']=$item['cost'];
					$rec['item_dur']=$item['duration'];
					$rec['item_maxdur']=$item['maxdur'];
					$rec['item_ups']=$item['ups'];
					$rec['item_unic']=$item['unic'];
					$rec['item_incmagic']=$item['includemagicname'];
					$rec['item_incmagic_count']=$item['includemagicuses'];
					$rec['item_arsenal']='';
					add_to_new_delo($rec); //юзеру

							
							
							
							if (olddelo==1)
							{
							mysql_query("INSERT INTO oldbk.`delo`(`id` , `author` ,`pers`, `text`, `type`, `date`) VALUES ('','0','{$user[id]}','Получил ".$sc_name[$dial]." от \"".$nexten['login']."\"  (за ".$kol." свитков подгона) ','1','".time()."');");							
							}
							
							echo "
							<TABLE cellspacing=0 cellpadding=0 border=0 width=70%><TR><TD>
							<font style=\"COLOR:#8f0000;FONT-SIZE:10pt\">&nbsp;&nbsp;Удачный обмен!
							&nbsp;&nbsp; Держи свой \"".$sc_name[$dial]."\"  !<br>
							&nbsp;&nbsp;</font>
							<br></TD></TR>";
							echo "<TR><TD bgcolor=f2f0f0>";
							echo "</TD></TR>";
							echo "<TR><TD bgcolor=f2f0f0>&nbsp;&nbsp;<br>&nbsp;&nbsp;<br><a href=?talk=".$T_BOT."&dial=22>Выбрать другой осколок...</a></TD></TR>";
							echo "<TR><TD bgcolor=f2f0f0>&nbsp;&nbsp;<br>&nbsp;&nbsp;<br> &nbsp;&nbsp;<a href=?talk=".$T_BOT.">Я передумал, давай еще поговорим...</a></TD></TR>";																		
							echo "<TR><TD bgcolor=f2f0f0>&nbsp;&nbsp;<br><a href=?talk=".$T_BOT."&dial=88>Окончить разговор...</a></TD></TR>";
							echo "</TABLE>";						
					        	}
					        	else
					        	{
					        	//все плохо
							echo "
							<TABLE cellspacing=0 cellpadding=0 border=0 width=70%><TR><TD>
							<font style=\"COLOR:#8f0000;FONT-SIZE:10pt\">&nbsp;&nbsp;Дурить меня вздумал?<br>
							&nbsp;&nbsp;Не хорошо...</font>
							<br></TD></TR>";
							echo "<TR><TD bgcolor=f2f0f0>";
							echo "</TD></TR>";
							echo "<TR><TD bgcolor=f2f0f0>&nbsp;&nbsp;<br>&nbsp;&nbsp;<br><a href=?talk=".$T_BOT."&dial=22>Выбрать другой осколок...</a></TD></TR>";
							echo "<TR><TD bgcolor=f2f0f0>&nbsp;&nbsp;<br>&nbsp;&nbsp;<br> &nbsp;&nbsp;<a href=?talk=".$T_BOT.">Я передумал, давай еще поговорим...</a></TD></TR>";																		
							echo "<TR><TD bgcolor=f2f0f0>&nbsp;&nbsp;<br><a href=?talk=".$T_BOT."&dial=88>Окончить разговор...</a></TD></TR>";
							echo "</TABLE>";
					        	}
					  	
					  	}
					else
					  {
						//
					
					echo "
					<TABLE cellspacing=0 cellpadding=0 border=0 width=70%><TR><TD>
					<font style=\"COLOR:#8f0000;FONT-SIZE:10pt\">&nbsp;&nbsp;Я готов обменять \"".$sc_name[$dial]."\" на <b>$kol</b> других...<br>
					&nbsp;&nbsp;Какие осколки ты готов отдать?<br>
					&nbsp;&nbsp;</font>
					<br></TD></TR>";
					echo "<TR><TD bgcolor=f2f0f0>";
					print_my_scrols($kol);					
					echo "</TD></TR>";
					echo "<TR><TD bgcolor=f2f0f0>&nbsp;&nbsp;<br>&nbsp;&nbsp;<br><a href=?talk=".$T_BOT."&dial=22>Выбрать другие осколки...</a></TD></TR>";
					echo "<TR><TD bgcolor=f2f0f0>&nbsp;&nbsp;<br><a href=?talk=".$T_BOT."&dial=88>Окончить разговор...</a></TD></TR>";
					echo "</TABLE>";						
					 	}
					 }	

					else
					if  (($dial>=15551 AND $dial<=15558)and(($T_BOT==75) OR ($T_BOT==73)))
					{
					$kol=$sc_kol[$dial];
					  if ($_POST[scrol])
					  	{
					  	if ($user[id]==14897)
					  	{
					  	echo "DeBUG:";
					  	 print_r($_POST[scrol]);
					  	 }
					  	$kcount=0;
					  	$sobr='';
					  	foreach($_POST[scrol] as $sckey=>$scqv)
					                        {
					                        $kcount++;
					                        $sobr.=(int)($sckey).",";
					                        }
					        $sobr=substr($sobr,0,-1);
					        //testing 
					        $dial_osk=$dial+10; // осколки нужного типа
				                $get_test=mysql_fetch_array(mysql_query("select count(*) from oldbk.inventory where id in (".$sobr.") and  prototype ='{$dial_osk}' and owner={$user[id]} and setsale=0"));  
				                //echo "T1";
					        if ($get_test[0]==$kol)
					        	{
					        	//вссе хорошо
							mysql_query("delete from oldbk.inventory where id in (".$sobr.") and  prototype ='{$dial_osk}' and owner={$user[id]} and setsale=0;");
							addchp ('<font color=red>Внимание!</font> Вы отдали '.$kol.' осколков статуй! ','{[]}'.$user[login].'{[]}',$user['room'],$user['id_city']);
							$item=present_scroll($dial);
							addchp ('<font color=red>Внимание!</font> Персонаж "'.$nexten['login'].'" подарил вам <B>'.$sc_name[$dial].'</B>.   ','{[]}'.$user['login'].'{[]}',$user['room'],$user['id_city']);
							//new_delo
  		    			$rec['owner']=$user[id]; 
					$rec['owner_login']=$user[login];
					$rec['owner_balans_do']=$user['money'];
					$rec['owner_balans_posle']=$user['money'];
					$rec['target']=$nexten['id'];
					$rec['target_login']=$nexten['login'];
					$rec['type']=14;
					$rec['sum_kr']=0;
					$rec['sum_ekr']=0;
					$rec['sum_kom']=0;
					$rec['item_id']=get_item_fid($item);
					$rec['item_name']=$item['name'];
					$rec['item_count']=1;
					$rec['item_type']=$item['type'];
					$rec['item_cost']=$item['cost'];
					$rec['item_dur']=$item['duration'];
					$rec['item_maxdur']=$item['maxdur'];
					$rec['item_ups']=$item['ups'];
					$rec['item_unic']=$item['unic'];
					$rec['item_incmagic']=$item['includemagicname'];
					$rec['item_incmagic_count']=$item['includemagicuses'];
					$rec['item_arsenal']='';
					add_to_new_delo($rec); //юзеру

							
							
							
							if (olddelo==1)
							{
							mysql_query("INSERT INTO oldbk.`delo`(`id` , `author` ,`pers`, `text`, `type`, `date`) VALUES ('','0','{$user[id]}','Получил ".$sc_name[$dial]." от \"".$nexten['login']."\"  (за ".$kol." свитков подгона) ','1','".time()."');");							
							}
							
							echo "
							<TABLE cellspacing=0 cellpadding=0 border=0 width=70%><TR><TD>
							<font style=\"COLOR:#8f0000;FONT-SIZE:10pt\">&nbsp;&nbsp;Удачный обмен!
							&nbsp;&nbsp; Держи свою \"".$sc_name[$dial]."\"  !<br>
							&nbsp;&nbsp;</font>
							<br></TD></TR>";
							echo "<TR><TD bgcolor=f2f0f0>";
							echo "</TD></TR>";
							echo "<TR><TD bgcolor=f2f0f0>&nbsp;&nbsp;<br>&nbsp;&nbsp;<br><a href=?talk=".$T_BOT."&dial=23>Выбрать другую статую...</a></TD></TR>";
							echo "<TR><TD bgcolor=f2f0f0>&nbsp;&nbsp;<br>&nbsp;&nbsp;<br> &nbsp;&nbsp;<a href=?talk=".$T_BOT.">Я передумал, давай еще поговорим...</a></TD></TR>";																		
							echo "<TR><TD bgcolor=f2f0f0>&nbsp;&nbsp;<br><a href=?talk=".$T_BOT."&dial=88>Окончить разговор...</a></TD></TR>";
							echo "</TABLE>";						
					        	}
					        	else
					        	{
					        	//все плохо
							echo "
							<TABLE cellspacing=0 cellpadding=0 border=0 width=70%><TR><TD>
							<font style=\"COLOR:#8f0000;FONT-SIZE:10pt\">&nbsp;&nbsp;Дурить меня вздумал?<br>
							&nbsp;&nbsp;Не хорошо...</font>
							<br></TD></TR>";
							echo "<TR><TD bgcolor=f2f0f0>";
							echo "</TD></TR>";
							echo "<TR><TD bgcolor=f2f0f0>&nbsp;&nbsp;<br>&nbsp;&nbsp;<br><a href=?talk=".$T_BOT."&dial=23>Выбрать другую статую...</a></TD></TR>";
							echo "<TR><TD bgcolor=f2f0f0>&nbsp;&nbsp;<br>&nbsp;&nbsp;<br> &nbsp;&nbsp;<a href=?talk=".$T_BOT.">Я передумал, давай еще поговорим...</a></TD></TR>";																		
							echo "<TR><TD bgcolor=f2f0f0>&nbsp;&nbsp;<br><a href=?talk=".$T_BOT."&dial=88>Окончить разговор...</a></TD></TR>";
							echo "</TABLE>";
					        	}
					  	
					  	}
					else
					  {
						//
					echo "
					<TABLE cellspacing=0 cellpadding=0 border=0 width=70%><TR><TD>
					<font style=\"COLOR:#8f0000;FONT-SIZE:10pt\">&nbsp;&nbsp;Я готов обменять \"".$sc_name[$dial]."\" на <b>$kol</b> ее осколков...<br>
					&nbsp;&nbsp;Какие осколки ты готов отдать?<br>
					&nbsp;&nbsp;</font>
					<br></TD></TR>";
					echo "<TR><TD bgcolor=f2f0f0>";
					print_my_scrols($kol,$dial);					
					echo "</TD></TR>";
					echo "<TR><TD bgcolor=f2f0f0>&nbsp;&nbsp;<br>&nbsp;&nbsp;<br><a href=?talk=".$T_BOT."&dial=23>Выбрать другую статую...</a></TD></TR>";
					echo "<TR><TD bgcolor=f2f0f0>&nbsp;&nbsp;<br>&nbsp;&nbsp;<br> &nbsp;&nbsp;<a href=?talk=".$T_BOT.">Я передумал, давай еще поговорим...</a></TD></TR>";																
					echo "<TR><TD bgcolor=f2f0f0>&nbsp;&nbsp;<br><a href=?talk=".$T_BOT."&dial=88>Окончить разговор...</a></TD></TR>";
					echo "</TABLE>";						
					 	}
					 }
					else
					{
					  die("<script>location.href='lab".$LAB.".php';</script>");
					//echo "ELSE";
					}
					
					?>
					
					
					</CENTER>
	
	<HR>
	</B>

</td>
<TD  valign=top align=rigth>
<TABLE width=250 cellspacing=0 cellpadding=0><TR>
<TD valign=top width=250 nowrap><CENTER>
<?


	showtelo($nexten,$en_wearItems,$en_magicItems);


?>
</TD></TR>
</TABLE>

</TD></TR>
</TABLE>

</td></tr>
</TABLE>
<input type='hidden' id='txtblockzone' value=''/>
</FORM>

<!-- <DIV ID=oMenu CLASS=menu onmouseout="closeMenu()"></DIV> -->
<DIV ID="oMenu"  style="position:absolute; border:1px solid #666; background-color:#CCC; display:none; "></DIV>
<TEXTAREA ID=holdtext STYLE="display:none;"></TEXTAREA>
</BODY>
</HTML>
<?
}
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