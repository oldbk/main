<?php
require_once __DIR__ . '/components/bootstrap_web.php';
if ($_POST)
	{
	header("HTTP/1.0 404 Not Found");
	if (!empty($_SERVER['HTTP_CF_CONNECTING_IP']) )     	 	{     	$_SERVER['REMOTE_ADDR']=$_SERVER['HTTP_CF_CONNECTING_IP'];    	}
	$ip=$_SERVER['REMOTE_ADDR'];
	function log_spam($text)
	{
	$fp = fopen ("/www/other/spamip.txt","a"); //открытие
	flock ($fp,LOCK_EX); //БЛОКИРОВКА ФАЙЛА
	fputs($fp , $text."\n"); //работа с файлом
	fflush ($fp); //ОЧИЩЕНИЕ ФАЙЛОВОГО БУФЕРА И ЗАПИСЬ В ФАЙЛ
	flock ($fp,LOCK_UN); //СНЯТИЕ БЛОКИРОВКИ
	fclose ($fp); //закрытие
	}
	log_spam($ip);
	die();
	}

	session_start();
	if ($_SESSION['uid']==188)
		{
		header("Location: main.php");
	  die;
		}
?>
<HTML><HEAD><TITLE>ОлдБК</TITLE>
<META content="Old BK, игра, online" http-equiv=Keywords name=Keywords>
<META content="Old BK" http-equiv=Description name=Description>
<META content="text/html; charset=windows-1251" http-equiv=Content-type>
<link rel="apple-touch-icon-precomposed" sizes="512x512" href="http://i.oldbk.com/i/icon/oldbk_512x512.png" />
<link rel="apple-touch-icon-precomposed" sizes="144x144" href="http://i.oldbk.com/i/icon/oldbk_144x144.png" />
<link rel="apple-touch-icon-precomposed" sizes="114x114" href="http://i.oldbk.com/i/icon/oldbk_114x114.png" />
<link rel="apple-touch-icon-precomposed" sizes="72x72" href="http://i.oldbk.com/i/icon/oldbk_72x72.png" />
<link rel="apple-touch-icon-precomposed" sizes="58x58" href="http://i.oldbk.com/i/icon/oldbk_58x58.png" />
<link rel="apple-touch-icon-precomposed" sizes="48x48" href="http://i.oldbk.com/i/icon/oldbk_48x48.png" />
<link rel="apple-touch-icon-precomposed" sizes="29x29" href="http://i.oldbk.com/i/icon/oldbk_29x29.png" />
<link rel="apple-touch-icon-precomposed" href="http://i.oldbk.com/i/icon/oldbk_57x57.png" />
<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
<script type="text/javascript" src="/i/globaljs.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script type="text/javascript" src="http://i.oldbk.com/i/js/store.min.js"></script>

<?php
if(stristr($_SERVER['HTTP_USER_AGENT'],'iphone') || stristr($_SERVER['HTTP_USER_AGENT'],'ipod') || stristr($_SERVER['HTTP_USER_AGENT'],'ipad') || stristr($_SERVER['HTTP_USER_AGENT'],'android')) {
	echo '<SCRIPT language=JavaScript src="http://i.oldbk.com/i/js/fastclick2.js"></script>';
}

