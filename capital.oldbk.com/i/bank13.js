var ie = document.all;
var moz = (navigator.userAgent.indexOf("Mozilla") != -1);
var opera = window.opera;
var brodilka = "";
if(ie && !opera){brodilka = "ie";}
else if(moz){brodilka = "moz";}
else if(opera){brodilka = "opera";}
var inputMasks = new Array();

function kdown(inpt, ev){
    var id = inpt.getAttribute("id");
    var idS = id.substring(0, id.length - 1);
    var idN = Number(id.substring(id.length - 1));
    inputMasks[idS].BlKPress(idN, inpt, ev);
}

function kup(inpt, ck){
    if(Number(inpt.getAttribute("size")) == inpt.value.length){
        var id = inpt.getAttribute("id");
        var idS = id.substring(0, id.length - 1);
        var idN = Number((id.substring(id.length - 1))) + 1;
        var t = document.getElementById(idS + idN);
        if(ck!=8 && ck!=9){
            if(t){t.focus();}
        } else if (ck==8) {
            inpt.value = inpt.value.substring(0, inpt.value.length - 1);
        }
    }
}


function setupwm(sb,uid,param)
{
	kol=document.getElementById('qbil').value;
	if (kol>=25)
		{
		document.getElementById('wmdata').value="0:"+sb+':'+(param-1)+':'+uid;		
		}
		else
		{
		document.getElementById('wmdata').value="0:"+sb+':'+param+':'+uid;
		}
}


function callcrublord(kol,rub,price)
{

if (kol>=25)
	{
	price=price-1;
	}
document.getElementById('qrub').value=(((rub*price)*kol)).toFixed(0);
document.getElementById('lordcost').innerHTML='1 пропуск = '+(((rub*price))).toFixed(0);
}

function callcusdlord(kol,rub,price)
{

if (kol>=25)
	{
	price=price-1;
	}
document.getElementById('qrub').value=(((rub*price)*kol)).toFixed(2);
document.getElementById('lordcost').innerHTML='1 пропуск = '+(((rub*price))).toFixed(2);
}

function callcrubrep(rep,kurs)
{
document.getElementById('qrub').value=(((rep/600)*kurs)).toFixed(0);
}

function callcrubrep(rep,kurs)
{
document.getElementById('qrub').value=(((rep/600)*kurs)).toFixed(0);
}

function callcrep(rub,kurs)
{
document.getElementById('qrep').value=((rub/kurs)*600).toFixed(0);
}

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
document.getElementById('qrub').value=(ekr*kurs).toFixed(0);
}

