var Hint3Name = '';

function new_runmagic(but,id,what){
//alert('test');
	var title='Впишите комментарий';
	var submbutton='';
	var magicformcontent='';
    var el = document.getElementById("hint3");
	magicformcontent= "</TD></TR><TR><TD align left><br><INPUT TYPE=text id=\"inp\" NAME=\"coment\">";
	submbutton='<br><br><center><INPUT id="button3" TYPE="submit" value=" «« ОТПРАВИТЬ »» "></center><br></TD></TR></TABLE></FORM></td></tr></table>';
//	alert (what);
	if (what==1)
	{el.innerHTML = '<table width=100% cellspacing=1 cellpadding=0 bgcolor=#CCC3AA><tr><td align=left bgcolor=#CCC3AA width=410><B>'+title+'</td><td width=20 align=right valign=top bgcolor=#CCC3AA style="cursor: pointer" onclick="closehint3();"><BIG><B>x</b></BIG></td></tr><tr><td colspan=2>'+
	'<form action="index.php" method=POST><table width=100% cellspacing=0 cellpadding=2 bgcolor=#FFF6DD><tr><td><INPUT TYPE=hidden name=act value=makedone><INPUT TYPE=hidden name=id value='+id+'>'+magicformcontent+submbutton;}
	if (what==2)
	{el.innerHTML = '<table width=100% cellspacing=1 cellpadding=0 bgcolor=#CCC3AA><tr><td align=left bgcolor=#CCC3AA width=410><B>'+title+'</td><td width=20 align=right valign=top bgcolor=#CCC3AA style="cursor: pointer" onclick="closehint3();"><BIG><B>x</b></BIG></td></tr><tr><td colspan=2>'+
	'<form action="index.php" method=POST><table width=100% cellspacing=0 cellpadding=2 bgcolor=#FFF6DD><tr><td><INPUT TYPE=hidden name=act value=answer><INPUT TYPE=hidden name=id value='+id+'>'+magicformcontent+submbutton;}
	if (what==3)
	{el.innerHTML = '<table width=100% cellspacing=1 cellpadding=0 bgcolor=#CCC3AA><tr><td align=left bgcolor=#CCC3AA width=410><B>'+title+'</td><td width=20 align=right valign=top bgcolor=#CCC3AA style="cursor: pointer" onclick="closehint3();"><BIG><B>x</b></BIG></td></tr><tr><td colspan=2>'+
	'<form action="index.php" method=POST><table width=100% cellspacing=0 cellpadding=2 bgcolor=#FFF6DD><tr><td><INPUT TYPE=hidden name=act value=makepnotclear><INPUT TYPE=hidden name=id value='+id+'>'+magicformcontent+submbutton;}
	if (what==4)
	{el.innerHTML = '<table width=100% cellspacing=1 cellpadding=0 bgcolor=#CCC3AA><tr><td align=left bgcolor=#CCC3AA width=410><B>'+title+'</td><td width=20 align=right valign=top bgcolor=#CCC3AA style="cursor: pointer" onclick="closehint3();"><BIG><B>x</b></BIG></td></tr><tr><td colspan=2>'+
	'<form action="index.php" method=POST><table width=100% cellspacing=0 cellpadding=2 bgcolor=#FFF6DD><tr><td><INPUT TYPE=hidden name=act value=makenreset><INPUT TYPE=hidden name=id value='+id+'>'+magicformcontent+submbutton;}

	if (what==5)
	{el.innerHTML = '<table width=100% cellspacing=1 cellpadding=0 bgcolor=#CCC3AA><tr><td align=left bgcolor=#CCC3AA width=410><B>'+title+'</td><td width=20 align=right valign=top bgcolor=#CCC3AA style="cursor: pointer" onclick="closehint3();"><BIG><B>x</b></BIG></td></tr><tr><td colspan=2>'+
	'<form action="index.php" method=POST><table width=100% cellspacing=0 cellpadding=2 bgcolor=#FFF6DD><tr><td><INPUT TYPE=hidden name=act value=makenresetemail><INPUT TYPE=hidden name=id value='+id+'>'+magicformcontent+submbutton;}

	if (what==6)
	{el.innerHTML = '<table width=100% cellspacing=1 cellpadding=0 bgcolor=#CCC3AA><tr><td align=left bgcolor=#CCC3AA width=410><B>'+title+'</td><td width=20 align=right valign=top bgcolor=#CCC3AA style="cursor: pointer" onclick="closehint3();"><BIG><B>x</b></BIG></td></tr><tr><td colspan=2>'+
	'<form action="index.php" method=POST><table width=100% cellspacing=0 cellpadding=2 bgcolor=#FFF6DD><tr><td><INPUT TYPE=hidden name=act value=makenklanchange><INPUT TYPE=hidden name=id value='+id+'>'+magicformcontent+submbutton;}

	if (what==7)
	{el.innerHTML = '<table width=100% cellspacing=1 cellpadding=0 bgcolor=#CCC3AA><tr><td align=left bgcolor=#CCC3AA width=410><B>'+title+'</td><td width=20 align=right valign=top bgcolor=#CCC3AA style="cursor: pointer" onclick="closehint3();"><BIG><B>x</b></BIG></td></tr><tr><td colspan=2>'+
	'<form action="index.php" method=POST><table width=100% cellspacing=0 cellpadding=2 bgcolor=#FFF6DD><tr><td><INPUT TYPE=hidden name=act value=makenaddrekrut><INPUT TYPE=hidden name=id value='+id+'>'+magicformcontent+submbutton;}

	if (what==8)
	{el.innerHTML = '<table width=100% cellspacing=1 cellpadding=0 bgcolor=#CCC3AA><tr><td align=left bgcolor=#CCC3AA width=410><B>'+title+'</td><td width=20 align=right valign=top bgcolor=#CCC3AA style="cursor: pointer" onclick="closehint3();"><BIG><B>x</b></BIG></td></tr><tr><td colspan=2>'+
	'<form action="index.php" method=POST><table width=100% cellspacing=0 cellpadding=2 bgcolor=#FFF6DD><tr><td><INPUT TYPE=hidden name=act value=makenremoverekrut><INPUT TYPE=hidden name=id value='+id+'>'+magicformcontent+submbutton;}

	if (what==9)
	{el.innerHTML = '<table width=100% cellspacing=1 cellpadding=0 bgcolor=#CCC3AA><tr><td align=left bgcolor=#CCC3AA width=410><B>'+title+'</td><td width=20 align=right valign=top bgcolor=#CCC3AA style="cursor: pointer" onclick="closehint3();"><BIG><B>x</b></BIG></td></tr><tr><td colspan=2>'+
	'<form action="index.php" method=POST><table width=100% cellspacing=0 cellpadding=2 bgcolor=#FFF6DD><tr><td><INPUT TYPE=hidden name=act value=makenresetbd><INPUT TYPE=hidden name=id value='+id+'>'+magicformcontent+submbutton;}

	if (what==10)
	{el.innerHTML = '<table width=100% cellspacing=1 cellpadding=0 bgcolor=#CCC3AA><tr><td align=left bgcolor=#CCC3AA width=410><B>'+title+'</td><td width=20 align=right valign=top bgcolor=#CCC3AA style="cursor: pointer" onclick="closehint3();"><BIG><B>x</b></BIG></td></tr><tr><td colspan=2>'+
	'<form action="index.php" method=POST><table width=100% cellspacing=0 cellpadding=2 bgcolor=#FFF6DD><tr><td><INPUT TYPE=hidden name=act value=makenchangesex><INPUT TYPE=hidden name=id value='+id+'>'+magicformcontent+submbutton;}


	var x=findPosX(but);
	var y=findPosY(but);
	var posx=x-150;
	var posy=y-200;
	el.style.visibility = "visible";
	el.style.left = posx + 'px';
	el.style.top = posy + 'px';
	el.style.zIndex = 999;
	document.getElementById('inp').focus();
	Hint3Name = 'coment';
}		
function closehint3(){
	document.getElementById("hint3").style.visibility="hidden";
    Hint3Name='';
}	
function findPosX(obj)
  {
    var curleft = 0;
    if(obj.offsetParent)
        while(1) 
        {
          curleft += obj.offsetLeft;
          if(!obj.offsetParent)
            break;
          obj = obj.offsetParent;
        }
    else if(obj.x)
        curleft += obj.x;
    return curleft;
  }

  function findPosY(obj)
  {
    var curtop = 0;
    if(obj.offsetParent)
        while(1)
        {
          curtop += obj.offsetTop;
          if(!obj.offsetParent)
            break;
          obj = obj.offsetParent;
        }
    else if(obj.y)
        curtop += obj.y;
    return curtop;
  }

function CheckComment() {
	if (document.getElementById("otherserv").checked) {
		var content=document.getElementById("commentarea").value;
		if (content=="") {
			alert("Вы не заполнили поле Комментарий!");
			return false;
		}
	}

	var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
	var address = document.getElementById("comemail").value;
	if(reg.test(address) == false) {
      		alert('Введите корректный email адрес');
      		return false;
   	}		

	document.getElementById("mainform").submit();
}