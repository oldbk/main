//-------------------------------------------------------------
// Функции для обработки магии 
var Hint3Name = '';
function findlogin(title, script, name){
    var el = document.getElementById("hint3");
	el.innerHTML = '<table width=100% cellspacing=1 cellpadding=0 bgcolor=CCC3AA><tr><td align=center><B>'+title+'</td><td width=20 align=right valign=top style="cursor: pointer" onclick="closehint3();"><BIG><B>x</td></tr><tr><td colspan=2>'+
	'<form action="'+script+'" method=POST><table width=100% cellspacing=0 cellpadding=2 bgcolor=FFF6DD><tr><td colspan=2><INPUT TYPE=hidden name=sd4 value="6">'+
	'Укажите логин персонажа:<small><BR>(можно щелкнуть по логину в чате)</TD></TR><TR><TD width=50% align=right><INPUT TYPE=text id="'+name+'" NAME="'+name+'"></TD><TD width=50%><INPUT TYPE="submit" value=" »» "></TD></TR></TABLE></FORM></td></tr></table>';
	el.style.visibility = "visible";
	el.style.left = 100;
	el.style.top = 100;
	document.getElementById(name).focus();
	Hint3Name = name;
}
function check(type)
{		
		var x=document.getElementById('timer').value;
		if(type=='timer')
		{
			if(x=='365')
			{
				document.getElementById('kr').disabled=false;
				document.getElementById('ekr').disabled=true;
				document.getElementById('vkr').setAttribute('checked', 'checked');
				document.getElementById('vekr').removeAttribute('checked');
				document.getElementById('no').removeAttribute('checked');
			}
			else
			{
				
				document.getElementById('kr').disabled=true;
				document.getElementById('vkr').removeAttribute('checked');
				<?
				if(ADMIN || $user[id]==7937)
				{
					?>
				document.getElementById('ekr').disabled=false;
				document.getElementById('no').removeAttribute('checked');
				document.getElementById('vekr').setAttribute('checked', 'checked');
				//alert('q');
					<?
				}
				?>
				
			}
		}
	<?
	if(ADMIN || $user[id]==7937)
	{
	?>
		if(type=='kr' && x=='365')
		{
			document.getElementById('kr').disabled=false;
			document.getElementById('ekr').disabled=true;
			document.getElementById('vekr').removeAttribute('checked');
			document.getElementById('no').removeAttribute('checked');
			document.getElementById('vkr').setAttribute('checked', 'checked');
		}
		
		if(type=='ekr')
		{
			document.getElementById('ekr').disabled=false;
			document.getElementById('kr').disabled=true;
			document.getElementById('vkr').removeAttribute('checked');
			document.getElementById('no').removeAttribute('checked');
			document.getElementById('vekr').setAttribute('checked', 'checked');
		}
		
		if(type=='no')
		{
			document.getElementById('ekr').disabled=true;
			document.getElementById('kr').disabled=true;
			document.getElementById('vkr').removeAttribute('checked');
			document.getElementById('vekr').removeAttribute('checked');
			document.getElementById('no').setAttribute('checked', 'checked');
		}
	<?	
	}
	
	?>
	
}
function new_runmagic(title, magic, name, name1, type, abit ){
	var submbutton='';
	var magicformcontent='';
    var el = document.getElementById("hint3");

	if (type=='' || type=='f' || type=='ff' || type=='1' || type=='11' || type=='2')

		{submbutton='</TD><TD width=30><INPUT TYPE="submit" value=" »» "></TD></TR></TABLE></FORM></td></tr></table>';}
	else
		{submbutton='<br><center><INPUT TYPE="submit" value=" »» "></center></TD></TR></TABLE></FORM></td></tr></table>';}

	if (type=='')
		{
		 magicformcontent=	'Укажите логин персонажа:<small><BR>(можно щелкнуть по логину в чате)</TD></TR><TR><TD align=left><INPUT TYPE=text id="'+name+'" NAME="'+name+'">'+	'<select style="background-color:#eceddf; color:#000000;" name="timer"><option value=15>15 мин<option value=30>30 мин<option value=60>1 час'+	'<option value=180>3 часа<option value=360>6 часов<option value=720>12 часов<option value=1440>сутки<? if (($user[klan]=='radminion') OR ($user[klan]=='Adminion')) echo "<option value=2880>2-е суток<option value=4320>3-е суток<option value=10080>неделя"; ?></select><br>Продлить? <input type=checkbox name="updatetime">';
		<?php if (ADMIN) {
		?>
			magicformcontent += '<br>Или количество дней: <input type="text" name="admindays">';
		<?php
		}
		?>

		}
	if (type=='0')	
		{
		 magicformcontent=  '<INPUT TYPE=hidden name=sd4 value=""><INPUT TYPE=hidden NAME="use" value="'+magic+'">'+'Использовать сейчас?<small><INPUT id="'+name+'" TYPE=hidden NAME="'+name+'" value=1>';
		 submbutton='<br><center><INPUT TYPE="button" onclick="closehint3();" value=" НЕТ "><INPUT TYPE="submit" value=" ДА "></center></TD></TR></TABLE></FORM></td></tr></table>';
		}
	if (type=='f')
		{
		 magicformcontent=	'Укажите логин персонажа:<small><BR>(можно щелкнуть по логину в чате)</TD></TR><TR><TD align=left><INPUT TYPE=text id="'+name+'" NAME="'+name+'">'+	'<select style="background-color:#eceddf; color:#000000;" name="timer"><option value=15>15 мин<option value=30>30 мин<option value=60>1 час'+	'<option value=180>3 часа<option value=360>6 часов<option value=720>12 часов<option value=1440>сутки<option value=4320>3 суток<option value=10080>неделя</select><br>Продлить? <input type=checkbox name="updatetime">';
		<?php if (ADMIN) {
		?>
			magicformcontent += '<br>Или количество дней: <input type="text" name="admindays">';
		<?php
		}
		?>
		}
	if (type=='ff')
		{
		 magicformcontent=	'Укажите логин персонажа:<small><BR>(можно щелкнуть по логину в чате)</TD></TR><TR><TD align=left><INPUT TYPE=text id="'+name+'" NAME="'+name+'">'+	'<select style="background-color:#eceddf; color:#000000;" name="timer"><option value=15>15 мин<option value=30>30 мин<option value=60>1 час'+	'<option value=180>3 часа<option value=360>6 часов<option value=720>12 часов<option value=1440>сутки<option value=4320>3 суток<option value=10080>неделя<option value=525600>год</select>';
		}
	if (type=='1')
		{
		 magicformcontent='Укажите логин персонажа:<small><BR>(можно щелкнуть по логину в чате)</TD></TR><TR><TD align=left><INPUT id="'+name+'" TYPE=text NAME="'+name+'">';
		}
	if (type=='11')
		{
		 magicformcontent='Укажите номер карты:<small><BR><TD></TR><TR><TD align=left><INPUT id="'+name+'" TYPE=text NAME="'+name+'">';
		}

	if (type=='10')
		{
		 magicformcontent='Введите название города(capital,avalon)<small><BR><TD></TR><TR><TD align=left><INPUT id="'+name+'" TYPE=text NAME="'+name+'">';
		}

	if (type=='12')
		{
		 magicformcontent='Укажите номер Боя:<small><BR><TD></TR><TR><TD align=left><INPUT id="'+name+'" TYPE=text NAME="'+name+'">';
		}

	if (type=='13')
		{
		 magicformcontent='Укажите логин персонажа:<small><BR>(можно щелкнуть по логину в чате)</TD></TR><TR><TD align=left><INPUT TYPE=text id="'+name+'" NAME="'+name+'">'+'</TD></TR><TR><TD align=left>Введите название города</TD></TR><TR><TD align=left><INPUT id="city" TYPE=text NAME="city">' ;
		}
		
	if (type=='2')
		{
		 magicformcontent='Укажите логин персонажа:<small><BR>(можно щелкнуть по логину в чате)</TD></TR><TR><TD align=left><INPUT id="'+name+'" TYPE=text NAME="'+name+'">'+	'<? if ($_SERVER['PHP_SELF']=='/orden.php') { ?><select style="background-color:#eceddf; color:#000000;" name="timer"><option value=2>2 дня<option value=3>3 дня<option value=7>неделя<option value=14>2 недели'+	'<option value=30>1 месяц<option value=60>2 месяца<option value=365>бессрочно</select> <? } ?> ';
		}

	if (type=='155')
		{
		 magicformcontent='Укажите логин персонажа:<small><BR>(можно щелкнуть по логину в чате)</TD></TR><TR><TD align=left><INPUT id="'+name+'" TYPE=text NAME="'+name+'"></TD></TR><TR><TD>Причина:<br> <input type="text" id="reasonbl" name="reasonbl"><br>До:<br> <input type="text" value="<?php echo date("d-m-Y",time()+30*24*3600); ?>" id="bantime" name="bantime"><br>';
		 submbutton='</TD><TD width=30><INPUT OnClick=\'if(!document.getElementById("reasonbl").value.length) {alert("Причина обязательна"); return false;} else return true;\' TYPE="submit" value=" »» "></TD></TR></TABLE></FORM></td></tr></table>';
	}

	if (type=='144')
		{
		 magicformcontent='Укажите логин персонажа:<small><BR>(можно щелкнуть по логину в чате)</TD></TR><TR><TD align=left><INPUT id="'+name+'" TYPE=text NAME="'+name+'"><select style="background-color:#eceddf; color:#000000;" name="timer" id="timer" onchange="check(\'timer\')"><option value=2>2 дня<option value=3>3 дня<option value=7>неделя<option value=14>2 недели<option value=30>1 месяц <option value=31>1 месяц (руны)<option value=60>2 месяца<option value=365>бессрочно</select></TD></TR><TR><TD>Выкуп<br>'
		 +'<input type=radio onclick="check(\'kr\')" name=chose value=kr id=vkr><input type=text name=kr id=kr size=6 disabled>кр.<br>';
		 <?if(ADMIN || $user[id]==7937)
		 {?> 
		 
		 magicformcontent=magicformcontent+'<input type=radio onclick="check(\'ekr\')" name=chose id=vekr value=ekr><input type=text name=ekr id=ekr size=6 disabled>екр. <br>';
		 /* +'<input type=radio onclick="check(\'no\')" name=chose id=no value=no>Без выкупа.'; */
		 
		 <?
		 }?>
		
		}
	if (type=='3')
		{
		 magicformcontent='Введите текст комментария:<small><BR>(смайлики работают)</TD></TR><TR><TD align=left><INPUT id="'+name+'" TYPE=text NAME="'+name+'">';
		}		
		
		
	if (type=='4')
		{
		 magicformcontent='Укажите логин жениха: <INPUT id="'+name+'" TYPE=text NAME="'+name+'">'+	'<br>Укажите логин невесты: <INPUT TYPE=text NAME="'+name1+'">';
		}
	if (type=='5')
		{
		 magicformcontent=	'Укажите текущий ник: <INPUT id="'+name+'" TYPE=text NAME="'+name+'" >'+	'<br>Укажите новый ник: <INPUT TYPE=text NAME="'+name1+'" maxlength="23">';
		}
	if (type=='6')
		{
		 magicformcontent='Введите ник персонажа: <INPUT id="'+name+'" TYPE=text NAME="'+name+'">'+	'<br>Укажите новую дату (дд-мм-гггг):<br> <INPUT TYPE=text NAME="'+name1+'">';
		}

	if (type=='7')
	 {
	 magicformcontent=  '<INPUT TYPE=hidden name=sd4 value=""><INPUT TYPE=hidden NAME="use" value="'+magic+'">'+'Использовать сейчас?<small><INPUT id="'+name+'" TYPE=hidden NAME="'+name+'" value=1>';
	 submbutton='<br><center><INPUT TYPE="button" onclick="closehint3();" value=" НЕТ "><INPUT TYPE="submit" value=" ДА "></center></TD></TR></TABLE></FORM></td></tr></table>';
	}



	el.innerHTML = '<table width=100% cellspacing=1 cellpadding=0 bgcolor=CCC3AA><tr><td align=center><B>'+title+'</td><td width=20 align=right valign=top style="cursor: pointer" onclick="closehint3();"><BIG><B>x</b></BIG></td></tr><tr><td colspan=2>'+
	'<form action="?" method=POST><table width=100% cellspacing=0 cellpadding=2 bgcolor=FFF6DD><tr><td><INPUT TYPE=hidden NAME="abit" value="'+abit+'"><INPUT TYPE=hidden name=sd4 value="<? echo @$user['id']; ?>"> <INPUT TYPE=hidden NAME="use" value="'+magic+'">'+magicformcontent+submbutton;
	el.style.visibility = "visible";
	el.style.left = 100 + 'px';
	el.style.top = 100 + 'px';
	document.getElementById(name).focus();
	Hint3Name = name;
}