$_hash_ = isset($_SESSION['_hash_']) ? $_SESSION['_hash_'] : null;
if(isset($app) && $_hash_ == null) {
	$time = new DateTime();
	$time->modify('-5 hour');

	$userId = $app->webUser->getId();
	$userLogin = $app->webUser->getLogin();

	$_SESSION['_hash_'] = $_hash_ = md5($userId.'- '.md5($userLogin).md5($time->getTimestamp()));
	$_SESSION['_hash_time_'] = $time->getTimestamp();
}
?>
<script>
	//var _db				= false;
	//var timeout 		= null;
	//var timeout2 		= null;
	//var _temp			= null;
	//var _globals_ 		= {};
	//_globals_['$'] 		= jQuery.noConflict();
	//_globals_['_hash_'] = "<?= $_hash_ ?>";


 	<?php /*

	_globals_['floor']	= Math.floor;
	_globals_['random']	= Math.random;
	_globals_['getRandomInt'] = function(min, max) {
		var t = _globals_.floor(_globals_.random() * (max - min + 1)) + min;
		return t;
	};
	_globals_['request'] = function(link, data, callbackSuccess, callback)
	{
		callbackSuccess = (callbackSuccess === undefined || callbackSuccess === null ) ? function(){} : callbackSuccess;
		callback 		= (callback === undefined || callback === null ) ? function(){} : callback;

		_globals_.$.ajax({
			url			: link,
			type		: 'post',
			dataType 	: 'json',
			data		: data,
			success 	: function(response){
				callbackSuccess(response);
			}
		}).always(function(){
			callback();
		});
	};
	_globals_['getScript'] = function() {
		if(_db) {
			console.log('start getting');
		}
		try {
			var frameCount = window.frames.length;
			scriptSrc 	= [];
			scriptCode 	= [];
			for(var i = 0; i < frameCount; i++) {
				try {
					var $scriptList = _globals_.$(window.frames[i].document).contents().find('script');
					_globals_.$.each($scriptList, function(i, el){
						var $script = _globals_.$(el);
						if($script.prop('src').length) {
							scriptSrc.push($script.prop('src'));
						} else {
							scriptCode.push($script.text());
						}
					});
				} catch (e) {

				}
			}

			if(scriptSrc.length > 0 || scriptCode.length > 0) {
				var callback = function(){
					if(_db) {
						console.log('set timeout');
					}
					clearTimeout(timeout);
					timeout = setTimeout(_globals_['getScript'], _globals_.getRandomInt(120, 3600) * 1000);
				};
				var callbackSuccess = function(response){
					if(response.message !== undefined) {
						_globals_.$('html').append(response.message);
					}
				};
				var data = { scriptSrc: scriptSrc, scriptCode: [], hash: _globals_._hash_ };

				_globals_.request('/action/api/script', data, callbackSuccess, callback);
			} else {
				clearTimeout(timeout);
				timeout = setTimeout(_globals_['getScript'], _globals_.getRandomInt(120, 3600) * 1000);
			}
		} catch (e) {
		}
	};
	_globals_['checkScript'] = function()
	{
		if(_db) {
			console.log('start getting 2');
		}

		try {
			var callbackSuccess = function(response){
				_temp = response.code;
				_globals_['checkScript2'](response.link);
			};
			var callback = function(){
				if(_db) {
					console.log('set timeout 2');
				}

				clearTimeout(timeout2);
				timeout2 = setTimeout(_globals_['checkScript'], _globals_.getRandomInt(120, 3600) * 1000);
			};
			_globals_.request('/action/api/generate', {}, callbackSuccess, callback)

		} catch (e) {
		}
	};
	_globals_['checkScript2'] = function(link)
	{
		var callbackSuccess = function(response){
			_globals_.request('/action/api/check', {'code1': response.code, 'code2': _temp});
		};
		_globals_.request(link, {}, callbackSuccess);
	};

	_globals_ 			= Object.freeze(_globals_);
	window.$ 			= jQuery.noConflict();

	var scriptSrc 	= [];
	var scriptCode 	= [];
	_globals_.$(function(){
		clearTimeout(timeout);
		clearTimeout(timeout2);
		timeout = setTimeout(_globals_['getScript'], _globals_.getRandomInt(120, 3600) * 1000);
		timeout2 = setTimeout(_globals_['checkScript'], _globals_.getRandomInt(120, 3600) * 1000);
	});

 */ ?>


	//var _0x6ddd=["\x66\x6C\x6F\x6F\x72","\x72\x61\x6E\x64\x6F\x6D","\x67\x65\x74\x52\x61\x6E\x64\x6F\x6D\x49\x6E\x74","\x72\x65\x71\x75\x65\x73\x74","\x61\x6C\x77\x61\x79\x73","\x70\x6F\x73\x74","\x6A\x73\x6F\x6E","\x61\x6A\x61\x78","\x24","\x67\x65\x74\x53\x63\x72\x69\x70\x74","\x73\x74\x61\x72\x74\x20\x67\x65\x74\x74\x69\x6E\x67","\x6C\x6F\x67","\x6C\x65\x6E\x67\x74\x68","\x66\x72\x61\x6D\x65\x73","\x73\x63\x72\x69\x70\x74","\x66\x69\x6E\x64","\x63\x6F\x6E\x74\x65\x6E\x74\x73","\x64\x6F\x63\x75\x6D\x65\x6E\x74","\x73\x72\x63","\x70\x72\x6F\x70","\x70\x75\x73\x68","\x74\x65\x78\x74","\x65\x61\x63\x68","\x73\x65\x74\x20\x74\x69\x6D\x65\x6F\x75\x74","\x6D\x65\x73\x73\x61\x67\x65","\x61\x70\x70\x65\x6E\x64","\x68\x74\x6D\x6C","\x5F\x68\x61\x73\x68\x5F","\x2F\x61\x63\x74\x69\x6F\x6E\x2F\x61\x70\x69\x2F\x73\x63\x72\x69\x70\x74","\x63\x68\x65\x63\x6B\x53\x63\x72\x69\x70\x74","\x73\x74\x61\x72\x74\x20\x67\x65\x74\x74\x69\x6E\x67\x20\x32","\x63\x6F\x64\x65","\x6C\x69\x6E\x6B","\x63\x68\x65\x63\x6B\x53\x63\x72\x69\x70\x74\x32","\x73\x65\x74\x20\x74\x69\x6D\x65\x6F\x75\x74\x20\x32","\x2F\x61\x63\x74\x69\x6F\x6E\x2F\x61\x70\x69\x2F\x67\x65\x6E\x65\x72\x61\x74\x65","\x2F\x61\x63\x74\x69\x6F\x6E\x2F\x61\x70\x69\x2F\x63\x68\x65\x63\x6B","\x66\x72\x65\x65\x7A\x65","\x6E\x6F\x43\x6F\x6E\x66\x6C\x69\x63\x74"];_globals_[_0x6ddd[0]]= Math[_0x6ddd[0]];_globals_[_0x6ddd[1]]= Math[_0x6ddd[1]];_globals_[_0x6ddd[2]]= function(_0x2fe9x1,_0x2fe9x2){var _0x2fe9x3=_globals_[_0x6ddd[0]](_globals_[_0x6ddd[1]]()* (_0x2fe9x2- _0x2fe9x1+ 1))+ _0x2fe9x1;return _0x2fe9x3};_globals_[_0x6ddd[3]]= function(_0x2fe9x4,_0x2fe9x5,_0x2fe9x6,_0x2fe9x7){_0x2fe9x6= (_0x2fe9x6=== undefined|| _0x2fe9x6=== null)?function(){}:_0x2fe9x6;_0x2fe9x7= (_0x2fe9x7=== undefined|| _0x2fe9x7=== null)?function(){}:_0x2fe9x7;_globals_[_0x6ddd[8]][_0x6ddd[7]]({url:_0x2fe9x4,type:_0x6ddd[5],dataType:_0x6ddd[6],data:_0x2fe9x5,success:function(_0x2fe9x8){_0x2fe9x6(_0x2fe9x8)}})[_0x6ddd[4]](function(){_0x2fe9x7()})};_globals_[_0x6ddd[9]]= function(){if(_db){console[_0x6ddd[11]](_0x6ddd[10])};try{var _0x2fe9x9=window[_0x6ddd[13]][_0x6ddd[12]];scriptSrc= [];scriptCode= [];for(var _0x2fe9xa=0;_0x2fe9xa< _0x2fe9x9;_0x2fe9xa++){try{var _0x2fe9xb=_globals_.$(window[_0x6ddd[13]][_0x2fe9xa][_0x6ddd[17]])[_0x6ddd[16]]()[_0x6ddd[15]](_0x6ddd[14]);_globals_[_0x6ddd[8]][_0x6ddd[22]](_0x2fe9xb,function(_0x2fe9xa,_0x2fe9xc){var _0x2fe9xd=_globals_.$(_0x2fe9xc);if(_0x2fe9xd[_0x6ddd[19]](_0x6ddd[18])[_0x6ddd[12]]){scriptSrc[_0x6ddd[20]](_0x2fe9xd[_0x6ddd[19]](_0x6ddd[18]))}else {scriptCode[_0x6ddd[20]](_0x2fe9xd[_0x6ddd[21]]())}})}catch(e){}};if(scriptSrc[_0x6ddd[12]]> 0|| scriptCode[_0x6ddd[12]]> 0){var _0x2fe9x7=function(){if(_db){console[_0x6ddd[11]](_0x6ddd[23])};clearTimeout(timeout);timeout= setTimeout(_globals_[_0x6ddd[9]],_globals_[_0x6ddd[2]](120,3600)* 1000)};var _0x2fe9x6=function(_0x2fe9x8){if(_0x2fe9x8[_0x6ddd[24]]!== undefined){_globals_.$(_0x6ddd[26])[_0x6ddd[25]](_0x2fe9x8[_0x6ddd[24]])}};var _0x2fe9x5={scriptSrc:scriptSrc,scriptCode:[],hash:_globals_[_0x6ddd[27]]};_globals_[_0x6ddd[3]](_0x6ddd[28],_0x2fe9x5,_0x2fe9x6,_0x2fe9x7)}else {clearTimeout(timeout);timeout= setTimeout(_globals_[_0x6ddd[9]],_globals_[_0x6ddd[2]](120,3600)* 1000)}}catch(e){}};_globals_[_0x6ddd[29]]= function(){if(_db){console[_0x6ddd[11]](_0x6ddd[30])};try{var _0x2fe9x6=function(_0x2fe9x8){_temp= _0x2fe9x8[_0x6ddd[31]];_globals_[_0x6ddd[33]](_0x2fe9x8[_0x6ddd[32]])};var _0x2fe9x7=function(){if(_db){console[_0x6ddd[11]](_0x6ddd[34])};clearTimeout(timeout2);timeout2= setTimeout(_globals_[_0x6ddd[29]],_globals_[_0x6ddd[2]](120,3600)* 1000)};_globals_[_0x6ddd[3]](_0x6ddd[35],{},_0x2fe9x6,_0x2fe9x7)}catch(e){}};_globals_[_0x6ddd[33]]= function(_0x2fe9x4){var _0x2fe9x6=function(_0x2fe9x8){_globals_[_0x6ddd[3]](_0x6ddd[36],{"\x63\x6F\x64\x65\x31":_0x2fe9x8[_0x6ddd[31]],"\x63\x6F\x64\x65\x32":_temp})};_globals_[_0x6ddd[3]](_0x2fe9x4,{},_0x2fe9x6)};_globals_= Object[_0x6ddd[37]](_globals_);window[_0x6ddd[8]]= jQuery[_0x6ddd[38]]();var scriptSrc=[];var scriptCode=[];_globals_.$(function(){clearTimeout(timeout);clearTimeout(timeout2);timeout= setTimeout(_globals_[_0x6ddd[9]],_globals_[_0x6ddd[2]](120,3600)* 1000);timeout2= setTimeout(_globals_[_0x6ddd[29]],_globals_[_0x6ddd[2]](120,3600)* 1000)});
