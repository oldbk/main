var Hint3Name = '';

step=0;
function errmess(s)
{
  messid.innerHTML='<B>'+s+'</B>';
  highlight();
}
function highlight()
{
  if (step) return(0);
  step=10;
  setTimeout(dohi,50);
}

function dohi()
{
  var hx=new Array(0,1,2,3,4,5,6,7,8,9,"A","B","C","D","E","F");

  step--;
  messid.style.color="#"+hx[Math.floor(15-step/2)]+((step&1)?"F":"8")+"0000";
  if (step>0) setTimeout(dohi,50);
}

function fixspaces(s)
{
  while (s.substr(s.length-1,s.length)==" ") s=s.substr(0,s.length-1);
  while (s.substr(0,1)==" ") s=s.substr(1,s.length);
  return(s);
}

// Заголовок, название скрипта, имя поля с логином
function findlogin(title, script, name, defaultlogin)
{
	document.all("hint3").innerHTML = '<table border=0 width=100% cellspacing="1" cellpadding="0" bgcolor="#CCC3AA"><tr><td align=center><B>'+title+'</td><td width=20 align=right valign=top style="cursor: hand" onclick="closehint3();"><BIG><B>x</td></tr><tr><td colspan=2>'+
	'<table border=0 width=100% cellspacing="0" cellpadding="2" bgcolor="#FFF6DD"><tr><form action="'+script+'" method=POST name=slform><td colspan=2>'+
	'Укажите логин персонажа:<small><BR>(можно щелкнуть по логину в чате)</TD></TR><TR><TD width=50% align=right><INPUT TYPE="text" NAME="'+name+'" value="'+defaultlogin+'"></TD><TD width=50%><INPUT type=image SRC="/i/b__ok.gif" WIDTH="25" HEIGHT="18" BORDER=0 ALT="" onclick="slform.'+name+'.value=fixspaces(slform.'+name+'.value);"></TD></TR></FORM></TABLE></td></tr></table>';
	document.all("hint3").style.visibility = "visible";
	document.all("hint3").style.left = 100;
	document.all("hint3").style.top = 60;
	document.all(name).focus();
	Hint3Name = name;
}

// Для магии. Заголовок, название скрипта, название магии, номер вещицы в рюкзаке, логин по умолчанию, описание доп. поля
function magicklogin(title, script, magickname, n, defaultlogin, extparam)
{
	var s = '<table border=0 width=100% cellspacing="1" cellpadding="0" bgcolor="#CCC3AA"><tr><td align=center><B>'+title+'</td><td width=20 align=right valign=top style="cursor: hand" onclick="closehint3();"><BIG><B>x</td></tr><tr><td colspan=2>'+
	'<table border=0 width=100% cellspacing="0" cellpadding="2" bgcolor="#FFF6DD"><tr><form action="'+script+'" method=POST name=slform><input type=hidden name="use" value="'+magickname+'"><input type=hidden name="n" value="'+n+'"><td colspan=2>'+
	'Укажите логин персонажа:<small><BR>(можно щелкнуть по логину в чате)</TD></TR><TR><TD width=50% align=right><INPUT TYPE="text" NAME="param" value="'+defaultlogin+'"></TD><TD width=50%><INPUT type=image SRC="/i/b__ok.gif" WIDTH="25" HEIGHT="18" BORDER=0 ALT="" onclick="slform.param.value=fixspaces(slform.param.value);"></TD></TR>';
	if (extparam != null && extparam != '') {
		s = s + '<TR><td colspan=2>'+extparam+'<BR><INPUT TYPE="text" NAME="param2" size=20></TD></TR>';
	}
	s = s + '</FORM></TABLE></td></tr></table>';
	document.all("hint3").innerHTML = s;
	document.all("hint3").style.visibility = "visible";
	document.all("hint3").style.left = 100;
	document.all("hint3").style.top = document.body.scrollTop+50;
	document.all("param").focus();
	Hint3Name = 'param';
}