function runmagic1(title, magic, name){
    var el = document.getElementById("hint3");
	el.innerHTML = '<table width=100% cellspacing=1 cellpadding=0 bgcolor=CCC3AA><tr><td align=center><B>'+title+'</td><td width=20 align=right valign=top style="cursor: pointer" onclick="closehint3();"><BIG><B>x</b></BIG></td></tr><tr><td colspan=2>'+
	'<form action="" method=POST><table width=100% cellspacing=0 cellpadding=2 bgcolor=FFF6DD><tr><td colspan=2><INPUT TYPE=hidden name=sd4 value="<? echo @$user['id']; ?>"> <INPUT TYPE=hidden NAME="use" value="'+magic+'">'+
	'Укажите логин персонажа:<small><BR>(можно щелкнуть по логину в чате)</TD></TR><TR><TD align=left><INPUT id="'+name+'" TYPE=text NAME="'+name+'">'+
	'</TD><TD width=30><INPUT TYPE="submit" value=" »» "></TD></TR></TABLE></FORM></td></tr></table>';
	el.style.visibility = "visible";
	el.style.left = 100 + 'px';
	el.style.top = 100 + 'px';
	document.getElementById(name).focus();
	Hint3Name = name;
}
function comment_fight(title, magic, name){
	document.all("hint3").innerHTML = '<form action="" method=POST><table width=100% cellspacing=1 cellpadding=0 bgcolor=CCC3AA><tr><td align=center><B>'+title+'</td><td width=20 align=right valign=top style="cursor: pointer" onclick="closehint3();"><BIG><B>x</td></tr><tr><td colspan=2>'+
	'<table width=100% cellspacing=0 cellpadding=2 bgcolor=FFF6DD><tr><form action="" method=POST><INPUT TYPE=hidden name=sd4 value="<? echo @$user['id']; ?>"> <INPUT TYPE=hidden NAME="use" value="'+magic+'"><td colspan=2>'+
	'Введите текст комментария:<small><BR>(смайлики работают)</TD></TR><TR><TD width=80% align=right><INPUT TYPE=text NAME="'+name+'"></TD><TD width=20%><INPUT TYPE="submit" value=" »» "></TD></TR></TABLE></td></tr></table></form>';
	document.all("hint3").style.visibility = "visible";
	document.all("hint3").style.left = 100;
	document.all("hint3").style.top = 100;
	document.all(name).focus();
	Hint3Name = name;
}	
function closehint3(){
	document.getElementById("hint3").style.visibility="hidden";
    Hint3Name='';
}