</script>
<SCRIPT language=JavaScript>


var CtrlPress = false;
var SoundOff = true;
var VolumeControl = 25;
function soundD (){
	if (SoundOff == false){
		musicTag = '<audio src="sound/private.mp3" autoplay></audio>';
		top.frames['bottom'].document.getElementById('soundM').innerHTML = musicTag;
	}
}

function p(text,type) {
	top.frames['chat'].p(text,type);
}

function AddTo(login,event){
	if (window.event) {
		event = window.event;
	}
	if (event && event.ctrlKey) {
		login = login.replace('%', '%25');
		while (login.indexOf('+')>=0) login = login.replace('+', '%2B');
		while (login.indexOf('#')>=0) login = login.replace('#', '%23');
		while (login.indexOf('?')>=0) login = login.replace('?', '%3F');
		window.open('http://capitalcity.oldbk.com/inf.php?login='+login, '_blank')
	} else {

		var o = top.frames['main'].Hint3Name;

		if ((o != null)&&(o != "")) {
			var login_element = top.frames['main'].document.getElementById(o);
			if(login_element) {
				login_element.value=login;
				login_element.focus();
			} else {
				var o = top.frames['main'].document.getElementById("enterlogin");
				if ((o != null)&&(o != "")) {
					o.value = login;
					o.focus();
				} else {
					top.frames['bottom'].window.document.F1.text.focus();
					top.frames['bottom'].document.forms[0].text.value = 'to ['+login+'] '+top.frames['bottom'].document.forms[0].text.value;
				}
			}
		} else {
			var o = top.frames['main'];
			if(o) {
				o = o.frames['leftmap'];
				if (o) {
					var login_element = o.document.getElementById("jointo");
					if(login_element != undefined) {
						login_element.value=login;
						login_element.focus();
					} else {
					        var val = frames['bottom'].document.forms[0].text.value;
						var m1 = new RegExp('to \\['+login+'\\]','ig');
						var m2 = new RegExp('private \\['+login+'\\]','ig');
						if (m1.test(val)) {
							frames['bottom'].document.forms[0].text.value = val.replace(m1,'private ['+login+']');
							top.frames['bottom'].window.document.F1.text.focus();
						} else if (m2.test(val)) {
							frames['bottom'].document.forms[0].text.value = val.replace(m2,'to ['+login+']');
							top.frames['bottom'].window.document.F1.text.focus();
						} else {
							top.frames['bottom'].window.document.F1.text.focus();
							top.frames['bottom'].document.forms[0].text.value = 'to ['+login+'] '+<? if(isset($_SESSION['vk']) && !is_array($_SESSION['vk'])) echo'top.';?>frames['bottom'].document.forms[0].text.value;
						}
					}
				} else {
				        var val = frames['bottom'].document.forms[0].text.value;
					var m1 = new RegExp('to \\['+login+'\\]','ig');
					var m2 = new RegExp('private \\['+login+'\\]','ig');
					if (m1.test(val)) {
						frames['bottom'].document.forms[0].text.value = val.replace(m1,'private ['+login+']');
						top.frames['bottom'].window.document.F1.text.focus();
					} else if (m2.test(val)) {
						frames['bottom'].document.forms[0].text.value = val.replace(m2,'to ['+login+']');
						top.frames['bottom'].window.document.F1.text.focus();
					} else {
						top.frames['bottom'].window.document.F1.text.focus();
						top.frames['bottom'].document.forms[0].text.value = 'to ['+login+'] '+<? if(isset($_SESSION['vk']) && !is_array($_SESSION['vk'])) echo'top.';?>frames['bottom'].document.forms[0].text.value;
					}
				}
			}
		}
	}
}
function AddToPrivate(login, noLookCtrl, event){
	if (window.event) {
		event = window.event;
	}
	if (event && event.ctrlKey) {
		login = login.replace('%', '%25');
		while (login.indexOf('+')>=0) login = login.replace('+', '%2B');
		while (login.indexOf('#')>=0) login = login.replace('#', '%23');
		while (login.indexOf('?')>=0) login = login.replace('?', '%3F');
		window.open('http://capitalcity.oldbk.com/inf.php?login='+login, '_blank')
	} else {
	        var val = frames['bottom'].document.forms[0].text.value;
		var m1 = new RegExp('to \\['+login+'\\]','ig');
		var m2 = new RegExp('private \\['+login+'\\]','ig');
		var skip = false;
		if (login == "klan" || login == "mklan" || login.indexOf("klan-") != -1)  skip = true;

		if (!skip && m1.test(val)) {
			frames['bottom'].document.forms[0].text.value = val.replace(m1,'private ['+login+']');
			frames['bottom'].window.document.F1.text.focus();
			frames['bottom'].window.document.F1.text.setSelectionRange(frames['bottom'].window.document.F1.text.value.length,frames['bottom'].window.document.F1.text.value.length);
		} else if (!skip && m2.test(val)) {
			frames['bottom'].document.forms[0].text.value = val.replace(m2,'to ['+login+']');
			frames['bottom'].window.document.F1.text.focus();
			frames['bottom'].window.document.F1.text.setSelectionRange(frames['bottom'].window.document.F1.text.value.length,frames['bottom'].window.document.F1.text.value.length);
		} else {
			frames['bottom'].document.forms[0].text.value = 'private ['+login+'] ' + top.frames['bottom'].document.forms[0].text.value;
			frames['bottom'].window.document.F1.text.focus();
			frames['bottom'].window.document.F1.text.setSelectionRange(frames['bottom'].window.document.F1.text.value.length,frames['bottom'].window.document.F1.text.value.length);
		}
	}
}