function callcrubr(ekr,kurs)
{
document.getElementById('qrub').value=Math.ceil(ekr*kurs);
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

function callpodar(kurs)
{
$t=document.getElementById('podarid').value-200000;
$k=document.getElementById('qbil').value;
document.getElementById('qrub').value=(kurs*$t*$k).toFixed(2);
}

function callpodarw(kurs)
{
$t=document.getElementById('podarid').value.split(':');
$tt=$t[2]-200000;
$k=document.getElementById('qbil').value;
document.getElementById('qrub').value=Math.ceil(kurs*$tt*$k);
}

function callpodarwp(kurs)
{
$t=document.getElementById('podarid').value.split(':');
$tt=$t[2]-200000;
$k=document.getElementById('qbil').value;
document.getElementById('qrub').value=(kurs*$tt*$k).toFixed(2);
}

function callelk(kurs,p)
{
if (p==0)
{
$t=document.getElementById('buketid').value;
}
else
{
$ta=document.getElementById('buketid').value.split(':');
$t=$ta[2];
}
$prisearray = {55510350:10,55510351:50};

$co=($prisearray[$t]).toFixed(2);
if (kurs>1)
	{
	document.getElementById('qrub').value=Math.ceil(kurs*$co);	
	}
	else
	{
	document.getElementById('qrub').value=($co);
	}


	  if ($t=='55510350')
	  	{
			document.getElementById('55510350').style.display='block';
			document.getElementById('55510351').style.display='none';			
	  	}
	  	else
	  	{
			document.getElementById('55510351').style.display='block';
			document.getElementById('55510350').style.display='none';				  	
	  	}

}

function callbuket(kurs,p)
{
if (p==0)
{
$t=document.getElementById('buketid').value;
}
else
{
$ta=document.getElementById('buketid').value.split(':');
$t=$ta[2];
}
//$prisearray = {410021:10,410022:10,410023:10};

$co=10;
if (kurs>1)
	{
	document.getElementById('qrub').value=Math.ceil(kurs*$co);	
	}
	else
	{
	document.getElementById('qrub').value=($co);
	}

	        for (var i = 410021; i <= 410026; i++) 
	        {
	        		  if ($t==i)
	        		  {
      		  			document.getElementById(i).style.display='block';
	        		  }
	        		  else
	        		  {
       		  			document.getElementById(i).style.display='none';
	        		  }
	        }

}


function Mask(fieldObj){
    var template = "(\\d{3})\\d{3}-\\d{2}-\\d{2}";
    var parts = [];
    var blocks = [];
    var order = [];
    var value = "";

    var Block = function(pattern){
        var inptsize = Number(pattern.substring(3, pattern.indexOf('}')));
        var idS = fieldObj.getAttribute("id");
        var idN = blocks.length;
        var text = "";

        var checkKey = function(ck){
            return ((ck >= 48) && (ck <= 57)) || ((ck >= 96) && (ck <= 105)) || (ck == 27) || (ck == 8) || (ck == 9) || (ck == 13) || (ck == 45) || (ck == 46) || (ck == 144) || ((ck >= 33) && (ck <= 40)) || ((ck >= 16) && (ck <= 18)) || ((ck >= 112) && (ck <= 123));
        }

        this.makeInput = function(){
            return "<input type='text' " + "size='" + inptsize + "' maxlength='" + inptsize + "'"  + " id='" + idS + idN + "' onKeyDown='kdown(this, event)' onKeyUp='kup(this, event.keyCode)' value='" + text + "'>";
        }

        this.key = function(inpt, ev){
            if(opera) return;
            if(!checkKey(ev.keyCode)){
                switch(brodilka){
                    case "ie":
                        ev.cancelBubble = true;
                        ev.returnValue = false;
                    break;
                    case "moz":
                        ev.preventDefault();
                        ev.stopPropagation();
                    break;
                    case "opera":
                    break;
                    default:
                }
                return;
            }

            if(ev.keyCode == 8 && inpt.value == ""){
                var tid = inpt.getAttribute("id");
                var tidS = tid.substring(0, tid.length - 1);
                var tidN = Number(tid.substring(tid.length - 1)) - 1;
                var t = document.getElementById(tidS + tidN);
                if(t != null) t.focus();
            }
        }

        this.getText = function(){
            text = document.getElementById(idS + idN).value;
            return text;
        }

        this.setText = function(val){
            text = val;
        }

        this.getSize = function() {
            return inptsize;
        }
    }

    this.drawInputs = function(){
        var inputStr = "<span class='Field'>";
        var p = 0;
        var b = 0;
        for (var i = 0; i < order.length; i++) {
            if (order[i] == "p") {
                inputStr += parts[p];
                p++;
            } else {
                inputStr += blocks[b].makeInput();
                b++;
            }
        }
        inputStr += "</span>";
        document.getElementById("div_" + fieldObj.getAttribute("id")).innerHTML = inputStr;
        fieldObj.style.display = "none";
    }

    this.buildFromFields = function() {// constructor
        var tmpstr = template;
        while(tmpstr.indexOf("\\") != -1){
            var slash = tmpstr.indexOf("\\");
            var d = "";
            if(tmpstr.substring(0, slash) != ""){
                parts[parts.length] = tmpstr.substring(0, slash);
                order[order.length] = 'p';
                tmpstr = tmpstr.substring(slash);
            }
            var q = tmpstr.indexOf('}');
            blocks[blocks.length] = new Block(tmpstr.substring(0, q + 1), d);
            tmpstr = tmpstr.substring(q + 1);
            order[order.length] = 'b';
        }
        if (tmpstr != "") {
            parts[parts.length] = tmpstr;
            order[order.length] = 'p';
        }
        this.drawInputs();
    }

    this.buildFromFields();

    this.BlKPress = function(idN, inpt, ev){
        blocks[idN].key(inpt, ev);
    }

    this.makeHInput = function(){
        var name = fieldObj.getAttribute("name");
        document.getElementById("div_" + fieldObj.getAttribute("id")).innerHTML =
            "<input type='text' readonly='readonly' name='" + name + "' value='" + this.getValue() + "'>";
    }

    this.getFName = function(){
        return fieldObj.getAttribute("name");
    }

    this.getValue = function(){
        value = "";
        var p = 0;
        var b = 0;
        for(var i = 0; i < order.length; i++){
            /*if(order[i] == 'p'){
                value += parts[p];
                p++;
            } else {
                value += blocks[b].getText();
                b++;
            }
            */
        	if (order[i] != 'p') {
        		value += blocks[b].getText();
        		b++;
        	}
        }
        return value;
    }

    this.check = function(){
        for(var i in blocks){
            if (blocks[i].getText().length == 0) return false;
        }
        return true;
    }
}


		function returned2(s){
			location.href='city.php?'+s+'tmp='+Math.random();
		}
		
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



function showitemschoice(title, type, script)
{
	var choicehtml = "<form style='display:none' id='formtarget' action='" + script + "' method=POST><input type='hidden' id='target' name='target'>";
	choicehtml += "</form><table width='100%' cellspacing='1' cellpadding='0' bgcolor='CCC3AA'>";
	choicehtml += "<tr><td align='center'><B>" + title + "</td>";
	choicehtml += "<td width='20' align='right' valign='top' style='cursor: pointer' onclick='closehint3(true);'>";
	choicehtml += "<big><b>x</td></tr><tr><td colspan='2' id='tditemcontainer'><div id='itemcontainer' style='width:100%'>";
	choicehtml += "</div></td></tr></table>";
	
	choicehtml += "<table width='100%' cellspacing='1' cellpadding='2' border='0' bgcolor='#FFF6DD'>";
	choicehtml += "<tr bgcolour='#c7c7c7'>";
	choicehtml += "<td width='50' align='center'>";
	choicehtml += "</td>";
	choicehtml += "<td align='center'>";
	choicehtml += "<br>";
	choicehtml += "<a style='cursor: pointer' onclick='setch(1);'>Обменять на еврокредиты (1$=1екр.)</a>";		
	choicehtml += "<br><br>";
	choicehtml += "<a style='cursor: pointer' onclick='setch(2);'>Обменять на репутацию (1$=3000реп.)</a>";		
	choicehtml += "<br><br>";	
	choicehtml += "</td>";
	choicehtml += "<td width='50' align='center'>";
	choicehtml += "</td></tr>";	
	choicehtml += "<table>";

	var el = document.getElementById("hint3");
	el.innerHTML = choicehtml;
	el.style.width = 400 + 'px';
	el.style.visibility = "visible";
	el.style.left = (document.body.clientWidth/2-200) + 'px';
	el.style.top = (document.body.clientHeight/2-200) + 'px';
	Hint3Name = "target";

	
}

function setch(u){
		var targetform = document.getElementById('formtarget');
		targetform.action += "&u="+u;
		targetform.submit();
}

function closehint3(clearstored){
	if(clearstored)
	{
		var targetform = document.getElementById('formtarget');
		targetform.action += "&clearstored=1";
		targetform.submit();
	}
	document.getElementById("hint3").style.visibility="hidden";
    Hint3Name='';
}