// Магия
function UseMagick(title, script, name, extparam, n, extparam2) {
   if ((extparam != null)&&(extparam != '')) {

	var t1='text',t2='text';

	if (extparam.substr(0,1) == "!")
	{
		t1='password';
		extparam=extparam.substr(1,extparam.length);
	}
	var s = '<table border=0 width=100% cellspacing="1" cellpadding="0" bgcolor="#CCC3AA"><tr><td align=center><B>'+title+'</td><td width=20 align=right valign=top style="cursor: hand" onclick="closehint3();"><BIG><B>x</td></tr><tr><td colspan=2>'+
	'<table border=0 width=100% cellspacing="0" cellpadding="2" bgcolor="#FFF6DD"><tr><form action="'+script+'" method=POST name=slform><input type=hidden name="use" value="'+name+'"><input type=hidden name="n" value="'+n+'"><td colspan=2><NOBR><SMALL>'+
	extparam + ':</NOBR></TD></TR><TR><TD width=100% align=left>&nbsp;&nbsp;<INPUT tabindex=1 size=30 TYPE="'+t1+'" NAME="param" value=""></TD><TD width=10%><INPUT type=image SRC="/i/b__ok.gif" WIDTH="25" HEIGHT="18" BORDER=0 ALT="" tabindex=3></TD></TR>';
	if (extparam2 != null && extparam2 != '') {
		if (extparam2.substr(0,1) == "!")
		{
			t2='password';
			extparam2=extparam2.substr(1,extparam2.length);
		}
		s = s + '<TR><td colspan=2><NOBR><SMALL>'+extparam2+':</NOBR><TR colspan=2><TD>&nbsp;&nbsp;<INPUT tabindex=2 TYPE="'+t2+'" NAME="param2" size=30></TD></TR>';
	}
	s = s + '</FORM></TABLE></td></tr></table>';
	document.all("hint3").innerHTML = s;
	document.all("hint3").style.visibility = "visible";
	document.all("hint3").style.left = 100;
	document.all("hint3").style.top = document.body.scrollTop+50;
	document.all("param").focus();
	Hint3Name = 'param';
   } else {
     if (confirm('Использовать сейчас?')) { location=script+'?use='+name+'&n='+n; }
   }
}

// Закрывает окно ввода логина
function closehint3()
{
	document.all("hint3").style.visibility="hidden";
    Hint3Name='';
}

// Для боевой магии. Заголовок, название магии, номер вещицы в рюкзаке
function Bmagicklogin(title, magickname, n)
{
   if (defend==false)
   {
      errmess("Блок не выбран.");
      return false;
   }

	document.all("hint3").innerHTML = '<table border=0 width=100% cellspacing="1" cellpadding="0" bgcolor="#CCC3AA"><tr><td align=center><B>'+title+'</td><td width=20 align=right valign=top style="cursor: hand" onclick="closehint3();"><BIG><B>x</td></tr><tr><td colspan=2>'+
	'<table border=0 width=100% cellspacing="0" cellpadding="2" bgcolor="#FFF6DD"><tr><form name="bmagic" action="/battle.pl" method=POST onsubmit="bmagic.mdefend.value=defend;"><input type=hidden name="use" value="'+magickname+'"><input type=hidden name="n" value="'+n+'"><input type=hidden name="mdefend" value="'+defend+'"><input type=hidden name="enemy" value="'+enemy+'"><input type=hidden name="myid" value="'+myid+'"><td colspan=2>'+
	'Укажите логин персонажа:<small><BR>(можно щелкнуть по логину в чате)</TD></TR><TR><TD width=50% align=right><INPUT TYPE="text" NAME="param"></TD><TD width=50%><INPUT type=image SRC="/i/b__ok.gif" WIDTH="25" HEIGHT="18" BORDER=0 ALT="" onclick="bmagic.param.value=fixspaces(bmagic.param.value);"></TD></TR></FORM></TABLE></td></tr></table>';
	document.all("hint3").style.visibility = "visible";
	document.all("hint3").style.left = 100;
	document.all("hint3").style.top = 60;
	document.all("param").focus();
	Hint3Name = 'param';
}

// Магия
function BUseMagick(name, extparam, n) {

   if (defend==false)
   {
      errmess("Блок не выбран.");
      return false;
   }

   if ((extparam != null)&&(extparam != '')) {
     var s = prompt(extparam+':', '');
     if ((s != null)&&(s != '')) {
       re = /\%/g; s=s.replace(re, "%25");
       re = /\+/g; s=s.replace(re, "%2B");
	   re = /\#/g; s=s.replace(re, "%23");
	   re = /\?/g; s=s.replace(re, "%3F");
	   re = /\&/g; s=s.replace(re, "%26");
       window.location.href='/battle.pl?use='+name+'&param='+s+'&n='+n+'&mdefend='+defend+'&enemy='+enemy+'&myid='+myid;

	 }
   } else {
     if (confirm('Использовать сейчас?')) { location='/battle.pl?use='+name+'&n='+n+'&mdefend='+defend+'&enemy='+enemy+'&myid='+myid;}
   }
}