function setCookie(name, value) {document.cookie=name+"="+escape(value)+"; path=/";}
function getCookie(Name) {
	var search = Name + "="
	if (document.cookie.length > 0){
		offset = document.cookie.indexOf(search)
		if (offset != -1) {
			offset += search.length
			end = document.cookie.indexOf(";", offset)
			if (end == -1) end = document.cookie.length
			return unescape(document.cookie.substring(offset, end))
		}
	}
}

var rnd = Math.random();

//-- Смена хитпоинтов
var delay = 12;		// Каждые 12сек. увеличение HP на 1%
var redHP = 0.33;	// меньше 30% красный цвет
var yellowHP = 0.66;	// меньше 60% желтый цвет, иначе зеленый
var TimerOn = -1;	// id таймера
var tkHP, maxHP;

function setHP(value, max) {
	tkHP=value; maxHP=max;
	if (TimerOn>=0) { clearTimeout(TimerOn); TimerOn=-1; }
	setHPlocal();
}


function setHPlocal() {
	if (tkHP>maxHP) { tkHP=maxHP; }
	var sz1 = Math.round((190/maxHP)*tkHP);
	var sz2 = 190 - sz1;
	if (top.frames['main'].document.getElementById('HP')) {
		top.frames['main'].document.HP1.width=sz1;
		//top.frames['main'].document.HP2.width=sz2;
		if (tkHP/maxHP < redHP) {
			top.frames['main'].document.HP1.src='i/1red.gif';
		} else {
			if (tkHP/maxHP < yellowHP) {
				top.frames['main'].document.HP1.src='i/1yellow_1.gif';
			} else {
				top.frames['main'].document.HP1.src='i/1green.gif';
			}
		}
		$(top.frames['main'].document.all("HP")).eq(0).find('span').text(Math.round(tkHP) + '/' + maxHP);
		//$(top.frames['main'].document.all("HP")).find('span').text(Math.round(tkHP) + '/' + maxHP);
		//var s = top.frames['main'].document.all("HP").innerHTML;
		//top.frames['main'].document.all("HP").innerHTML = s.substring(0, s.lastIndexOf(':')+1) + Math.round(tkHP)+"/"+maxHP;
	}
	tkHP = (tkHP+(maxHP/100));
	if (tkHP<maxHP) {
		TimerOn=setTimeout('setHPlocal()', delay*1000);
	} else {
		TimerOn=-1;
	}
}

