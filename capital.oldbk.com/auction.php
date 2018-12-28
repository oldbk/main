<?php
// тип отображения мессаги
$typet = "s";

function get_itemfilt() {
	global $itemsfilter;
	$name = isset($_SESSION['rsf2_iname']) ? $_SESSION['rsf2_iname'] : "";
	$levellow = isset($_SESSION['rsf2_ilevellow']) ? $_SESSION['rsf2_ilevellow'] : "";
	$levelmax = isset($_SESSION['rsf2_ilevellow']) ? $_SESSION['rsf2_ilevelmax'] : "";
	$type = isset($_SESSION['rsf2_itype']) ? $_SESSION['rsf2_itype'] : "";
	$view = isset($_SESSION['rsf2_iview']) ? $_SESSION['rsf2_iview'] : 10;
	$sort = isset($_SESSION['rsf2_isort']) ? $_SESSION['rsf2_isort'] : 0;
	$unik = isset($_SESSION['rsf2_iunik']) ? $_SESSION['rsf2_iunik'] : 0;
	$unik2 = isset($_SESSION['rsf2_iunik2']) ? $_SESSION['rsf2_iunik2'] : 0;
	$charka = isset($_SESSION['rsf2_icharka']) ? $_SESSION['rsf2_icharka'] : 0;

	$opt='';				
	for ($k=0;$k<=14;$k++) {
		$opt.='<option value="'.$k.'"';
		if ($k==$levellow ) $opt.=' selected ';
		$opt.='>'.$k.'</option>';
	}

	$itemsfilter = str_replace("%ILEVELLOW%",$opt,$itemsfilter);
				
	$opt='';				
	for ($k=14;$k>=1;$k--) {
		$opt.='<option value="'.$k.'"';
		if ($k==$levelmax ) $opt.=' selected ';
		$opt.='>'.$k.'</option>';
	}

	$itemsfilter = str_replace("%ILEVELMAX%",$opt,$itemsfilter);					
	$itemsfilter = str_replace("%TYPE%",$type,$itemsfilter);									
					
	$itemsfilter = str_replace("%INAME%",htmlspecialchars($name,ENT_QUOTES),$itemsfilter);
	$itemsfilter = str_replace("%VIEW".$view."%","selected",$itemsfilter);
	$itemsfilter = str_replace("%ISORT".$sort."%","selected",$itemsfilter);

	$itemsfilter = str_replace("%IUNIK%",($unik > 0 ? "checked" : ""),$itemsfilter);
	$itemsfilter = str_replace("%IUNIK2%",($unik2 > 0 ? "checked" : ""),$itemsfilter);
	$itemsfilter = str_replace("%ICHARKA%",($charka > 0 ? "checked" : ""),$itemsfilter);
	
	$arr=array($itemsfilter,$name,$levellow,$levelmax,$type,$view,$sort,$unik,$unik2,$charka);
	return ($arr);
}


function aucpriceitem($item) {
	$ret = array('min' => 0, 'max' => 0);

	// максимум 100 кр для пушек хаоса
	$weap_haos=array(1006232,1006233,1006234,1006241,1006242,199,201,204);
	if (in_array($item['prototype'],$weap_haos)) {
		$ret['min'] = 1;
		$ret['max'] = 100;
		return $ret;
	}

	$max_ups = 5;   // Максимальное кол. апов шмотки - подобная запись есть В РЕМОНТКЕ! Там меня так же!!!

	$prot = mysql_query_cache('select * from oldbk.shop where id = '.$item['prototype'],false,5*60);
	if ($prot === false) die();
	$prote = mysql_query_cache('select * from oldbk.eshop where id = '.$item['prototype'],false,5*60);
	if ($prote === false) die();

	list($k,$prot) = each($prot);
	list($k,$prote) = each($prote);

	if($prot['id'] > 0) {

	} else {
    		$prot['cost'] = $item['cost'];
		$prot['nlevel'] = 0;
	}

	// исключение - плащи, футболки
	if ($item['prototype'] == 7001 || $item['prototype'] == 7002 || $item['prototype'] == 7003 || $item['prototype'] == 100029 || $item['prototype'] == 100028) {
		$ret['min']  = EKR_TO_KR*$item['ecost'];
		if ($prot['unikflag'] > 0) {
			$ret['max'] = EKR_TO_KR*$prot['unikflag'];
		}
		if ($prote['unikflag'] > 0) {
			$ret['max'] = EKR_TO_KR*$prote['unikflag'];
		}
		if ($ret['min'] > $ret['max']) $ret['min'] = $ret['max'];
		return $ret;
	}

	$mf_cost = 0;
	$is_mf = !(strpos($item['name'], '(мф)') === false);

	if($is_mf > 0) {
		$mf_cost = $prot['cost'] * 0.5;

	    	if (($prot['gsila'] == 0) and ($prot['glovk'] == 0) and ($prot['ginta'] == 0) and ($prot['gintel'] == 0) and ($prot['gmp'] == 0)) {
			$mf_cost = round($mf_cost*0.5, 0);
		}

	}

	$real_price['sowner'] = $item['sowner'];
	$real_price['prot_cost'] = $prot['cost'];
	    
	$real_price['mf_cost'] = $mf_cost;

    	$real_price['item_cost'] = $real_price['prot_cost'];

	$cost_add = round($prot['cost'], 0);
	$max_ups_left = $max_ups - $item['ups'];
	$mx_op = array(1=>'5',2=>'4',3=>'3',4=>'2',5=>'1');
	$u_cost = 0;

	if($item['ups']>0 && $real_price['sowner'] == 0) {
		for($cc = $item['ups'];$cc>0;$cc--) {
			$costs[$cc]=upgrade_item($cost_add,$mx_op[$cc]);
			$u_cost += $costs[$cc][up_cost];
		}
	}

	$real_price['u_cost'] = $u_cost;
		
		
	$sharp_pr = 0;
	if($item['type'] == 3) {
		$sharp= explode("+",$item['name']);
		if((int)($sharp[1])>0) {
			$is_sharp=array(1=>20,2=>40,3=>80,4=>160,5=>320, 6 => 640, 7 => 1280, 8 => 2560 , 9 => 5120);
			$sharp_pr=$is_sharp[$sharp[1]];
		}
	}


	$real_price['sharp_pr'] = $sharp_pr;

	$item['includemagicname'] = trim($item['includemagicname']);

	if (!empty($item['includemagicname'])) {
		$real_price['item_cost'] += 250;
		if ($item['includemagiccost'] > 0) $real_price['item_cost'] += $item['includemagiccost']*2;
	}

	//высчитываем от госцены цена + мф + подгоны + заточка
	$real_price['summ'] = $real_price['item_cost']+$real_price['mf_cost']+$real_price['u_cost']+$real_price['sharp_pr'];

	//print_r($real_price);

	if ($item['type'] != 12 && $item['unik'] > 0 && $item['nlevel'] >= 8 && (($prot !== false && $prot['unikflag'] > 0) || ($prote !== false && $prote['unikflag'] > 0))) {
		// для уник вещей >= 8 уровня те что продаются в березе
		if ($prot['unikflag'] > 0) {
			$ret['min']  = $real_price['summ'];
			$ret['max'] = EKR_TO_KR*$prot['unikflag'];
			return $ret;
		}	
		if ($prote['unikflag'] > 0) {
			$ret['min']  = $real_price['summ'];
			$ret['max'] = EKR_TO_KR*$prote['unikflag'];
			return $ret;
		}	
	}

	// все остальные
	$ret['max'] = $real_price['summ']*1.5;
	$ret['min'] = $real_price['summ']*0.6;

	if ($item['type'] == 200) {
		$ret['min'] = $real_price['summ'];
	}

	if ($item['type'] == 12 || ($item['magic'] > 0 && $item['type'] == 50 && $item['ekr_flag'] == 3)) {
		if ($item['ekr_flag'] == 3) {
			$ret['min'] = EKR_TO_KR*$item['ecost'];
			$ret['max'] = 5000;			
			if ($ret['min'] > $ret['max']) $ret['min'] = $ret['max'];
			return $ret;
		}

		$ret['max'] = 5000;
		if ($ret['prototype'] == 190191 || $ret['prototype'] == 190192) {
			if ($prot) $ret['max'] = EKR_TO_KR*$prot['ecost'];
			if ($prote) $ret['max'] = EKR_TO_KR*$prote['ecost'];
		}
		return $ret;
	}

	return $ret;	
}


$head = <<<HEADHEAD
<!DOCTYPE html>
<html lang="ru">
<head>
	<meta content="text/html; charset=windows-1251" http-equiv=Content-type>
	<META Http-Equiv=Cache-Control Content=no-cache>
	<meta http-equiv=PRAGMA content=NO-CACHE>
	<META Http-Equiv=Expires Content=0>
    <title>Old BK - Аукцион</title>
	<link rel="StyleSheet" href="newstyle_loc4.css" type="text/css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	<script type="text/javascript" src="i/js/noty/packaged/jquery.noty.packaged.min.js"></script>
	<script type="text/javascript" src="i/js/noty/packaged/custom.js"></script>
	<script type="text/javascript" src="/i/globaljs.js"></script>
	<script type='text/javascript' src='http://i.oldbk.com/i/js/recoverscroll.js'></script>
	<!-- Asynchronous Tracking GA top piece counter -->
<style>
SELECT {
	BORDER-RIGHT: #b0b0b0 1pt solid; BORDER-TOP: #b0b0b0 1pt solid; MARGIN-TOP: 1px; FONT-SIZE: 10px; MARGIN-BOTTOM: 2px; BORDER-LEFT: #b0b0b0 1pt solid; COLOR: #191970; BORDER-BOTTOM: #b0b0b0 1pt solid; FONT-FAMILY: MS Sans Serif
}
TEXTAREA {
	BORDER-RIGHT: #b0b0b0 1pt solid; BORDER-TOP: #b0b0b0 1pt solid; MARGIN-TOP: 1px; FONT-SIZE: 10px; MARGIN-BOTTOM: 2px; BORDER-LEFT: #b0b0b0 1pt solid; COLOR: #191970; BORDER-BOTTOM: #b0b0b0 1pt solid; FONT-FAMILY: MS Sans Serif
}
INPUT {
	BORDER-RIGHT: #b0b0b0 1pt solid; BORDER-TOP: #b0b0b0 1pt solid; MARGIN-TOP: 1px; FONT-SIZE: 10px; MARGIN-BOTTOM: 2px; BORDER-LEFT: #b0b0b0 1pt solid; COLOR: #191970; BORDER-BOTTOM: #b0b0b0 1pt solid; FONT-FAMILY: MS Sans Serif
}

.noty_message { padding: 5px !important;}
</style>
<script type="text/javascript">

function mkfilt(id) {
	document.getElementById('itype').value=id;
	document.getElementById('apply').value='Yes';
	document.filter.submit();		
}


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
<link rel="stylesheet" type="text/css" href="http://i.oldbk.com/i/jscal/css/jscal2.css" />
<link rel="stylesheet" type="text/css" href="http://i.oldbk.com/i/jscal/css/border-radius.css" />
<link rel="stylesheet" type="text/css" href="http://i.oldbk.com/i/jscal/css/steel/steel.css" />
<script type="text/javascript" src="http://i.oldbk.com/i/jscal/js/jscal2.js"></script>
<script type="text/javascript" src="http://i.oldbk.com/i/jscal/js/lang/ru2.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.1/jquery.min.js"></script>
<script src="i/jquery.drag.js" type="text/javascript"></script>	 
<script>
	
function showhide(id) {
	if (document.getElementById(id).style.display=="none") {
		document.getElementById(id).style.display="block";
	} else {
		document.getElementById(id).style.display="none";
	}
}

function getformdata(id,param,event,page) {
	if (window.event) {
		event = window.event;
	}
	if (event) {
		$.get('auction.php?view='+param+'&page='+page+'&id='+id+'', function(data) {
			$('#pl').html(data);
			$('#pl').show(200);
			$("input, select, button").bind('mousedown.ui-disableSelection selectstart.ui-disableSelection', function(e){
			    e.stopImmediatePropagation();
			});

		});

		$('#pl').css({ position:'absolute',left: ($(window).width()-$('#pl').outerWidth())/2, top: ($(window).scrollTop()+120)+'px'  });

	}
}
			
function closeinfo() {
	$('#pl').hide(200);
}
			
$(window).resize(function() {
	$('#pl').css({ position:'absolute',left: ($(window).width()-$('#pl').outerWidth())/2, top: ($(window).scrollTop()+120)+'px'  });
});			
	
\$(document).ready(function(){\$('#pl').drag();});

var bankauth = %BANKID%;
var hlitem = "";
var hlclick = "ismarked";

