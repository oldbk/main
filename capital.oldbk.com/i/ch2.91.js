document.onmousedown = Down;
function Down() {top.CtrlPress = window.event.ctrlKey}

// Разрешенные смайлики
var sm = new Array("horse",60,40,  "hug",48,20, "laugh",15,15,  "susel",70,34 , "fingal",22,15,  "eek",15,15, "flowers",28,29, "smoke",20,20,  "hi",31,28,  "bye",15,15,
"king",21,22, "boks",62,28,  "gent",15,21,  "lady",15,19,  "tongue",15,15,  "smil",16,16,  "rotate",15,15,
"ponder",21,15,  "bow",15,21, "angel",42,23, "angel2",26,25,  "hello",25,27,  "dont",26,26,  "idea",26,27,  "mol",27,22,  "super",26,28,
"beer",15,15,  "drink",19,17,  "baby",15,18,  "tongue2",15,15,  "sword",49,18,  "agree",37,15,
"loveya",27,15,  "kiss",15,15,  "kiss2",15,15,  "kiss3",15,15,  "kiss4",37,15,  "rose",15,15,  "love",27,28,
"love2", 55,24, "inv",80,20,
"confused",15,22,  "yes",15,15,  "no",15,15,  "shuffle",15,20,  "nono",22,19,  "maniac",70,25,  "privet",27,29,  "ok",22,16,  "ninja",15,15,
"pif",46,26,  "smash",30,26,  "gun",40,18,  "trup",20,20,
"mdr",56,15,  "mad",15,15,  "friday",57,28,  "cry",16,16,  "grust",15,15,  "rupor",38,18,
"fie",15,15,  "nnn",82,16,  "row",36,15,  "red",15,15,  "lick",15,15,
"help",23,15,  "wink",15,15, "jeer",26,16, "tease",33,19, "str", 35, 25, "kruger",34,27, "girl",37,26, "rev",40,25, "smile100",44,39,
"smile237",35,35, "smile289",46,31, "smile39",46,48,
"smile87",42,31, "smile434",39,28, "vamp",25,25, "s210",45,27,
"radio001",33,23,"radio002",50,30,"radio003",56,36,"wall",51,26,"smile26",42,25,"superng",45,41,
"doctor",35,35,"fflowers",57,38,"elix",30,35,"pal",25,21,"ura",31,36,"ggg",15,15,"balet",74,22,"2heart",46,33,"hell",46,28,"facepalm",28,24,"nybah",46,31,"danceny",70,45,"santa",50,50,
"crazy",20,27,"holiday",47,43,"laba",50,22,"cat",29,27

);

function AddLogin()
{	var o = window.event.srcElement;
	if (o.tagName == "SPAN") {
		var login=o.innerText;
		if (o.alt != null && o.alt.length>0) login=o.alt;
		var i1,i2;
		if ((i1 = login.indexOf('['))>=0 && (i2 = login.indexOf(']'))>0) login=login.substring(i1+1, i2);
		if (o.className.substr(0,1) == "p") { top.AddToPrivate(login, false) }
		else if (o.className == "s") {top.AddToSms(login, false) }
		else { top.AddTo(login) }
	}
}

function ClipBoard(text)
{
	//var holdtext.innerText = text;
	var Copied = text;//holdtext.createTextRange();
	Copied.execCommand("RemoveFormat");
	Copied.execCommand("Copy");
}

function OpenMenu(th) {
	var el, x, y, login, login2;
	el = document.all("oMenu");
	var o = window.event.srcElement;
	if (o.tagName != "SPAN") return true;
	x = window.event.clientX + document.documentElement.scrollLeft + document.body.scrollLeft - 3;
	y = window.event.clientY + document.documentElement.scrollTop + document.body.scrollTop;

	if (window.event.clientY + 72 > document.body.clientHeight) { y-=68 } else { y-=2 }
	login = o.innerText;
	if (o.alt != null && o.alt.length>0) login = o.alt;
	window.event.returnValue=false;
	var i1, i2;
	if ((i1 = login.indexOf('['))>=0 && (i2 = login.indexOf(']'))>0) login=login.substring(i1+1, i2);
	var login2 = login;
	login2 = login2.replace('%', '%25');
	while (login2.indexOf('+')>=0) login2 = login2.replace('+', '%2B');
	while (login2.indexOf('#')>=0) login2 = login2.replace('#', '%23');
	while (login2.indexOf('?')>=0) login2 = login2.replace('?', '%3F');

	el.innerHTML = '<A class=menuItem HREF="javascript:top.AddTo(\''+login+'\');cMenu()">TO</A>'+
	'<A class=menuItem HREF="javascript:top.AddToPrivate(\''+login+'\');cMenu()">PRIVATE</A>'+
	'<A class=menuItem HREF="" target=_blank onclick="OpenInfo(\''+login2+'\');return false;">INFO</A>'+
	'<A class=menuItem HREF="javascript:ClipBoard(\''+login+'\');cMenu()">COPY</A>';

	el.style.left = x + "px";
	el.style.top  = y + "px";
	el.style.visibility = "visible";
}

function OpenInfo(login) {
    var lar = login.split(/,/g);
    for (i=0;i<lar.length;i++) {
	if (lar[i].match(/^(k|c)lan$/i)) {
	    window.open('http://capitalcity.combats.com/encicl/clans.html');
	} else {
	    window.open('/inf.pl?login='+top.trim(lar[i]));
	}
    }

}

function cMenu() {
  document.all("oMenu").style.visibility = "hidden";
  document.all("oMenu").style.top="0px";
  top.frames['bottom'].window.document.F1.text.focus();
}

function closeMenu(event) {
  if (window.event && window.event.toElement) {
    var cls = window.event.toElement.className;
    if (cls=='menuItem' || cls=='menu') return;
  }
  document.all("oMenu").style.visibility = "hidden";
  document.all("oMenu").style.top="0px";
  return false;
}