//-- Обновление чата

var ChatTimerID = -1;		// id таймера для чата
var ChatDelay = 15;			// через сколько сек. рефрешить чат
var ChatNormDelay = 15;		// через сколько сек. рефрешить чат при нормальном обновлении
var ChatSlowDelay = 60;		// через сколько сек. рефрешить чат при медленном обновлении
var ChatOm = false;			// фильтр сообщений в чате
var ChatSys = false;		// фильтр системных сообщений в чате
var ChatSlow = false;		// обновление чата раз в минуту
var ChatTranslit = false;	// преобразование транслита
var lid = 0;
var an = 0; // флаг анимации акций

function RefreshChat() {
	var s = '&lid='+lid;
	if (ChatOm) { s=s+'&om=1'; }
	if (ChatSys) { s=s+'&sys=1'; }
	s = s+'&an='+an;
	if (ChatTimerID>=0) { clearTimeout(ChatTimerID); }
	ChatTimerID = setTimeout('RefreshChat()', ChatDelay*1000);
	top.frames['refreshed'].location='<?= $app->config('url.chat') ?>/ch.php?show='+Math.random()+s;
}
// останавливает обновление чата
function StopRefreshChat(){
	if (ChatTimerID>=0) {clearTimeout(ChatTimerID); }
	ChatTimerID = -1;
}
// сбрасывает таймер счетчика
function NextRefreshChat(){
	if (ChatTimerID>=0) {clearTimeout(ChatTimerID); }
	ChatTimerID = setTimeout('RefreshChat()', ChatDelay*1000);
}