function doauth() {
	var bankid = $('#bankid').val();
	var bankpass = $('#bankpass').val();

	$.get('auction.php?view=bankauth&bankid='+bankid+'&bankpass='+bankpass, function(data) {
		$('#pl').show(200);
		$('#pl').html(data);
		$("input, select, button").bind('mousedown.ui-disableSelection selectstart.ui-disableSelection', function(e){
		    e.stopImmediatePropagation();
		});
	});
					
	return true;
}

function hl() {
	if (hlitem.length > 0) {
		if ($("#"+hlitem).children(":eq(1)").children(":eq(1)").find("td:first").css("background-color") == "transparent") {
			$("#"+hlitem).children(":eq(1)").children(":eq(1)").find("td:first").css("background-color", "#FAF0E6");
		} else {
			$("#"+hlitem).children(":eq(1)").children(":eq(1)").find("td:first").css("background-color", "transparent");
		}
	}
}

function checkbank() {
	if (bankauth) {
		hl();
		return true;
	} else {
		$.get('auction.php?view=bankauth', function(data) {
			$('#pl').html(data);
		 	$('#pl').css({ position:'absolute',left: ($(window).width()-$('#pl').outerWidth())/2, top: ($(window).scrollTop()+120)+'px'  });
			$('#pl').show(200);

			$("input, select, button").bind('mousedown.ui-disableSelection selectstart.ui-disableSelection', function(e){
			    e.stopImmediatePropagation();
			});
		});
					
		return false;
	}
}

function checkputlot() {
	if (!isNaN(minprice) && !isNaN(maxprice)) {
		var blicprice = $("#blic").val();
		if (!isNaN(blicprice) && blicprice > 0) {
			if (blicprice < minprice || blicprice > maxprice) {
				$("#minmax").show();
				return false;
			}
		}
	}
	document.putlot.submit();
}

</script>	
</head>
<body id="arenda-body">
<script type='text/javascript'>
RecoverScroll.start();
</script>
<div id="page-wrapper">
	<div id="pl" style="z-index: 300; position: absolute; left: 155px; top: 120px;
				%WINSIZE% background-color: #eeeeee; cursor: move;
				border: 1px solid black; display: none;">
	
			</div>
			%MESSAGE%
    <div class="title">
        <div class="h3">
            Аукцион
        </div>
        <div id="buttons">
            <a class="button-dark-mid btn" href="javascript:void(0);" title="Подсказка" onclick="window.open('help/auction.html', 'help', 'height=300,width=500,location=no,menubar=no,status=no,toolbar=no,scrollbars=yes');">Подсказка</a>            
            <a class="button-mid btn" href="javascript:void(0);" title="Обновить" onclick="location.href='auction.php?refresh='+Math.random();" >Обновить</a>                    
            <a class="button-mid btn" href="javascript:void(0);" title="Вернуться" onclick="location.href='auction.php?exit=1';">Вернуться</a>
        </div>
    </div>
    <div id="aukcion">
        <table cellspacing="0" cellpadding="0">
            <colgroup>
                <col>
                <col width="300px">
            </colgroup>
            <tbody>
            <tr>
                <td>
                    <table class="table" cellspacing="0" cellpadding="0">
                        <thead>
                        <tr class="head-line">
                            <th>
                                <div class="head-left"></div>
                                <div class="head-title p">
                                    %auction%
                                </div>
                                <div class="head-separate"></div>
                            </th>
                            <th>
                                <div class="head-title p">
                                     %myrates%
                                </div>
                                <div class="head-separate"></div>
                            </th>
                            <th>
                                <div class="head-title p">
                                    	%mylots%
                                </div>
                                <div class="head-separate"></div>
                            </th>
                            <th>
                                <div class="head-title p">
                                    %newlot%
                                </div>
                                <div class="head-right"></div>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="title">
                            <td colspan="4" class="center">
                            %TITLE%
                            </td>
                        </tr>
                        <tr class="odd">
                            <td colspan="4" class="center">
                            %PAGES%
                            </td>
                        </tr>
                        </tbody>
                    </table>
			%CONTENT%
                </td>
                <td><form method="POST" name="filter" action="?view=auction" OnSubmit="if (document.getElementById('apply').value != 'Yes' && document.getElementById('reset').value != 'Yes') document.getElementById('apply').value = 'Yes'; return true;">
                    <table id="filter" cellspacing="0" cellpadding="0">
                        <tbody>
                        <tr>
                            <td align="left">
                                У Вас в наличии: <span class="money"><strong>%MONEY%</strong></span><strong> кр.</strong><br>
				%KAZNA%
				%BANKINFO%
                                Репутации покупки: <span class="money"><strong>%REPA%</strong></span><strong> реп.</strong>
                            </td>
                        </tr>
                        <tr>

                        </tr>
                        <tr>
                            <td></td>
                        </tr>
                        %OTDEL%
                        <tr>
                            <td style="text-align: right;">
                                <img src="http://i.oldbk.com/i/images/arenda/arenda_illustration.jpg">
                            </td>
                        </tr>
                        </tbody>
                    </table>
		</form>
                </td>
            </tr>
	    <tr><td align="center">
		<table class="table">
		    <tr class="odd"><td style="text-align:center;">
			%PAGES%
		    </td></tr>
	            </tbody>
		</table>
	    </td></tr>
        </table>
    </div>
HEADHEAD;

$itemsfilter = <<<ITEMSFILTER
	                <form method="POST" name="filter" action="?view=auction" OnSubmit="if (document.getElementById('apply').value != 'Yes' && document.getElementById('reset').value != 'Yes') document.getElementById('apply').value = 'Yes'; return true;">
                        <tr>
                            <td class="hint-block center">
                                Воспользуйтесь фильтрами для поиска нужных Вам вещей.
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input placeholder="Название предмета..." style="width: 205px;" name="iname" value="%INAME%"> 
                                <a href="javascript:void(0);" class="button-mid btn" title="Искать" onClick="document.getElementById('apply').value='Yes';document.filter.submit();" >Искать</a>
                                <a href="javascript:void(0);" class="button-mid btn" title="Сбросить" onClick="document.getElementById('reset').value='Yes';document.filter.submit();" >Сбросить</a><br>
				<input type="checkbox" %IUNIK2% name="iunik2"> улучшенный уникальный предмет<br>
				<input type="checkbox" %IUNIK% name="iunik"> уникальный предмет<br>
				<input type="checkbox" %ICHARKA% name="icharka"> имеется чарование<br>

                                <input type="hidden" name="apply" id="apply" value="">
				<input type="hidden" name="reset" id="reset" value="">
				<input type="hidden" name="itype" id="itype" value="%TYPE%">
                            </td>
                        </tr>
                        <tr>
                        <td>Сортировать по: <select name="isort">
			<option %ISORT0% value="0">По цене ставки</option>
			<option %ISORT1% value="1">По цене вещи</option>
			<option %ISORT2% value="2">По дате окончания</option>
			<option %ISORT3% value="3">По названию</option>
			</select>
			</td>
                        </tr>
                        <tr>
				<td>Уровень от 
                                <select name="ilevellow" >
				%ILEVELLOW%
                                </select> до
                                <select name="ilevelmax">
				%ILEVELMAX%
                                </select>
                            </td>
                        </tr>
                        <tr>
	                        <td>Выводить по: <select name="iview"><option %VIEW10% value="10">10</option><option %VIEW20% value="20">20</option><option %VIEW50% value="50">50</option></select></td>
			</tr>
			</form>
ITEMSFILTER;

$otdels =<<<OTDELS
			%FILTER%
			<tr>
                            <td class="filter-title">Оружие</td>
                        </tr>
                        <tr>
                            <td class="filter-item">
                                <ul>
                                    <li>
                                        <a href="javascript:void(0);" onClick="mkfilt(1);">Кастеты и ножи</a>
                                    </li>
                                    <li>
					<a href="javascript:void(0);" onClick="mkfilt(11);">Топоры</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" onClick="mkfilt(12);">Дубины и булавы</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" onClick="mkfilt(13);">Мечи</a>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                        <tr>
                            <td class="filter-title">Одежда и броня</td>
                        </tr>
                        <tr>
                            <td class="filter-item">
                                <ul>
                                    <li>
                                        <a href="javascript:void(0);" onClick="mkfilt(2);">Сапоги</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" onClick="mkfilt(21);">Перчатки</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" onClick="mkfilt(22);">Легкая броня</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" onClick="mkfilt(23);">Тяжелая броня</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" onClick="mkfilt(24);">Шлемы</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" onClick="mkfilt(3);">Щиты</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" onClick="mkfilt(6);">Амуниция</a>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                        <tr>
                            <td class="filter-title">Ювелирные товары</td>
                        </tr>
                        <tr>
                            <td class="filter-item">
                                <ul>
                                    <li>
                                        <a href="javascript:void(0);" onClick="mkfilt(4);">Серьги</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" onClick="mkfilt(41);">Ожерелья</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" onClick="mkfilt(42);">Кольца</a>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                        <tr>
                            <td class="filter-title">Заклинания и свитки</td>
                        </tr>
                        <tr>
                            <td class="filter-item">
                                <ul>
                                    <li>
                                        <a href="javascript:void(0);" onClick="mkfilt(5);">Нейтральные заклинания</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" onClick="mkfilt(51);">Боевые и защитные</a>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                        <tr>
                            <td class="filter-title">Прочее</td>
                        </tr>
                        <tr>
                            <td class="filter-item">
                                <ul>
                                    <li>
                                        <a href="javascript:void(0);" onClick="mkfilt(61);">Еда</a>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                        <tr>
                            <td class="filter-title">Сувениры</td>
                        </tr>
                        <tr>
                            <td class="filter-item">
                                <ul>
                                    <li>
                                        <a href="javascript:void(0);" onClick="mkfilt(7);">Открытки</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" onClick="mkfilt(73);">Подарки</a>
                                    </li>

                                    <li>
                                        <a href="javascript:void(0);" onClick="mkfilt(71);">Другие подарки</a>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="filter-item">
                                <ul>
                                    <li>
                                        <a href="javascript:void(0);" onClick="mkfilt(52);">Прочее</a>
                                    </li>
                                </ul>
                            </td>
                        </tr>
OTDELS;

$mailrucounter = <<<MAILRUCOUNTER
	<div align=right>
	<script type="text/javascript">
		document.write("<a href='http://www.liveinternet.ru/click' "+
		"target=_blank><img src='http://counter.yadro.ru/hit?t54.2;r"+
		escape(document.referrer)+((typeof(screen)=="undefined")?"":
		";s"+screen.width+"*"+screen.height+"*"+(screen.colorDepth?
		screen.colorDepth:screen.pixelDepth))+";u"+escape(document.URL)+
		";"+Math.random()+
		"' alt='' title='LiveInternet: показано число просмотров и"+
		" посетителей за 24 часа' "+
		"border='0' ><\/a>");
		</script>
	
	<!--Rating@Mail.ru counter-->
	<script language="javascript" type="text/javascript"><!--
	d=document;var a='';a+=';r='+escape(d.referrer);js=10;//--></script>
	<script language="javascript1.1" type="text/javascript"><!--
	a+=';j='+navigator.javaEnabled();js=11;//--></script>
	<script language="javascript1.2" type="text/javascript"><!--
	s=screen;a+=';s='+s.width+'*'+s.height;
	a+=';d='+(s.colorDepth?s.colorDepth:s.pixelDepth);js=12;//--></script>
	<script language="javascript1.3" type="text/javascript"><!--
	js=13;//--></script><script language="javascript" type="text/javascript"><!--
	d.write('<a href="http://top.mail.ru/jump?from=1765367" target="_top">'+
	'<img src="http://df.ce.ba.a1.top.mail.ru/counter?id=1765367;t=49;js='+js+
	a+';rand='+Math.random()+'" alt="Рейтинг@Mail.ru" border="0" '+
	'height="31" width="88"><\/a>');if(11<js)d.write('<'+'!-- ');//--></script>
	<noscript><a target="_top" href="http://top.mail.ru/jump?from=1765367">
	<img src="http://df.ce.ba.a1.top.mail.ru/counter?js=na;id=1765367;t=49"
	height="31" width="88" border="0" alt="Рейтинг@Mail.ru"></a></noscript>
	<script language="javascript" type="text/javascript"><!--
	if(11<js)d.write('--'+'>');//--></script>
	<!--// Rating@Mail.ru counter-->
	</div>
MAILRUCOUNTER;

$foot = <<<FOOTFOOT
	<br><br>
	%MAILRU%
	<br>
	<!-- Asynchronous Tracking GA bottom piece counter-->
<script type="text/javascript">


(function() {
var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(ga);
})();
</script>

<!-- Asynchronous Tracking GA bottom piece end -->


</div>
</body>
</html>
FOOTFOOT;