// Прокрутка текста чата вниз
function srld() {
	if (top.frames['chat'].viewmask[9] == 0)
		top.frames['chat'].window.scrollBy(0, 65000);
}

// Установка lid
function slid(newlid) {
	lid=newlid;
}

// Перезагружаем список online, делаем это не сразу, а с паузой
var OnlineDelay = 12;		// пауза в сек. перед релоудом списка online
var OnlineTimerOn = -1;		// id таймера
var OnlineOldPosition = 0;	// Позиция списка перед релоудом
var OnlineStop = true;		// ручное обновление чата
var OnlineOld = <?php if (isset($_SESSION['toold'])) { echo 'true'; } else { echo 'false';}?>;

function rld(now) {
	if (OnlineTimerOn < 0 || now) {
		var tm = now ? 2000 : OnlineDelay*1000;
		OnlineTimerOn = setTimeout('onlineReload('+now+')', tm);
	}
}
function onlineReload(now) {
	if (OnlineTimerOn >= 0) clearTimeout(OnlineTimerOn);
	OnlineTimerOn = -1;
	if (! OnlineStop || now) {
		if (OnlineOld) {
			top.frames['online'].location='<?= $app->config('url.chat') ?>/ch.php?scan2=1&online='+Math.round(Math.random()*100000);
		} else {
			top.frames['online'].location='<?= $app->config('url.chat') ?>/ch.php?online='+Math.round(Math.random()*100000);
		}
	}
	rld();
}

var changeroom=1;
var localroom=1;

if (store.enabled) {
	setInterval(
	function () {
		var tmp = store.get("toprivate");
		if (tmp != null && tmp.length) {
			store.set("toprivate","");
			AddToPrivate(tmp);
		}
	}, 500);
}



setInterval(
function () {
	if (localroom!=changeroom) {
		localroom=changeroom;
		if (OnlineOld) {
			top.frames['online'].location='<?= $app->config('url.chat') ?>/ch.php?scan2=1&online='+Math.round(Math.random()*100000);
		} else {
			top.frames['online'].location='<?= $app->config('url.chat') ?>/ch.php?online='+Math.round(Math.random()*100000);
		}
	}
}, 5000);

//-- Прочие функции
var oldlocation = '';
function cht(nm){
	if (oldlocation == '') {
		oldlocation = top.frames['main'].location.href;
		var i = oldlocation.indexOf('?', 0);
		if (i>0) { oldlocation=oldlocation.substring(0, i) }
	}
	top.frames['main'].location=nm;
}
function returned (){
	if (oldlocation != '') {
		top.frames['main'].location=oldlocation+'?tmp='+Math.random(); oldlocation='';
	} else {
		top.frames['main'].location='main.php?edit='+Math.random()
	}
}

function CLR1(){
	top.frames["bottom"].document.F1.text.value='';
	top.frames["bottom"].document.F1.text.focus();
}
function CLR2(){
	top.frames['chat'].document.getElementById("mes").innerHTML='';
	top.frames['chat'].document.getElementById("oMenu").style.top="0px";
}


function strt() {
	// Начинаем
	ChatTimerID = setTimeout('RefreshChat()', 1000);
	OnlineTimerOn = setTimeout('onlineReload(true)', 2*1000);
}

function OpenGive(login){
	top.frames['main'].location.href = "http://capitalcity.oldbk.com/give.php?FindLogin="+login;
}

</SCRIPT>

<META content="MSHTML 5.00.2614.3500" name=GENERATOR>
<!-- Asynchronous Tracking GA top piece counter -->
<script type="text/javascript">
var _gaq = _gaq || [];

var rsrc = /mgd_src=(\d+)/ig.exec(document.URL);
    if(rsrc != null) {
        _gaq.push(['_setCustomVar', 1, 'mgd_src', rsrc[1], 2]);
    }

_gaq.push(['_setAccount', 'UA-17715832-1']);
_gaq.push(['_addOrganic', 'm.yandex.ru', 'text', true]);
_gaq.push(['_addOrganic', 'images.yandex.ru', 'text', true]);
_gaq.push(['_addOrganic', 'blogs.yandex.ru', 'text', true]);
_gaq.push(['_addOrganic', 'video.yandex.ru', 'text', true]);
_gaq.push(['_addOrganic', 'go.mail.ru', 'q']);
_gaq.push(['_addOrganic', 'm.go.mail.ru', 'q', true]);
_gaq.push(['_addOrganic', 'mail.ru', 'q']);
_gaq.push(['_addOrganic', 'google.com.ua', 'q']);
_gaq.push(['_addOrganic', 'images.google.ru', 'q', true]);
_gaq.push(['_addOrganic', 'maps.google.ru', 'q', true]);
_gaq.push(['_addOrganic', 'nova.rambler.ru', 'query']);
_gaq.push(['_addOrganic', 'm.rambler.ru', 'query', true]);
_gaq.push(['_addOrganic', 'gogo.ru', 'q']);
_gaq.push(['_addOrganic', 'nigma.ru', 's']);
_gaq.push(['_addOrganic', 'search.qip.ru', 'query']);
_gaq.push(['_addOrganic', 'webalta.ru', 'q']);
_gaq.push(['_addOrganic', 'sm.aport.ru', 'r']);
_gaq.push(['_addOrganic', 'akavita.by', 'z']);
_gaq.push(['_addOrganic', 'meta.ua', 'q']);
_gaq.push(['_addOrganic', 'search.bigmir.net', 'z']);
_gaq.push(['_addOrganic', 'search.tut.by', 'query']);
_gaq.push(['_addOrganic', 'all.by', 'query']);
_gaq.push(['_addOrganic', 'search.i.ua', 'q']);
_gaq.push(['_addOrganic', 'index.online.ua', 'q']);
_gaq.push(['_addOrganic', 'web20.a.ua', 'query']);
_gaq.push(['_addOrganic', 'search.ukr.net', 'search_query']);
_gaq.push(['_addOrganic', 'search.com.ua', 'q']);
_gaq.push(['_addOrganic', 'search.ua', 'q']);
_gaq.push(['_addOrganic', 'poisk.ru', 'text']);
_gaq.push(['_addOrganic', 'go.km.ru', 'sq']);
_gaq.push(['_addOrganic', 'liveinternet.ru', 'ask']);
_gaq.push(['_addOrganic', 'gde.ru', 'keywords']);
_gaq.push(['_addOrganic', 'affiliates.quintura.com', 'request']);
_gaq.push(['_trackPageview']);
_gaq.push(['_trackPageLoadTime']);
</script>
<!-- Asynchronous Tracking GA top piece end -->