$newlot = <<<NEWLOT
	<tr bgcolor=#A5A5A5><td colspan=2>
		<form method="POST" name="putlot">
			%MINMAX%
			Начальная цена: <input size=4 type="text" name="start"> кр.<br>
			Блиц-цена (моментальная покупка лота): <input id="blic" size=4 type="text" name="blic"> <br>
			Длительность торгов: <input size=4 type="text" name="days"> дней (от 1 до 30)<br>
			<b>Выделить лот за 1 екр</b> <input autocomplete="off" type="checkbox" OnClick="hlitem = 'newlot'; return checkbank();" id="ismarked" name="ismarked"><br>
			Налог на выставление лота: <b>%AUCTAX%</b> кр.<br>
			Налог после продажи: <b>%AUCTAXSELL%</b>%<br><br>
			%ARSENAL%
                        <a href="javascript:void(0);" class="button-big btn" title="Выставить лот" onClick="return checkputlot();">Выставить лот</a>
		</form>
	</td></tr>
NEWLOT;

	function Redirect($path) {
		header("Location: ".$path);
		die();
	}

	function RoundTime($time) {
		// обрезаем время до секунд
		$seconds = date("s",$time);
		return ($time - $seconds);
	}

	session_start();

	if (!isset($_SESSION["uid"]) || $_SESSION["uid"] == 0) Redirect("index.php");


	include('connect.php');
	include('functions.php');
	include('bank_functions.php');	
	include "clan_kazna.php";


	if ($user[klan]!='')  {
		$clan_id=mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`clans` WHERE `short` = '{$user[klan]}' LIMIT 1;"));
		if ($clan_id[id] >0) {
			if ($clan_id[glava]==$user[id]) {
				$clan_kazna=clan_kazna_have($clan_id[id]);
			}
		}
	 }
	
	$auctionid = 445;
	$aucnewlottax = 1; // налог на выставления на аукцион
	$auctaxsell = 0.1; // налог после продажи - 10%

	if (isset($_GET['exit'])) {
		if ($user['room']==71) {
			mysql_query('UPDATE `users` SET `users`.`room` = "66" WHERE `users`.`id` = '.$_SESSION['uid']) or die();
			Redirect('city.php');
		} else {
			Redirect('main.php');
		}
	}

	$view = isset($_GET['view']) ? $_GET['view'] : "auction";

	// проверка на уровень
	if ($user['level'] < 4 || $user['align'] == 4) $view = "auction";

	$center = "";
	switch($view) {
		default:
			$view = "auction";
		case "bankauth":
			$error = 0;
			if (isset($_GET['bankpass'],$_GET['bankid'])) {
				$q = mysql_query('SELECT * FROM bank WHERE owner = '.$user['id'].' and id = '.intval($_GET['bankid']).' and pass = "'.md5($_GET['bankpass']).'"');	
				if (mysql_num_rows($q) > 0) {
					$_SESSION['bankid'] = intval($_GET['bankid']);					
					echo '<script>closeinfo();bankauth = true;$("#"+hlclick).click();</script>';
					die();
				} else {
					$error = 1;
				}
			}                                  

			$auth =  '<table border=0 width=400 height=100><tr><td  valign=top align="center" height=5 colspan="4"><font style="COLOR:#8f0000;FONT-SIZE:12pt">'; 
			$auth .= "Авторизация в банке";
			$auth .= '</font><a onClick="closeinfo();" title="Закрыть" style="cursor: pointer;" >
			<img src="http://i.oldbk.com/i/bank/bclose.png" style="position:relative;top:-20px;right:-220px;" border=0 title="Закрыть"></a></td></tr>
			<tr><td colspan="4" class="center" valign=top>';   
			$q = mysql_query('SELECT * FROM bank WHERE owner = '.$user['id']);
			if (mysql_num_rows($q) > 0) {
				$auth .= '<select id="bankid" style="width:100px" name="bankid">';
				while ($rah = mysql_fetch_array($q)) {
					$auth .= "<option>".$rah['id']."</option>";
				}
				$auth .= "</select> ";
				$auth .= 'Пароль: <input type=password name="bankpass" id="bankpass" style="width:100px"> <button style="height:23px;" OnClick="doauth();">Войти</button>';

			} else {
				$auth .= '<font color="red">Банковские счета не найдены</font>';
			}

			if ($error > 0) {
				$auth .= '<br><font color="red">Не правильный пароль</font>';				
			}

			$auth .= '</td>
				</tr><tr><td align="center"  colspan="3">
				</td></tr>
				</table>';
			echo $auth;
			die();
		break;
		case "auction":
			$otels[1]="Оружие: кастеты,ножи";
			$otels[11]="Оружие: топоры";
			$otels[12]="Оружие: дубины,булавы";
			$otels[13]="Оружие: мечи";
			$otels[2]="Одежда: сапоги";
			$otels[21]="Одежда: перчатки";
			$otels[22]="Одежда: легкая броня";
			$otels[23]="Одежда: тяжелая броня";
			$otels[24]="Одежда: шлемы";
			$otels[3]="Щиты";
			$otels[4]="Ювелирные товары: серьги";
			$otels[41]="Ювелирные товары: ожерелья";
			$otels[42]="Ювелирные товары: кольца";
			$otels[5]="Заклинания: нейтральные";
			$otels[51]="Заклинания: боевые и защитные";
			$otels[52]="Прочее";
			$otels[6]="Амуниция";
			$otels[61]="Еда";
			$otels[7]="Сувениры: открытки";
			$otels[71]="Сувениры: другие подарки";
			$otels[73]="Сувениры: подарки";
			$otels[72]="Уникальные подарки";
			
			$cont = '<center><table border=0 width="100%"><tr valign=top><td>';
			if ($user['level'] < 4) {
				$akk_err='Вход в аукцион только с 4го уровня.';
				$typet = "e";
				break;
			}
			if ($user['align'] == 4) {
				$akk_err='Вход со склонностью хаоса запрещен.';
				$typet = "e";
				break;
			}

			if ($_SERVER['REQUEST_METHOD'] == "POST") {
				if (isset($_GET['id']) && isset($_POST['up'],$_POST['lastrate'])) {
					$id = intval($_GET['id']);
					$up = round(floatval($_POST['up']),2);
					if ($up < 1) {
						Redirect('auction.php?view=auction&id='.$id.'&error=2');
					}

					// поднимаем ставку

					mysql_query('START TRANSACTION') or die();

					//ставку личную
					//выбираем, проверяем чтобы вещь была не моя и что торги по ней не закончились
					
					$q = mysql_query('SELECT * FROM oldbk.`auction` WHERE owner <> '.$user['id'].' AND endtime > '.time().' AND `repcost`=0  AND `id` = '.$id.' FOR UPDATE') or die();

					if (mysql_num_rows($q) > 0) {
						$item = mysql_fetch_assoc($q) or die();


						if (!($item['blic'] == 0 || ($item['blic'] > 0 && ($item['rate'] + $item['shag'] < $item['blic'])))) {
							Redirect('auction.php?view=auction&id='.$id.'&error=6');
						}

						// проверяем шаг ставки
						if  ($up <  $item['shag']) {
							mysql_query('COMMIT') or die();
							Redirect('auction.php?view=auction&id='.$id.'&error=2&lo='.$item['shag']);
						}

						// проверяем цену
						if ($item['rate'] != $_POST['lastrate']) {
							mysql_query('COMMIT') or die();
							Redirect('auction.php?view=auction&id='.$id.'&error=1');
						}
						
						//проверка на шибко умных для покупки в арсенал
						if ($_POST['ars']!='') {
							$test_item=mysql_fetch_assoc(mysql_query("SELECT * FROM oldbk.inventory WHERE id ='{$item['itemid']}' and  type in (1,2,3,4,5,6,7,8,9,10,11,28) ")) or die();
							if (!($test_item['id']>0)) {
						  		err('Ошибка предмета');
								die();
							}
						} 

						if (($_POST['ars']!='') and ($clan_kazna) and ($item['clan_id']==$clan_id['id'])) {
							//если поднимаем свою ставку(клан-казны) и ставка была сделана из казны клана в арсенал
							$coment='(поднял ставку в аукционе)';
						  	if (by_from_kazna($clan_id[id],1,$up,$coment)) {
						      		// обвновляем лот
								mysql_query('UPDATE oldbk.`auction` SET st_count=st_count+1, st_date=NOW() ,  rate = rate + '.$up.' WHERE id = '.$id) or die();
						      	} else {
						      		//ошибка
								mysql_query('COMMIT') or die();
								Redirect('auction.php?view=auction&id='.$id.'&error=3');
						      	}
						} else if (($_POST[ars]!='') and ($clan_kazna)) {
						 	//поднимаем ставку чужую/или пустую которая была поставлена из чужой клан казны/или личная
						 	//ставку делаем из своей клан казны
						 	$coment='(ставка в аукционе)';
						  	if (by_from_kazna($clan_id[id],1,($item['rate'] + $up),$coment)) {

								$lastowner = false;

								if ($item['newowner']) $lastowner=check_users_city_data($item['newowner']);
								$itemd = mysql_fetch_assoc(mysql_query('SELECT * FROM oldbk.inventory WHERE id = '.$item['itemid'])) or die();

						      		if ($item['clan_id']>0) {
						        		//ставка была из чужой клан казны
					        			// возвращаем бабки предыдущему ставщику-клан казне
					        			$delo_txt='Возврат ставки с аукциона '.$item['rate'].' кр.';
					        			sell_to_kazna($item[clan_id],$item['rate'],0,$delo_txt);
								} else if ($item['newowner'] > 0) {

									$rec = array();
				  		    			$rec['owner']=$lastowner[id];
									$rec['owner_login']=$lastowner[login];
									$rec['owner_balans_do']=$lastowner['money'];
									$rec['owner_balans_posle']=$lastowner['money']+($item['rate']);
									$rec['target']=$auctionid;
									$rec['target_login']="аукцион";
									$rec['type']=230; // получил возврат с аукциона
									$rec['sum_kr']=$item['rate'];
									$rec['sum_ekr']=0;
									$rec['sum_kom']=0;
									$rec['item_id']=get_item_fid($itemd);
									$rec['item_name']=$itemd['name'];
									$rec['item_count']=1;
									$rec['item_type']=$itemd['type'];
									$rec['item_cost']=$itemd['cost'];
									$rec['item_dur']=$itemd['duration'];
									$rec['item_maxdur']=$itemd['maxdur'];
									$rec['item_ups']=$itemd['ups'];
									$rec['item_unic']=$itemd['unik'];
									$rec['item_incmagic_id']=$itemd['includemagic'];
				                    			$rec['item_ecost']=$itemd['ecost'];
									$rec['item_proto']=$itemd['prototype'];
                                    					$rec['item_sowner']=($itemd['sowner']>0?1:0);
									$rec['item_incmagic']=$itemd['includemagicname'];
									$rec['item_incmagic_count']=$itemd['includemagicuses'];
									$rec['item_arsenal']='';
									$rec['item_mfinfo']=$itemd['mfinfo'];
									$rec['item_level']=$itemd['nlevel'];
									$rec['add_info'] = '';
									add_to_new_delo($rec); //юзеру

									// возвращаем бабки предыдущему ставщику
									if ($lastowner[id_city]==1) {
										mysql_query('UPDATE avalon.`users` set money = money + '.($item['rate']).' WHERE id = '.$item['newowner']) or die();
									} elseif ($lastowner[id_city]==2) {
										mysql_query('UPDATE angels.`users` set money = money + '.($item['rate']).' WHERE id = '.$item['newowner']) or die();
									} else {
										mysql_query('UPDATE oldbk.`users` set money = money + '.($item['rate']).' WHERE id = '.$item['newowner']) or die();
									}
								}

								// системку о перебитии ставки
								if ($lastowner !== false) {
									// отправляем системку хозяину вещи
									telepost($lastowner['login'],'<font color=red>Внимание!</font> Ваша ставка на лот «'.$itemd['name'].'» была перебита.');
								}

						      		// обвновляем лот - ставим ид клана
								mysql_query('UPDATE oldbk.`auction` SET st_count=st_count+1, st_date=NOW(),  rate = rate + '.$up.', newowner = '.$user['id'].', clan_id='.$clan_id[id].' WHERE id = '.$id) or die();
						      } else {
								//ошибка
								mysql_query('COMMIT') or die();
								Redirect('auction.php?view=auction&id='.$id.'&error=3');
						      }
						 } else if (($item['newowner'] == $user['id']) and ($item['clan_id']==0) ) {
							// если поднимаем собственную ставку ЛИЧНУЮ, то нам необходимо смотреть просто разницу
							if ($user['money'] < $up) {
								mysql_query('COMMIT') or die();
								Redirect('auction.php?view=auction&id='.$id.'&error=3');
							}
							// снимаем деньги
							mysql_query('UPDATE `users` SET money = money - '.$up.' WHERE id = '.$user['id']) or die();
							// обвновляем лот
							mysql_query('UPDATE oldbk.`auction` SET st_count=st_count+1, st_date=NOW(),  rate = rate + '.$up.' WHERE id = '.$id) or die();

							$itemd = mysql_fetch_assoc(mysql_query('SELECT * FROM oldbk.inventory WHERE id = '.$item['itemid'])) or die();

							$rec = array();
		  		    			$rec['owner']=$user['id'];
							$rec['owner_login']=$user['login'];
							$rec['owner_balans_do']=$user['money'];
							$rec['owner_balans_posle']=$user['money']-$up;
							$rec['target']=$auctionid;
							$rec['target_login']="аукцион";
							$rec['type']=232; // поднял свою же ставку
							$rec['sum_kr']=$up;
							$rec['sum_ekr']=0;
							$rec['sum_kom']=0;
							$rec['item_id']=get_item_fid($itemd);
							$rec['item_name']=$itemd['name'];
							$rec['item_count']=1;
							$rec['item_type']=$itemd['type'];
							$rec['item_cost']=$itemd['cost'];
							$rec['item_dur']=$itemd['duration'];
							$rec['item_maxdur']=$itemd['maxdur'];
							$rec['item_ups']=$itemd['ups'];
							$rec['item_unic']=$itemd['unik'];
							$rec['item_incmagic_id']=$itemd['includemagic'];
							$rec['item_ecost']=$itemd['ecost'];
							$rec['item_proto']=$itemd['prototype'];
							$rec['item_sowner']=($itemd['sowner']>0?1:0);
							$rec['item_incmagic']=$itemd['includemagicname'];
							$rec['item_incmagic_count']=$itemd['includemagicuses'];
							$rec['item_arsenal']='';
							$rec['item_mfinfo']=$itemd['mfinfo'];
							$rec['item_level']=$itemd['nlevel'];
							$rec['add_info'] = '';
							add_to_new_delo($rec); //юзеру

						} else {
							// поднимаем чужую ставку, проверяем бабки
							if ($user['money'] < ($item['rate']+$up)) {
								mysql_query('COMMIT') or die();
								Redirect('auction.php?view=auction&id='.$id.'&error=3');
							}

							// снимаем деньги
							mysql_query('UPDATE `users` set money = money - '.($item['rate'] + $up).' WHERE id = '.$user['id']) or die();

							$itemd = mysql_fetch_assoc(mysql_query('SELECT * FROM oldbk.inventory WHERE id = '.$item['itemid'])) or die();

							$rec = array();
		  		    			$rec['owner']=$user[id];
							$rec['owner_login']=$user[login];
							$rec['owner_balans_do']=$user['money'];
							$rec['owner_balans_posle']=$user['money']-($item['rate'] + $up);
							$rec['target']=$auctionid;
							$rec['target_login']="аукцион";
							$rec['type']=231; // сделал ставку
							$rec['sum_kr']=($item['rate'] + $up);
							$rec['sum_ekr']=0;
							$rec['sum_kom']=0;
							$rec['item_id']=get_item_fid($itemd);
							$rec['item_name']=$itemd['name'];
							$rec['item_count']=1;
							$rec['item_type']=$itemd['type'];
							$rec['item_cost']=$itemd['cost'];
							$rec['item_dur']=$itemd['duration'];
							$rec['item_maxdur']=$itemd['maxdur'];
							$rec['item_ups']=$itemd['ups'];
							$rec['item_unic']=$itemd['unik'];
							$rec['item_incmagic_id']=$itemd['includemagic'];
		                    			$rec['item_ecost']=$itemd['ecost'];
							$rec['item_proto']=$itemd['prototype'];
                            				$rec['item_sowner']=($itemd['sowner']>0?1:0);
							$rec['item_incmagic']=$itemd['includemagicname'];
							$rec['item_incmagic_count']=$itemd['includemagicuses'];
							$rec['item_arsenal']='';
							$rec['item_mfinfo']=$itemd['mfinfo'];
							$rec['item_level']=$itemd['nlevel'];
							$rec['add_info'] = '';
							add_to_new_delo($rec); //юзеру

							$lastowner = false;
							if ($item['newowner']) $lastowner = check_users_city_data($item['newowner']);
							$itemd = mysql_fetch_assoc(mysql_query('SELECT * FROM oldbk.inventory WHERE id = '.$item['itemid'])) or die();

							if ($item['clan_id']>0) {
							        //ставка была из чужой клан казны
						        	// возвращаем бабки предыдущему ставщику-клан казне
						        	$delo_txt='Возврат ставки с аукциона '.$item['rate'].'кр.';
						        	sell_to_kazna($item[clan_id],$item['rate'],0,$delo_txt);
						        } elseif ($item['newowner'] > 0) {
								$rec = array();
			  		    			$rec['owner']=$lastowner[id];
								$rec['owner_login']=$lastowner[login];
								$rec['owner_balans_do']=$lastowner['money'];
								$rec['owner_balans_posle']=$lastowner['money']+($item['rate']);
								$rec['target']=$auctionid;
								$rec['target_login']="аукцион";
								$rec['type']=230; // получил возврат с аукциона
								$rec['sum_kr']=$item['rate'];
								$rec['sum_ekr']=0;
								$rec['sum_kom']=0;
								$rec['item_id']=get_item_fid($itemd);
								$rec['item_name']=$itemd['name'];
								$rec['item_count']=1;
								$rec['item_type']=$itemd['type'];
								$rec['item_cost']=$itemd['cost'];
								$rec['item_dur']=$itemd['duration'];
								$rec['item_maxdur']=$itemd['maxdur'];
								$rec['item_ups']=$itemd['ups'];
								$rec['item_unic']=$itemd['unik'];
								$rec['item_incmagic_id']=$itemd['includemagic'];
			                    			$rec['item_ecost']=$itemd['ecost'];
								$rec['item_proto']=$itemd['prototype'];
                                				$rec['item_sowner']=($itemd['sowner']>0?1:0);
								$rec['item_incmagic']=$itemd['includemagicname'];
								$rec['item_incmagic_count']=$itemd['includemagicuses'];
								$rec['item_arsenal']='';
								$rec['item_mfinfo']=$itemd['mfinfo'];
								$rec['item_level']=$itemd['nlevel'];
								$rec['add_info'] = '';
								add_to_new_delo($rec); //юзеру

								// возвращаем бабки предыдущему ставщику
								if ($lastowner[id_city]==1) {
									mysql_query('UPDATE avalon.`users` set money = money + '.($item['rate']).' WHERE id = '.$item['newowner']) or die();
								} elseif ($lastowner[id_city]==2) {
									mysql_query('UPDATE angels.`users` set money = money + '.($item['rate']).' WHERE id = '.$item['newowner']) or die();
								} else {
									mysql_query('UPDATE oldbk.`users` set money = money + '.($item['rate']).' WHERE id = '.$item['newowner']) or die();
								}
							}

							// системку о перебитии ставки
							if ($lastowner !== false) {
								// отправляем системку хозяину вещи
								telepost($lastowner['login'],'<font color=red>Внимание!</font> Ваша ставка на лот «'.$itemd['name'].'» была перебита.');
							}


							// обвновляем лот - затираем ид_клана если было
							mysql_query('UPDATE oldbk.`auction` SET st_count=st_count+1, st_date=NOW(), rate = rate + '.$up.', newowner = '.$user['id'].' , clan_id=0  WHERE id = '.$id) or die();
						}


						mysql_query('COMMIT') or die();
						Redirect('auction.php?view=auction&otdel='.$_GET['otdel'].'&error=0&page='.intval($_GET['page']));
					}

					mysql_query('COMMIT') or die();
					Redirect('auction.php?view=auction&id='.$id);
				}


				if (($_POST['apply']!='') && isset($_POST['iname'],$_POST['itype'],$_POST['ilevellow'],$_POST['ilevelmax'],$_POST['iview'],$_POST['isort'])) {
					$_SESSION['rsf2_iname'] = stripslashes($_POST['iname']);
					$_SESSION['rsf2_ilevellow'] = $_POST['ilevellow'] === "" ? "": intval($_POST['ilevellow']);
					$_SESSION['rsf2_ilevelmax'] = $_POST['ilevelmax'] === "" ? "": intval($_POST['ilevelmax']);
					$_SESSION['rsf2_itype'] = $_POST['itype'] === "" ? "" : intval($_POST['itype']);
					$_SESSION['rsf2_iview'] = intval($_POST['iview']);
					$_SESSION['rsf2_isort'] = intval($_POST['isort']);
					$_SESSION['rsf2_iunik'] = (isset($_POST['iunik']) ? 1 : 0);
					$_SESSION['rsf2_iunik2'] = (isset($_POST['iunik2']) ? 1 : 0);
					$_SESSION['rsf2_icharka'] = (isset($_POST['icharka']) ? 1 : 0);
				} elseif ($_POST['reset']!='') {
					$_SESSION['rsf2_iname'] = "";
					$_SESSION['rsf2_ilevellow'] = "";
					$_SESSION['rsf2_ilevelmax'] = "";
					$_SESSION['rsf2_iview'] = 10;
					$_SESSION['rsf2_itype'] = "";
					$_SESSION['rsf2_isort'] = 0;
					$_SESSION['rsf2_iunik'] = 0;
					$_SESSION['rsf2_iunik2'] = 0;
					$_SESSION['rsf2_icharka'] = 0;
				}
				Redirect("auction.php?view=auction");

			} elseif (isset($_GET['blicid'])) {
				$id = intval($_GET['blicid']);
				if (give_count($user['id'],1))	{
					mysql_query('START TRANSACTION') or die();

					//выбираем, проверяем чтобы вещь была не моя и что торги по ней не закончились
					$q = mysql_query('SELECT * FROM oldbk.`auction` WHERE owner <> '.$user['id'].' AND endtime > '.time().' AND `repcost`=0  AND `id` = '.$id.' and blic > 0 FOR UPDATE') or die();
	
					if (mysql_num_rows($q) > 0) {
						$item = mysql_fetch_assoc($q) or die();
	
						if ($user['money'] >= $item['blic']) {
	
							// возвращаем все деньги по прошлой ставке если она была
					      		if ($item['clan_id'] > 0) {
					        		// ставка была через казну, возвращаем
				        			$delo_txt='Возврат ставки с аукциона '.$item['rate'].' кр.';
				        			sell_to_kazna($item[clan_id],$item['rate'],0,$delo_txt);
							} else if ($item['newowner'] > 0) {
								// ставка была игрока - возвращаем
								$lastowner=check_users_city_data($item['newowner']);
											
								$itemd = mysql_fetch_assoc(mysql_query('SELECT * FROM oldbk.inventory WHERE id = '.$item['itemid'])) or die();
								$rec = array();
		
			  		    			$rec['owner']=$lastowner[id];
								$rec['owner_login']=$lastowner[login];
								$rec['owner_balans_do']=$lastowner['money'];
								$rec['owner_balans_posle']=$lastowner['money']+($item['rate']);
								$rec['target']=$auctionid;
								$rec['target_login']="аукцион";
								$rec['type']=230; // получил возврат с аукциона
								$rec['sum_kr']=$item['rate'];
								$rec['sum_ekr']=0;
								$rec['sum_kom']=0;
								$rec['item_id']=get_item_fid($itemd);
								$rec['item_name']=$itemd['name'];
								$rec['item_count']=1;
								$rec['item_type']=$itemd['type'];
								$rec['item_cost']=$itemd['cost'];
								$rec['item_dur']=$itemd['duration'];
								$rec['item_maxdur']=$itemd['maxdur'];
								$rec['item_ups']=$itemd['ups'];
								$rec['item_unic']=$itemd['unik'];
								$rec['item_incmagic_id']=$itemd['includemagic'];
			                    			$rec['item_ecost']=$itemd['ecost'];
								$rec['item_proto']=$itemd['prototype'];
								$rec['item_sowner']=($itemd['sowner']>0?1:0);
								$rec['item_incmagic']=$itemd['includemagicname'];
								$rec['item_incmagic_count']=$itemd['includemagicuses'];
								$rec['item_arsenal']='';
								$rec['item_mfinfo']=$itemd['mfinfo'];
								$rec['item_level']=$itemd['nlevel'];
								$rec['add_info'] = '';
								add_to_new_delo($rec); //юзеру
		
								// возвращаем бабки предыдущему ставщику
								if ($lastowner['id_city']==1) {
									mysql_query('UPDATE avalon.`users` set money = money + '.($item['rate']).' WHERE id = '.$item['newowner']) or die();
								} elseif ($lastowner['id_city']==2) {
									mysql_query('UPDATE angels.`users` set money = money + '.($item['rate']).' WHERE id = '.$item['newowner']) or die();
								} else {
									mysql_query('UPDATE oldbk.`users` set money = money + '.($item['rate']).' WHERE id = '.$item['newowner']) or die();
								}
							}
	
							// теперь собственно блиц-покупка
	
							// снимаем деньги
							mysql_query('UPDATE `users` set money = money - '.($item['blic']).' WHERE id = '.$user['id']) or die();
	
							$itemd = mysql_fetch_assoc(mysql_query('SELECT * FROM oldbk.inventory WHERE id = '.$item['itemid'])) or die();
	
							$rec = array();
		  		    			$rec['owner']=$user['id'];
							$rec['owner_login']=$user['login'];
							$rec['owner_balans_do']=$user['money'];
							$rec['owner_balans_posle']=$user['money']-$item['blic'];
							$rec['target']=$auctionid;
							$rec['target_login']="аукцион";
							$rec['type']=231; // сделал ставку
							$rec['sum_kr']=($item['blic']);
							$rec['sum_ekr']=0;
							$rec['sum_kom']=0;
							$rec['item_id']=get_item_fid($itemd);
							$rec['item_name']=$itemd['name'];
							$rec['item_count']=1;
							$rec['item_type']=$itemd['type'];
							$rec['item_cost']=$itemd['cost'];
							$rec['item_dur']=$itemd['duration'];
							$rec['item_maxdur']=$itemd['maxdur'];
							$rec['item_ups']=$itemd['ups'];
							$rec['item_unic']=$itemd['unik'];
							$rec['item_incmagic_id']=$itemd['includemagic'];
		                    			$rec['item_ecost']=$itemd['ecost'];
							$rec['item_proto']=$itemd['prototype'];
							$rec['item_sowner']=($itemd['sowner']>0?1:0);
							$rec['item_incmagic']=$itemd['includemagicname'];
							$rec['item_incmagic_count']=$itemd['includemagicuses'];
							$rec['item_mfinfo']=$itemd['mfinfo'];
							$rec['item_level']=$itemd['nlevel'];
							$rec['item_arsenal']='';
							$rec['add_info'] = 'По блиц-цене';
							add_to_new_delo($rec); //юзеру
	
							// обвновляем лот
							mysql_query('UPDATE oldbk.`auction` SET rate = "'.$item['blic'].'", newowner = '.$user['id'].', endtime = '.(time()-1).', clan_id = 0 WHERE id = '.$id) or die();
							$_SESSION['auclastname'] = $itemd['name'];
							mysql_query('COMMIT') or die();
							Redirect('auction.php?view=auction&otdel='.$_GET['otdel'].'&error=5');
						} else {
							Redirect('auction.php?view=auction&otdel='.$_GET['otdel'].'&error=4');
						}
					}
				} else {
					Redirect('auction.php?view=auction&otdel='.$_GET['otdel'].'&error=7');
				}

			}


			$goid = "";
			$addsql = "";
			$limit = "";
			if (isset($_GET['id'])) {
				$id = intval($_GET['id']);
				$goid = 'AND auc.id = '.$id;
			} else {
				$ar = get_itemfilt();
				$tpl=$ar[0];
				$name=$ar[1];
				$levellow=$ar[2];
				$levelmax=$ar[3];
				$type=$ar[4];
				$view=$ar[5];
				$sort=$ar[6];
				$unik=$ar[7];
				$unik2=$ar[8];
				$charka=$ar[9];

				$otel = $otels[$type];
				if ($otel == '') {
					$otel=$otels[1];
				}

				//$head = str_replace("%TITLE%",'<strong>'.$otel.'</strong>',$head);
				$head = str_replace("%TITLE%",'',$head);

				
				if ($type !== "") {
					$addsql .= ' AND otdel = '.$type;
				}

				if ($levellow !== "") {
					$addsql .= ' AND nlevel >= '.$levellow;
				}
				if ($levelmax !== "") {
					$addsql .= ' AND nlevel <= '.$levelmax;
				}
				if ($name !== "") {
					$addsql .= ' AND name LIKE "%'.addcslashes(mysql_real_escape_string(str_replace('\\','\\\\',$name)),"%_").'%" ';
				}
				if ($unik > 0) $addsql .= ' AND unik = 1';
				if ($unik2 > 0) $addsql .= ' AND unik = 2';
				if ($charka > 0) $addsql .= ' AND LENGTH(charka) > 1';


				if (isset($_GET['page'])) {
					$page = intval($_GET['page']);
					$limit .= ' LIMIT '.($page*$view).','.$view.' ';
				} else {
					$page = 0;
					$limit .= ' LIMIT '.$view.' ';
				}
			}

			$sortsql = "";
			switch($sort) {
				default: case 0:
					$sortsql = "auc.rate";
				break;
				case 1:
					$sortsql = "inv.cost";
				break;
				case 2:
					$sortsql = "auc.endtime";
				break;
				case 3:
					$sortsql = "inv.name";
				break;
			}


			// выбираем все лоты, у которых я не владелец и не за репу!
			$q = mysql_query('
					SELECT SQL_CALC_FOUND_ROWS auc.id AS aucid, auc.itemid AS aucitemid, auc.owner AS aucowner, auc.endtime AS aucendtime, auc.newowner AS aucnewowner, auc.rate AS aucrate, auc.clan_id, auc.shag, auc.st_count, auc.st_date , auc.starttime as aucstarttime, auc.blic as aucblic,auc.ismarked as aucismarked, inv.* FROM oldbk.`auction` AS `auc`
					LEFT JOIN oldbk.`inventory` AS inv
					ON auc.itemid = inv.id
					WHERE inv.owner = "'.$auctionid.'" '.$goid.' AND auc.repcost=0 AND auc.owner <> '.$user['id'].' AND (auc.starttime<'.time().' OR auc.starttime=0  ) AND auc.endtime > '.time().' '.$addsql.' ORDER BY auc.ismarked DESC, auc.ismarkedupdate DESC, '.$sortsql.' ASC '.$limit
			) or die();


			if (!isset($_GET['id'])) {
				$q2 = mysql_query('SELECT FOUND_ROWS() AS `allcount`') or die();
				$allcount = mysql_fetch_assoc($q2);
				$allcount = $allcount['allcount'];

				$pages = '<span class="pagination">Страницы:</span>
                                <ul class="pagination" >';

				for ($i = 0; $i < ceil($allcount/$view); $i++) {
					if ($page == $i) {
						$pages .= '<b> '.($i+1).'</b> ';
	                                } else {
						$pages .= '<a href="?view=auction&otdel='.$_GET['otdel'].'&page='.$i.'">'.($i+1).'</a> ';
					}
				}
				
				$pages.='</ul>';
				if (ceil($allcount/$view)>0) {
					$head= str_replace("%PAGES%",$pages,$head);
				} else {
					$head= str_replace("%PAGES%","",$head);				
				}
			}

			if (isset($_GET['error'])) {
				$typet = "e";

				switch($_GET['error']) {
					case 0:
						$error = 'Ваша ставка была принята.';
						$typet = "s";
					break;
					case 1:
						$error = 'Текущая ставка была изменена другим участником аукциона.';
					break;
					case 2:
						$error = 'Поднять ставку можно минимум на <b>';
						if ($_GET['lo']) {
							$error .=(int)($_GET['lo']);
						} else {
							$error .=1;
						}
						$error .='</b> кр.';
					break;
					case 3:
						$error = 'У вас недостаточно денег для поднятия ставки.';
					break;
					case 4:
						$error = 'У вас недостаточно денег для покупки по блиц-цене.';
					break;
					case 5:
						if (isset($_SESSION['auclastname']) && !empty($_SESSION['auclastname'])) {
							$error = 'Товар <b>'.$_SESSION['auclastname'].'</b> успешно куплен и будет добавлен в рюкзак в течение минуты.';
							$typet = "s";
							unset($_SESSION['auclastname']);
						}
					break;
					case 6:
						$error = 'Данный лот может быть куплен только по блиц-цене.';
					break;
					case 7:
						$error = 'У вас недостаточно лимита передач для покупки по блиц-цене.';
					break;
				}
				$akk_err = $error;
			}

			
			$cont='<div class="content-block a_strong">
                        <table class="table border" cellspacing="0" cellpadding="0">
                            <colgroup>
                                <col width="200px">
                            </colgroup>
                            <tbody>
                            <tr class="even2">
                                <td class="center" style="vertical-align: middle;">
                                    <ul class="dress-item" style="text-align:left;">';
                        
			$cont .= '<TABLE BORDER=0 CELLSPACING="0" CELLPADDING="0"  class="table border" >  ';
			$i = 0;
			if (mysql_num_rows($q) == 0) {
				if (isset($_GET['id'])) {
					$cont .= '<tr><td bgcolor=white>Торги для данного лота закончены.</td></tr>';
				} else {
					$cont .= '<tr><td bgcolor=white align=center>В этом отделе лоты не найдены, выберите другой раздел.</td></tr>';
				}
			}

			// output buffering для того чтобы перехватить вывод showitem()
			ob_start();
			while($item = mysql_fetch_assoc($q)) {
				$color = $i % 2 == 0 ? '#C7C7C7' : '#D5D5D5';
				$my = $item['aucnewowner'] == $user['id'] ? "<br><small><b>(Ваша&nbsp;ставка&nbsp;последняя".(($item[clan_id]>0)?" из казны":"").")</b></small><br>" : "";
				if (isset($_GET['id'])) {
					if ($item['shag']<1) { $item['shag']=1; }
					$action = 'Текущая&nbsp;ставка:<br><b>'.$item['aucrate'].'</b>&nbsp;кр.'.$my.'<br>Окончание: '.date("d/m/Y\&\\n\b\s\p\;H:i:s",$item['aucendtime']).'<br>';
					
					if ($item['st_count']>0) {
						$action .='<img src="http://i.oldbk.com/i/aukco.png" title="Ставок" alt="Ставок"><small><font color=>'.$item['st_count'].'</font><br>Последняя ставка: '.$item['st_date'].' </small>';
					}
					
					$action .='<br><form name="getup" method="POST" action="auction.php?view=auction&otdel='.$item['otdel'].'&page='.intval($_GET['page']).'&id='.$_GET['id'].'"><input type="hidden" name="lastrate" value="'.$item['aucrate'].'"><small><b>Поднять ставку&nbsp;на:<br><input type="text" size=4 name="up" value="'.$item['shag'].'">&nbsp;кр.</b></small><br>';
					if (($clan_kazna) and ( (($item['type']>=1) and ($item['type']<=11)) or ($item['type']==28))) {
						$action .='<a href="javascript:void(0);" class="button-big btn" title="Поднять для личной покупки" onClick="document.getup.submit();">Личная покупка</a>';	
						$action .= '<input type="hidden" name=ars id=ars value="">';
						$action .="<a href=\"javascript:void(0);\" class=\"button-big btn\" title=\"Поднять для покупки в арсенал\" onClick=\"document.getElementById('ars').value='yes';document.getup.submit();\">Покупка в арсенал</a>";							
					} else {
					  	//$action .= '<input type="submit" value="Поднять">';
						$action .='<a href="javascript:void(0);" class="button-big btn" title="Поднять для личной покупки" onClick="document.getup.submit();">Личная покупка</a>';	
					}
					//$action .= '</form><br><br><input type=button onClick="location.href=location.href;" value="Обновить">';
					$action .= '</form>';					
				} else {
					$action = 'Текущая&nbsp;ставка:<br><b>'.$item['aucrate'].'</b>&nbsp;кр.'.$my.'<br>Окончание: '.date("d/m/Y\&\\n\b\s\p\;H:i:s",$item['aucendtime']).'<br>';
						
					if ($item['st_count']>0) {
						$action .='<img src="http://i.oldbk.com/i/aukco.png" title="Ставок" alt="Ставок">&nbsp;<small><font color="#315FD5">'.$item['st_count'].'</font><br>Последняя ставка: '.$item['st_date'].' </small>';
					}
					
					if ($item['aucblic'] == 0 || ($item['aucblic'] > 0 && ($item['aucrate'] + $item['shag'] < $item['aucblic']))) {
						$action .="<br><a  onclick=\"getformdata('".$item['aucid']."','auction',event,".intval($_GET['page']).");\" style=\"cursor: pointer;\" >Сделать ставку</a>";
					}                                    

					if ($item['aucblic'] > 0) {
						$action .= '<br><br>';
						$action .= 'Блиц-цена:<br><b>'.$item['aucblic'].'</b>&nbsp;кр.<br><a href="?view=auction&otdel='.$type.'&blicid='.$item['aucid'].'">Купить</a><br><br>';
					}
				}

				if ($item['aucismarked']) {
					showitem($item,0,false,$color,$action,0,0,0,'#FAF0E6');
				} else {
					showitem($item,0,false,$color,$action,0,0,0);
				}
					
				$cont .= ob_get_contents();
				ob_clean ();
				$i++;
			}
			ob_end_clean();
			
			
			$cont .= '</table></ul></td>
	                            </tr>
	                            </tbody>
	                        </table>
	                    </div>';
		break;
		
		case "myrates":
			$head = str_replace("%TITLE%",'<strong>Ваши ставки</strong>',$head);
			$head = str_replace("%PAGES%",'',$head);
		 
			// выбираем все лоты, где есть у меня ставки
			$q = mysql_query('
					SELECT auc.id AS aucid, auc.itemid AS aucitemid, auc.owner AS aucowner, auc.endtime AS aucendtime, auc.newowner AS aucnewowner, auc.rate AS aucrate ,  auc.repcost AS repc , auc.blic as aucblic,auc.ismarked as aucismarked, inv.* FROM oldbk.`auction` AS `auc`
					LEFT JOIN oldbk.`inventory` AS inv
					ON auc.itemid = inv.id
					WHERE inv.owner = "'.$auctionid.'" AND auc.newowner = '.$user['id'].' AND auc.endtime > '.time().' ORDER BY auc.ismarked DESC, auc.ismarked DESC, auc.endtime ASC
			') or die();

			$cont = '<div class="content-block">
                        <table class="table border" cellspacing="0" cellpadding="0">
                            <colgroup>
                                <col width="200px">
                            </colgroup>
                            <tbody>
                            <tr class="even2">
                                <td class="center" style="vertical-align: middle;">
                                    <ul class="dress-item" style="text-align:left;">';

			if (mysql_num_rows($q) == 0) {
				$akk_err='Вы не делали ставок.';
				$typet = "e";
			}

			$cont .= '<TABLE class="table border" BORDER=0 CELLSPACING="0" CELLPADDING="0" >';
			$i = 0;

			// output buffering для того чтобы перехватить вывод showitem()
			ob_start();
			while($item = mysql_fetch_assoc($q)) {
				$color = $i % 2 == 0 ? '#C7C7C7' : '#D5D5D5';
				$action = 'Текущая&nbsp;ставка:<br><b>'.$item['aucrate'].'</b>&nbsp;'.(($item['repc']==1)?"реп":"кр").'.<br>Окончание: '.date("d/m/Y\&\\n\b\s\p\;H:i:s",$item['aucendtime']).'<br><br>';
				$action .="<br><a  onclick=\"getformdata('".$item['aucid']."','auction',event);\" style=\"cursor: pointer;\" title='Поднять свою ставку' >Поднять свою ставку</a>";					

				if ($item['aucismarked']) {
					showitem($item,0,false,$color,$action,0,0,0,'#FAF0E6');
				} else {
					showitem($item,0,false,$color,$action,0,0,0);
				}

				$cont .= ob_get_contents();
				ob_clean ();
				$i++;
			}
			ob_end_clean();
			$cont .= '</table></ul></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>';
			
		break;
		case "mylots":
			if (isset($_GET['returnitem'])) {
				$id = intval($_GET['returnitem']);
				// снятие лота
				// выбираем по ID, проверяем чтобы небыло ставки
				mysql_query('START TRANSACTION') or die();

				$q = mysql_query('
					SELECT auc.id AS aucid, auc.itemid AS aucitemid, auc.owner AS aucowner, auc.endtime AS aucendtime, auc.newowner AS aucnewowner, auc.rate AS aucrate, auc.blic as aucblic,auc.ismarked as aucismarked, inv.* FROM oldbk.`auction` AS `auc`
					LEFT JOIN oldbk.`inventory` AS inv
					ON auc.itemid = inv.id
					WHERE auc.owner = '.$user['id'].' AND auc.newowner = 0 AND auc.endtime > '.time().' AND auc.id = '.$id.' FOR UPDATE
				') or die();

				if (mysql_num_rows($q) > 0) {
					$item = mysql_fetch_assoc($q) or die();

					if ($item[arsenal_klan]!='') {
						// забираем арсенальную вещь
						if ($clan_kazna) {
							// пишем в дело

						        //new_delo
							$rec = array();
		  		    			$rec['owner']=$user[id];
							$rec['owner_login']=$user[login];
							$rec['owner_balans_do']=$user['money'];
							$rec['owner_balans_posle']=$user['money'] ;
							$rec['target']=$auctionid;
							$rec['target_login']="аукцион";
							$rec['type']=220; // забрал из аукциона в арсенал
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
							$rec['item_unic']=$item['unik'];
							$rec['item_incmagic_id']=$item['includemagic'];
		                    			$rec['item_ecost']=$item['ecost'];
							$rec['item_proto']=$item['prototype'];
                             				$rec['item_sowner']=($item['sowner']>0?1:0);
							$rec['item_incmagic']=$item['includemagicname'];
							$rec['item_incmagic_count']=$item['includemagicuses'];
							$rec['item_mfinfo']=$item['mfinfo'];
							$rec['item_level']=$item['nlevel'];
							$rec['item_arsenal']=$user['klan'];
							$rec['add_info'] = '';
							add_to_new_delo($rec); //юзеру

							$itemdescr = mysql_real_escape_string('"'.$item['name'].'" id:('.get_item_fid($item).') 1 шт. ['.$item['duration'].'/'.$item['maxdur'].'] [ups:'.$item['ups'].'/unik:'.$item['unik'].'/inc:'.$item['includemagicname'].']');
							$coment='\"'.mysql_real_escape_string($user['login']).'\" забрал вещь с аукциона в арсенал: '.$itemdescr;

							mysql_query("INSERT INTO oldbk.clans_arsenal_log (klan,pers,text,date) VALUES ('{$user[klan]}','{$user[id]}','{$coment}','".time()."')");

							// возвращаем вещь в клан арсенал
							mysql_query('UPDATE oldbk.`inventory` SET owner = 22125 WHERE id = '.$item['aucitemid']) or die();

							// удаляем запись в ауке
							mysql_query('DELETE FROM oldbk.`auction` WHERE id = '.$id);

							mysql_query('COMMIT') or die();
							Redirect('auction.php?view=mylots&return');
					    	}

					} else {
						// заберираем личную
						// пишем в дело
						if (give_count($user['id'],1))	{
						        //new_delo
							$rec = array();
			  	 			$rec['owner']=$user[id];
							$rec['owner_login']=$user[login];
							$rec['owner_balans_do']=$user['money'];
							$rec['owner_balans_posle']=$user['money'];
							$rec['target']=$auctionid;
							$rec['target_login']="аукцион";
							$rec['type']=221; // забрал из аукциона себе
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
							$rec['item_unic']=$item['unik'];
							$rec['item_incmagic_id']=$item['includemagic'];
		                    			$rec['item_ecost']=$item['ecost'];
							$rec['item_proto']=$item['prototype'];
		                     			$rec['item_sowner']=($item['sowner']>0?1:0);
							$rec['item_incmagic']=$item['includemagicname'];
							$rec['item_incmagic_count']=$item['includemagicuses'];
							$rec['item_arsenal']='';
							$rec['item_mfinfo']=$item['mfinfo'];
							$rec['item_level']=$item['nlevel'];

							$rec['add_info'] = '';
							add_to_new_delo($rec); //юзеру
		
							// возвращаем вещь владельцу
							mysql_query('UPDATE oldbk.`inventory` SET owner = '.$user['id'].' WHERE id = '.$item['aucitemid']) or die();
			
							// удаляем запись в ауке
							mysql_query('DELETE FROM oldbk.`auction` WHERE id = '.$id);
		
							mysql_query('COMMIT') or die();
							Redirect('auction.php?view=mylots&return');
						} else {
							mysql_query('COMMIT') or die();
							Redirect('auction.php?view=mylots&errcount');
						}	
					}
				}

				mysql_query('COMMIT') or die();
				Redirect('auction.php?view=mylots&notallowed');
			} elseif (isset($_GET['hlitem'])) {
				mysql_query('START TRANSACTION') or die();
				$_GET['hlitem'] = intval($_GET['hlitem']);
				$q = mysql_query('
						SELECT auc.id AS aucid, auc.itemid AS aucitemid, auc.owner AS aucowner, auc.endtime AS aucendtime, auc.newowner AS aucnewowner, auc.rate AS aucrate, auc.blic as aucblic,auc.ismarked as aucismarked, inv.* FROM oldbk.`auction` AS `auc`
						LEFT JOIN oldbk.`inventory` AS inv
						ON auc.itemid = inv.id
						WHERE auc.owner = '.$user['id'].' AND inv.owner = "'.$auctionid.'" AND auc.endtime > '.time().' and auc.id = '.$_GET['hlitem'].' FOR UPDATE
				') or die();

				$item = mysql_fetch_assoc($q) or die();

				if (isset($_SESSION['bankid'])) {
					$q = mysql_query('SELECT * FROM bank WHERE owner = '.$user['id'].' and id = '.$_SESSION['bankid']) or die();
					$bank = mysql_fetch_assoc($q);
					if ($bank) {
						if ($bank['ekr'] >= 1) {
							// снимаем бабки с банка и пишем в дело
							mysql_query("UPDATE `oldbk`.`bank` SET `ekr` = `ekr` - 1 WHERE `id`= ".$bank['id']) or die();
						
							$rec = array();
				    			$rec['owner']=$user['id'];
							$rec['owner_login']=$user['login'];
							$rec['owner_balans_do']=$user['money'];
							$rec['owner_balans_posle']=$user['money'];
							$rec['target']=$auctionid;
							$rec['target_login']="аукцион";
							$rec['type']=1108; 
							$rec['sum_ekr']=1;
							$rec['bank_id']=$bank['id'];
							$rec['add_info']='Баланс до: '.$bank['ekr'].' екр. Баланс после:'.($bank['ekr']-=1);
							add_to_new_delo($rec) or die(); 
							
							mysql_query("INSERT INTO `oldbk`.`bankhistory`(`date`, `text` , `bankid`) VALUES ('".time()."','Оплата со счета <b>1 екр.</b>. <i>(Итого: {$bank['cr']} кр., {$bank['ekr']} екр.)</i>','{$bank['id']}');") or die();
							mysql_query('UPDATE oldbk.auction SET ismarked = ismarked + 1, ismarkedupdate = '.time().' WHERE id = '.$item['aucid']) or die();
							mysql_query('COMMIT') or die();

							if ($item['aucismarked']) {
								Redirect('auction.php?view=mylots&'.(($_POST[ars]!='')?"ars=1&":"").'hlok1=1');
							} else {
								Redirect('auction.php?view=mylots&'.(($_POST[ars]!='')?"ars=1&":"").'hlok2=1');
							}
						} else {
							mysql_query('COMMIT') or die();
							Redirect('auction.php?view=mylots&'.(($_POST[ars]!='')?"ars=1&":"").'nohl=1');
						}
					}
				}
				mysql_query('COMMIT') or die();
				Redirect('auction.php?view=mylots&'.(($_POST[ars]!='')?"ars=1&":""));
			}

			// выбираем свои лоты
			$q = mysql_query('
					SELECT auc.id AS aucid, auc.itemid AS aucitemid, auc.owner AS aucowner, auc.endtime AS aucendtime, auc.newowner AS aucnewowner, auc.rate AS aucrate, auc.blic as aucblic,auc.ismarked as aucismarked, inv.* FROM oldbk.`auction` AS `auc`
					LEFT JOIN oldbk.`inventory` AS inv
					ON auc.itemid = inv.id
					WHERE auc.owner = '.$user['id'].' AND inv.owner = "'.$auctionid.'" AND auc.endtime > '.time().' ORDER BY auc.ismarked DESC, auc.ismarkedupdate DESC, auc.endtime ASC
			') or die();

			$head = str_replace("%TITLE%",'<strong>Ваши лоты</strong>',$head);
			$head = str_replace("%PAGES%",'',$head);


			$cont = '<div class="content-block">
	                        <table class="table border" cellspacing="0" cellpadding="0">
                            <colgroup>
                                <col width="200px">
                            </colgroup>
                            <tbody>
                            <tr class="even2">
                                <td class="center" style="vertical-align: middle;">
                                    <ul class="dress-item" style="text-align:left;">';
                                    
			if (isset($_GET['notallowed'])) {
				$akk_err='Вы не можете снять лот, на который была сделана ставка.';
				$typet = "e";
			}
			if (isset($_GET['errcount'])) {
				$akk_err='Вы не можете снять лот, у вас недостаточно лимита передач.';
				$typet = "e";
			}
			if (isset($_GET['return'])) {
				$akk_err='Вы сняли свой лот.';
			}
			if (isset($_GET['nohl'])) {
				$akk_err='Недостаточно еврокредитов. для выделения лота';
				$typet = "e";
			}
			if (isset($_GET['hlok1'])) {
				$akk_err='Выделение лота обновлено';
			}
			if (isset($_GET['hlok2'])) {
				$akk_err='Лот успешно выделен';
			}
			if (mysql_num_rows($q) == 0 && !isset($_GET['return'])) {
				$akk_err= 'Вы не выставили ни одного лота.';
				$typet = "e";
			}

			$cont .= '<TABLE class="table border" BORDER=0 CELLSPACING="0" CELLPADDING="0" >';
			$i = 0;

			// output buffering для того чтобы перехватить вывод showitem()
			ob_start();
			while($item = mysql_fetch_assoc($q)) {
				$color = $i % 2 == 0 ? '#C7C7C7' : '#D5D5D5';
				$returnitem = $item['aucnewowner'] == 0 ? '<br><a href="?view=mylots&returnitem='.$item['aucid'].'">Забрать лот'.(($item[arsenal_klan]!='')?' в арсенал':'').'</a>': '';
				$blic = "";
				if ($item['aucblic'] > 0) $blic = '<br>Блиц-цена: <b>'.$item['aucblic'].'</b> кр.';
				$action = 'Текущая&nbsp;ставка:<br><b>'.$item['aucrate'].'</b>&nbsp;кр.'.$blic.'<br>Окончание: '.date("d/m/Y\&\\n\b\s\p\;H:i:s",$item['aucendtime']).$returnitem."<br><br><a href='#' id=\"ismarked".$item['aucid']."\" OnClick='hlitem = \"\"; hlclick = \"ismarked".$item['aucid']."\"; var t = checkbank(); if (t) {location.href=\"auction.php?view=mylots&hlitem=".$item['aucid']."\"} else {return false;}'><b>Выделить лот<br>за 1 екр</b></a>";
				if ($item['aucismarked']) {
					showitem($item,0,false,$color,$action,0,0,0,'#FAF0E6');
				} else {
					showitem($item,0,false,$color,$action,0,0,0);
				}
				$cont .= ob_get_contents();
				ob_clean ();
				$i++;
			}
			ob_end_clean();
			$cont .= '</table></ul></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>';
                    
		break;
		case "newlot":
			$al = 'личных';
			if ($clan_kazna) {
				if ($_GET[ars]) {$ar='<div class="odd"><a href=?view=newlot>Показать личные вещи</a></div>';$al='арсенальных';} else { $ar ='<div class="odd"><a href=?view=newlot&ars=1>Показать вещи из арсенала</a></div>';  }
			} else {
				$ar='';
			}

			
			$head = str_replace("%TITLE%",'<strong>Создание нового лота из '.$al.' вещей</strong>'.$ar,$head);
			$head = str_replace("%PAGES%",'',$head);
			$cont = '<div class="content-block">
                        <table class="table border" cellspacing="0" cellpadding="0">
                            <colgroup>
                                <col width="200px">
                            </colgroup>
                            <tbody>
                            <tr class="even2">
                                <td class="center" style="vertical-align: middle;">
                                    <ul class="dress-item" style="text-align:left;">';
			if ($user['room'] != 71) {
				$akk_err='Выставлять новые лоты вы можете только в здании Аукциона на Торговой улице!';
				$typet = "e";
				$cont .= '</ul></td>
        		                    </tr>
		                            </tbody>
		                        </table>
		                    </div>';			
			} else {
				if ($_SERVER['REQUEST_METHOD'] == "POST") {
					if (isset($_GET['id']) && isset($_POST['days'],$_POST['start'])) {
						$id = intval($_GET['id']);
						$days = intval($_POST['days']);
						$start = round(floatval($_POST['start']),2);
	
						// проверяем данные
						if ($start < 1) Redirect('auction.php?view=newlot&'.(($_POST[ars]!='')?"ars=1&":"").'id='.$id.'&error=0');
	
						if ($days < 1 || $days > 30) {
							Redirect('auction.php?view=newlot&'.(($_POST[ars]!='')?"ars=1&":"").'id='.$id.'&error=1');
						}
	
						if (($clan_kazna) and ($_POST[ars]!='')) {
							if ($clan_kazna[kr] < 1) Redirect('auction.php?view=newlot&ars=1&id='.$id.'&error=2');  
						} else {
							if ($user['money'] < 1) Redirect('auction.php?view=newlot&id='.$id.'&error=2');
						}

						// всё ок - выставляем лот

						if (($clan_kazna) and ($_POST[ars]!='')) {
							 //арсенальная
							$q = mysql_query("SELECT * FROM oldbk.`inventory` WHERE `id` = '{$id}' AND owner='22125' and arsenal_klan='{$user[klan]}' and arsenal_owner=1 and `setsale` = 0 AND `sowner` =0 AND (ekr_flag=0 or ekr_flag=3)  AND  `present` = '' AND prototype !=2123456804 AND prototype != 40000001 AND cost > 0 and (art_param is null or art_param = \"\") and prototype not in (4307,4308,260,262,2000,2001,2002,2003,100005,100015,100020,100025,100040,100100,100200,100300,18210,18229,18247,18527) and otdel != 62 and notsell = 0") or die();
						} else {
							// ищем шмотку личную
							$q = mysql_query('SELECT * FROM oldbk.`inventory` WHERE `id` = '.$id.' AND `setsale` = 0 AND `owner` = '.$user['id'].' AND `dressed` = 0 AND `sowner` =0  AND (ekr_flag=0 or ekr_flag=3) AND prototype !=2123456804  AND prototype !=40000001 AND `present` = "" AND cost > 0 and (art_param is null or art_param = "") and prototype not in (4307,4308,260,262,2000,2001,2002,2003,100005,100015,100020,100025,100040,100100,100200,100300,18210,18229,18247,18527) and otdel != 62 and notsell = 0') or die();
						}
	
						$item = mysql_fetch_assoc($q) or die();
	
						$t = time();
						// окргуляем до секунд время окончания аука
						$endauc = RoundTime($t + (60*60*24*$days));

						//шаг ставки вычисляем
						$shag_st=round($start*0.02);
						if ($shag_st<1) { $shag_st=1; }

	
						// провярем годность шмотки на момент окончания аукциона
						if ($item['dategoden'] > 0) {
							// отнимаем годность на час и сравниваем с окончанием аукциона и на всякие с текущим временем
							$item['dategoden'] -= (60*60);
							if ($endauc >= $item['dategoden'] || $item['dategoden'] <= $t) {
								Redirect('auction.php?view=newlot&'.(($_POST[ars]!='')?"ars=1&":"").'id='.$id.'&error=3');
							}
						}

						$ismarked = 0;								

						$blic = floatval($_POST['blic']);
						if ($blic < 0) $blic = 0;

						if ($blic > 0 && $blic < $start) {
							Redirect('auction.php?view=newlot&'.(($_POST[ars]!='')?"ars=1&":"").'id='.$id.'&error=6');
						}

						$price = aucpriceitem($item);
						if ($blic > 0) {
							if ($blic < $price['min'] || $blic > $price['max']) die();
						}


						if (isset($_POST['ismarked']) && isset($_SESSION['bankid'])) {
							$q = mysql_query('SELECT * FROM bank WHERE owner = '.$user['id'].' and id = '.$_SESSION['bankid']) or die();
							$bank = mysql_fetch_assoc($q);
							if ($bank) {
								if ($bank['ekr'] >= 1) {
									$ismarked = 1;

									// снимаем бабки с банка и пишем в дело
									mysql_query("UPDATE `oldbk`.`bank` SET `ekr` = `ekr` - 1 WHERE `id`= ".$bank['id']) or die();
						
									$rec = array();
						    			$rec['owner']=$user['id'];
									$rec['owner_login']=$user['login'];
									$rec['owner_balans_do']=$user['money'];
									$rec['owner_balans_posle']=$user['money'];
									$rec['target']=$auctionid;
									$rec['target_login']="аукцион";
									$rec['type']=1108; 
									$rec['sum_ekr']=1;
									$rec['bank_id']=$bank['id'];
									$rec['add_info']='Баланс до: '.$bank['ekr'].' екр. Баланс после:'.($bank['ekr']-=1);
									add_to_new_delo($rec) or die(); 
							
									mysql_query("INSERT INTO `oldbk`.`bankhistory`(`date`, `text` , `bankid`) VALUES ('".time()."','Оплата со счета <b>1 екр.</b>. <i>(Итого: {$bank['cr']} кр., {$bank['ekr']} екр.)</i>','{$bank['id']}');") or die();
		
								} else {
									Redirect('auction.php?view=newlot&'.(($_POST[ars]!='')?"ars=1&":"").'id='.$id.'&error=5');
								}
							}
						}

	
						//работаем
						if (($clan_kazna) and ($_POST['ars']!=''))  {
							if (by_from_kazna($clan_id['id'],1,1,$coment)) {
							        //new_delo
								$rec = array();
				  	 			$rec['owner']=$user[id];
								$rec['owner_login']=$user[login];
								$rec['owner_balans_do']=$user['money'];
								$rec['owner_balans_posle']=$user['money'];
								$rec['target']=$auctionid;
								$rec['target_login']="аукцион";
								$rec['type']=222; // выставил из арсенала в аук
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
								$rec['item_unic']=$item['unik'];
								$rec['item_incmagic_id']=$item['includemagic'];
			                    			$rec['item_ecost']=$item['ecost'];
								$rec['item_proto']=$item['prototype'];
	                             				$rec['item_sowner']=($item['sowner']>0?1:0);
								$rec['item_incmagic']=$item['includemagicname'];
								$rec['item_incmagic_count']=$item['includemagicuses'];
								$rec['item_mfinfo']=$item['mfinfo'];
								$rec['item_level']=$item['nlevel'];
								$rec['item_arsenal']=$user['klan'];
								$addinfo = $days.'/'.$start;
								if ($blic > 0) $addinfo .= '/'.$blic;
								$rec['add_info'] = $addinfo;
								add_to_new_delo($rec); //юзеру
	
								//арсенальная
								$itemdescr = mysql_real_escape_string('"'.$item['name'].'" id:('.get_item_fid($item).') 1 шт. ['.$item['duration'].'/'.$item['maxdur'].'] [ups:'.$item['ups'].'/unik:'.$item['unik'].'/inc:'.$item['includemagicname'].']');
								$coment='\"'.mysql_real_escape_string($user['login']).'\" выставил вещь из арсенала на аукцион: '.$itemdescr.' сроком на '.$days.' дн. и начальной стоимостью '.$start.' кр. (снято 1 кр. налога из казны)';
	
								mysql_query("INSERT INTO oldbk.clans_arsenal_log (klan,pers,text,date) VALUES ('{$user[klan]}','{$user[id]}','{$coment}','".time()."')");
					
								
								// добавляем вещь в аукцион
								if ($ismarked > 0) {
									mysql_query('
										INSERT INTO oldbk.`auction` (`itemid` ,`owner`, `endtime`, `rate`, `shag`, `blic`, `ismarked`,`ismarkedupdate`)
										VALUES
										(
											"'.$item['id'].'",
											"'.$user['id'].'",
											"'.$endauc.'",
											"'.$start.'",
											"'.$shag_st.'",
											"'.$blic.'",
											"'.$ismarked.'",
											"'.time().'"
										)
									') or die();
								} else {
									mysql_query('
										INSERT INTO oldbk.`auction` (`itemid` ,`owner`, `endtime`, `rate`, `shag`, `blic`, `ismarked`)
										VALUES
										(
											"'.$item['id'].'",
											"'.$user['id'].'",
											"'.$endauc.'",
											"'.$start.'",
											"'.$shag_st.'",
											"'.$blic.'",
											"'.$ismarked.'"
										)
									') or die();
								}
	
								// отбираем вещь в аукцион
								mysql_query('UPDATE oldbk.`inventory` SET owner = "'.$auctionid.'" WHERE id = '.$item['id']) or die();
							}
						} else {
							if (give_count($user['id'],1)) {
								// личная
								// пишем в дело
							        //new_delo
								$rec = array();
				  	 			$rec['owner']=$user[id];
								$rec['owner_login']=$user[login];
								$rec['owner_balans_do']=$user['money'];
								$rec['owner_balans_posle']=$user['money']-1;
								$rec['target']=$auctionid;
								$rec['target_login']="аукцион";
								$rec['type']=223; // выставил из своих вещей в аук
								$rec['sum_kr']=1;
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
								$rec['item_unic']=$item['unik'];
								$rec['item_incmagic_id']=$item['includemagic'];
			                    			$rec['item_ecost']=$item['ecost'];
								$rec['item_proto']=$item['prototype'];
		                        			$rec['item_sowner']=($item['sowner']>0?1:0);
								$rec['item_incmagic']=$item['includemagicname'];
								$rec['item_incmagic_count']=$item['includemagicuses'];
								$rec['item_arsenal']='';
								$rec['item_mfinfo']=$item['mfinfo'];
								$rec['item_level']=$item['nlevel'];

								$addinfo = $days.'/'.$start;
								if ($blic > 0) $addinfo .= '/'.$blic;
								$rec['add_info'] = $addinfo;
								add_to_new_delo($rec); //юзеру
		
								// снимаем налог на выставление лота
								mysql_query('UPDATE `users` SET `money` = `money` - "'.$aucnewlottax.'" WHERE id = '.$user['id']) or die();
								
					
								// добавляем вещь в аукцион
								if ($ismarked > 0) {
									mysql_query('
										INSERT INTO oldbk.`auction` (`itemid` ,`owner`, `endtime`, `rate`, `shag`,`blic`,`ismarked`,`ismarkedupdate`)
										VALUES
										(
											"'.$_GET['id'].'",
											"'.$user['id'].'",
											"'.$endauc.'",
											"'.$start.'",
											"'.$shag_st.'",
											"'.$blic.'",
											"'.$ismarked.'",
											"'.time().'"
										)
									') or die();
								} else {
									mysql_query('
										INSERT INTO oldbk.`auction` (`itemid` ,`owner`, `endtime`, `rate`, `shag`,`blic`,`ismarked`)
										VALUES
										(
											"'.$_GET['id'].'",
											"'.$user['id'].'",
											"'.$endauc.'",
											"'.$start.'",
											"'.$shag_st.'",
											"'.$blic.'",
											"'.$ismarked.'"
										)
									') or die();
								}
								// отбираем вещь в аукцион
								mysql_query('UPDATE oldbk.`inventory` SET owner = "'.$auctionid.'" WHERE id = '.$_GET['id']) or die();
							} else {
								Redirect('auction.php?view=newlot&'.(($_POST[ars]!='')?"ars=1&":"").'id='.$id.'&error=4');
							}
						}
						
						// говорим что всё ок
						Redirect('auction.php?view=newlot&'.(($_POST[ars]!='')?"ars=1&":"").'ok');
					}
					Redirect('auction.php?view=newlot');
				}
			

				// выбираем вещи для сдачи в аук
				if (($clan_kazna) and ($_GET[ars])) {
					$q = mysql_query("SELECT * FROM oldbk.`inventory` WHERE owner='22125' and arsenal_klan='{$user[klan]}' and arsenal_owner=1 and `setsale` = 0 AND (ekr_flag=0 or ekr_flag=3) and otdel!=72  and type!=77 AND prototype !=2123456804 AND prototype !=40000001 AND `sowner` =0 AND  `present` = '' AND cost > 0 and prototype not in (4307,4308,260,262,2000,2001,2002,2003,5277,100005,100015,100020,100025,100040,100100,100200,100300,18210,18229,18247,18527) and (art_param is null or art_param = \"\") and otdel != 62 and notsell = 0 ORDER by `update` DESC") or die();
				} else {
					$q = mysql_query('SELECT * FROM oldbk.`inventory` WHERE `setsale` = 0 AND `owner` = '.$user['id'].' AND `dressed` = 0 AND `sowner` =0 AND (ekr_flag=0 or ekr_flag=3) and otdel!=72 and type!=77 AND prototype !=2123456804 AND prototype !=40000001 AND  `present` = "" AND cost > 0 and (art_param is null or art_param = "") and prototype not in (4307,4308,260,262,2000,2001,2002,2003,5277,100005,100015,100020,100025,100040,100100,100200,100300,18210,18229,18247,18527) and otdel != 62 and notsell = 0 ORDER by `update` DESC') or die();
				}
	
				if (isset($_GET['ok'])) {
					$akk_err='Вещь удачно сдана в аукцион, ожидайте окончания торгов.';
				}
				if (mysql_num_rows($q) == 0 && !isset($_GET['ok'])) {
					$akk_err = 'У вас нет подходящих вещей для выставления на аукцион.';
					$typet = "e";
				}
				$cont .= '<TABLE width=80% BORDER=0 CELLSPACING="1" CELLPADDING="2"><TR><TD>';
				$cont .= '<TABLE BORDER=0 CELLSPACING="1" CELLPADDING="2" BGCOLOR="#A5A5A5">';
				$cont .= '<colgroup><col width="150px"><col></colgroup>';
				$i = 0;

				// output buffering для того чтобы перехватить вывод showitem()
				ob_start();
				while($item = mysql_fetch_assoc($q)) {
					$color = $i % 2 == 0 ? '#C7C7C7' : '#D5D5D5';
	
					if (($clan_kazna) and ($_GET[ars])) {
						$action = '<a href="?view=newlot&ars=1&id='.$item['id'].'">Выставить лот из арсенала</a>';
					} else {
						$action = '<a href="?view=newlot&id='.$item['id'].'">Выставить лот</a>';
					}
	
					showitem($item,0,false,$color,$action);
					$cont .= ob_get_contents();
					ob_clean ();
					$i++;
				}
				ob_end_clean();
				$cont .= '</table>';
				$cont .= '</TD><TD valign=top>';

				if (isset($_GET['id'])) {
					if (isset($_GET['error'])) {
						switch($_GET['error']) {
							case 0:
								$error = 'Минимальная сумма для лота - 1 кр.';
							break;
							case 1:
								$error = 'Количество дней должно быть от 1 до 30.';
							break;
							case 2:
								$error = 'У вас недостаточно денег для выставления лота.';
							break;
							case 3:
								$error = 'Вы не можете выставить этот лот. Срок годности вещи заканчивается раньше окончания аукциона.';
							break;
							case 4:
								$error = 'У вас недостаточно лимита передач.';
							break;						
							case 5:
								$error = 'Не достаточно екр на счету в банке.';
							break;						
							case 6:
								$error = 'Блиц-цена не должна быть меньше начальной ставки.';
							break;						

						}
						$akk_err=$error;
						$typet = "e";
					}
					$cont .= '<table id="newlot" BORDER=0 CELLSPACING="1" CELLPADDING="2" BGCOLOR="#A5A5A5">
			            		<colgroup>
	                				<col width="150px">
	                				<col>
	            				</colgroup>
					<tr bgcolor=#A5A5A5><td colspan=2 align=center>Вы хотите выставить вещь на аукцион</td></tr>';
					$id = intval($_GET['id']);
	
	
					if (($clan_kazna) and ($_GET[ars])) {
		 			        $q = mysql_query("SELECT * FROM oldbk.`inventory` WHERE `id` = '{$id}' AND owner='22125' and arsenal_klan='{$user[klan]}' and arsenal_owner=1 AND (ekr_flag=0 or ekr_flag=3) and otdel!=72 and type!=77 AND prototype !=2123456804 AND prototype !=40000001 and prototype not in (100005,100015,100020,100025,100040,100100,100200,100300) and `setsale` = 0 AND `sowner` =0 AND  `present` = '' AND cost > 0 ") or die();
					} else {
						$q = mysql_query('SELECT * FROM oldbk.`inventory` WHERE `id` = '.$id.' AND `setsale` = 0 AND `owner` = '.$user['id'].' AND `dressed` = 0 AND (ekr_flag=0 or ekr_flag=3)  and otdel!=72  and type!=77 AND prototype !=2123456804 AND prototype !=40000001 and prototype not in (100005,100015,100020,100025,100040,100100,100200,100300) AND `sowner` =0  AND  `present` = "" AND cost > 0') or die();
					}
	
					$item = mysql_fetch_assoc($q) or die();
	
					// output buffering для того чтобы перехватить вывод showitem()
					ob_start();
					showitem($item,0,false,'#C7C7C7','&nbsp;');
					$cont .= ob_get_contents();
					ob_end_clean();
					$nl = str_replace("%AUCTAX%",$aucnewlottax,$newlot);
					$price = aucpriceitem($item);
					$minmax = "<script>var minprice = ".$price['min']."; var maxprice = ".$price['max'].";</script><div id=\"minmax\" style='display:none;'><font color=red>";
					if ($price['min'] > 0 && $price['max'] > 0) {
						$minmax .= "Минимальная блиц-цена для этого предмета ".$price['min']." кр., максимальная цена ".$price['max']." кр.";
					} elseif ($price['min'] > 0) {
						$minmax .= "Минимальная блиц-цена для этого предмета ".$price['min']." кр.";
					} elseif ($price['max'] > 0) {
						$minmax .= "Максимальная блиц-цена для этого предмета ".$price['max']." кр.";
					}
					$minmax .= "</font></div>";
					$nl = str_replace("%MINMAX%",$minmax,$nl);

	
					if (($clan_kazna) and ($_GET[ars])) {
						$nl = str_replace("%ARSENAL%",'<input type=hidden name=ars value=1>',$nl);
					} else {
						$nl = str_replace("%ARSENAL%",'',$nl);
					}
	
					$nl = str_replace("%AUCTAXSELL%",$auctaxsell*100,$nl);
					$cont .= $nl;
					$cont .= '</table>';
	
				}
				
				$cont .= '</TD></TR>';
				$cont .= '</table></ul></td>
			                            </tr>
			                            </tbody>
			                        </table>
			                    </div>';			
			}
				
		break;
	}

	
	if ((isset($_GET['id'])) AND (($_GET['view']=='auction')) AND (!(isset($_GET['error'])))) {
		echo '<table border=0 width=600 height=300 ><tr><td  valign=top align="center"  colspan="4"><center><font style="COLOR:#8f0000;FONT-SIZE:12pt">'; 
		echo "Сделать ставку";
		echo '</font></center><a onClick="closeinfo();" title="Закрыть" style="cursor: pointer;" >
		<img src="http://i.oldbk.com/i/bank/bclose.png" style="position:relative;top:-35px;right:-300px;" border=0 title="Закрыть"></a></td></tr>
		<tr><td colspan="4" class="center">';
		echo $cont;
		echo '</td>
		</tr><tr><td align="center"  colspan="3">
		</td></tr>
		</table>';
	} else {
		$head = str_replace('%MONEY%',$user['money'],$head);
		$head = str_replace('%WINSIZE%',"width: 600px; ",$head);

		if  ($clan_kazna) {
			$head = str_replace('%KAZNA%','В клановой казне: <span class="money"><strong>'.$clan_kazna['kr'].'</strong></span><strong> кр.</strong><br>',$head);
		} else {
			$head = str_replace('%KAZNA%','',$head);
		}

		$bankinfo = "";
		if (isset($_SESSION['bankid']) && !empty($_SESSION['bankid'])) {
			$q = mysql_query('SELECT * FROM bank WHERE owner = '.$user['id'].' and id = '.$_SESSION['bankid']);
			if (mysql_num_rows($q) > 0) {
				$bank = mysql_fetch_assoc($q);
				$bankinfo = 'У Вас в наличии: <span class="money"><strong>'.$bank['ekr'].'</strong></span><strong> екр.</strong><br>';
			}
		}
		$head = str_replace('%BANKINFO%',$bankinfo,$head);
		
		$head = str_replace('%REPA%',$user['repmoney'],$head);

		$head = str_replace('%KURSWMZ%',(((int)((1/(1+get_ekr_addbonus()))*100))/100),$head);	

		$head = str_replace('%CONTENT%',$cont,$head);	

		if ($tpl != '') {
			$otdels  = str_replace('%FILTER%',$tpl,$otdels);
		} else {
			$ar = get_itemfilt();			
			$otdels  = str_replace('%FILTER%',$itemsfilter,$otdels);			
		}


		$head = str_replace('%OTDEL%',$otdels,$head);		


		$head = str_replace('%auction%',($_GET['view']=='auction'?'<a  class="active">Аукцион</a>':'<a  href="?view=auction">Аукцион</a>'),$head);			
		$head = str_replace('%myrates%',($_GET['view']=='myrates'?'<a  class="active">Мои ставки</a>':'<a  href="?view=myrates">Мои ставки</a>'),$head);			
		$head = str_replace('%mylots%',($_GET['view']=='mylots'?'<a  class="active">Мои лоты</a>':'<a  href="?view=mylots">Мои лоты</a>'),$head);			
		$head = str_replace('%newlot%',($_GET['view']=='newlot'?'<a  class="active">Новый лот</a>':'<a  href="?view=newlot">Новый лот</a>'),$head);						
	
		$head = str_replace("%TITLE%",'',$head);
		$head = str_replace("%PAGES%",'',$head);

		$head = str_replace("%BANKID%",(isset($_SESSION['bankid']) && $_SESSION['bankid'] > 0 ? "true" : "false"),$head);
	                           
		if (strlen($akk_err)) {
			$msg='	
			<script>
				var n = noty({
					text: "'.addslashes($akk_err).'",
				        layout: "topLeft2",
				        theme: "relax2",
					type: "'.($typet == "e" ? "error" : "success").'",
				});
			</script>';
			$head = str_replace('%MESSAGE%',$msg,$head);
		} else {
			$head = str_replace('%MESSAGE%','',$head);
		}
		
	
		echo $head;
		echo $center;
		if(isset($_SESSION['vk']) && is_array($_SESSION["vk"])) {
			echo str_replace("%MAILRU%","",$foot);
		} else {
			echo str_replace("%MAILRU%",$mailrucounter,$foot);
		}
	}

?>