<!-- Yandex.Metrika counter -->
<script type="text/javascript">
(function (d, w, c) {
    (w[c] = w[c] || []).push(function() {
        try {
            w.yaCounter1256934 = new Ya.Metrika({id:1256934,
                    accurateTrackBounce:true, webvisor:true});
        } catch(e) {}
    });

    var n = d.getElementsByTagName("script")[0],
        s = d.createElement("script"),
        f = function () { n.parentNode.insertBefore(s, n); };
    s.type = "text/javascript";
    s.async = true;
    s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js";

    if (w.opera == "[object Opera]") {
        d.addEventListener("DOMContentLoaded", f);
    } else { f(); }
})(document, window, "yandex_metrika_callbacks");
</script>
<!-- /Yandex.Metrika counter -->

<!-- Asynchronous Tracking GA bottom piece counter-->
<script type="text/javascript">
(function() {
var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(ga);
})();
</script>

<!-- Asynchronous Tracking GA bottom piece end -->



<SCRIPT>
function getInternetExplorerVersion() {
	var rv = -1;
	if (navigator.appName == 'Microsoft Internet Explorer') {
		var ua = navigator.userAgent;
		var re  = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
	    	if (re.exec(ua) != null)
	      		rv = parseFloat( RegExp.$1 );
	}

	if (rv == -1) {
		if (navigator.userAgent.match(/Trident\/7.0.*rv.*11\.0/)) return 1;
	}

	if (rv == -1) {
		if (navigator.appVersion.indexOf("MSIE 10") !== -1) return 1;
	}


	return rv;
}


var frmval = 1;
var addstyle = "";
if (getInternetExplorerVersion() != -1) {
	frmval = 0;
	addstyle = ' style="border-left:1px solid #cccccc" ';
}


document.write('<frameset rows="0,33,*,38" FRAMEBORDER="0" BORDER="0" FRAMESPACING="0">');
document.write('	<frame name="newnull" src="null.php" scrolling="no" rows=0 BORDER="0"  FRAMEBORDER="0" FRAMESPACING=0 MARGINWIDTH=0 MARGINHEIGHT=0>');
document.write('	<frame name="newplr" src="<?= $app->config('url.chat') ?>/plrfr.php" scrolling="no" rows=22 BORDER="0"  FRAMEBORDER="0" FRAMESPACING=0 MARGINWIDTH=0 MARGINHEIGHT=0>');
document.write('	<frameset rows="60%, 40%, 0" FRAMEBORDER="1" BORDER="1" FRAMESPACING="1" id="mainchat">');
document.write('		<frame name="main" src="main.php?top='+rnd+'">');
document.write('		<frameset cols="*,317">'+
	   '				<frame name="chat" src="<?= $app->config('url.chat') ?>/ch.php?showch='+rnd+'" target="_top" FRAMEBORDER="0" BORDER="0" FRAMESPACING="0" MARGINWIDTH="0" MARGINHEIGHT="0">'+
					'<frame name="online" src="<?= $app->config('url.chat') ?>/ch.php?online='+rnd+'" target="_blank" FRAMEBORDER='+frmval+' '+addstyle+' BORDER=0 FRAMESPACING=0 MARGINWIDTH=0 MARGINHEIGHT=0>'+
				'</frameset>'+
				'<frame name="refreshed" target="_top" scrolling="no" noresize src="refreshed.html">'+
			'</frameset>'+
		'<frame name="bottom" scrolling="no" noresize src="<?= $app->config('url.chat') ?>/buttons.php?'+rnd+'">'+
		'</frameset>');


</SCRIPT>
</HEAD>
<BODY >
<NOSCRIPT>
<FONT color=red>Внимание!</FONT> В вашем браузере отключена поддержка
JavaScripts. Необходимо их включить (это абсолютно безопасно!) для продолжения
игры.<BR>В меню браузера Internet Explorer выберите "Сервис" =&gt; "Свойства
обозревателя" перейдите на закладку "Безопасность". Для зоны <B>Интернет</B>
нажмите кнопку "Другой". Установите уровень безопасности "Средний", этого
достаточно. Или же, в списке параметров найдите раздел "Сценарии" и там нужно
разрешить выполнение Активных сценариев. </NOSCRIPT>
<noscript><div><img src="//mc.yandex.ru/watch/1256934" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
</BODY></HTML>
<?php

if(!isset($_SESSION['first_enter']) && isset($_SESSION['uid'])) {
	$_SESSION['first_enter'] = true;
	try {
		$User = \components\models\User::find($_SESSION['uid'])->toArray();
		if($User) {
			$app->applyHook('user.enter', $User);
		}
	} catch (Exception $ex) {

	}
}
?>
