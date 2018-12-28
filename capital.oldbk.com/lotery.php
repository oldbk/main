<?

//компресия для инфы
///////////////////////////
    if (strpos(' ' . $_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip') !== false) {
    $miniBB_gzipper_encoding = 'x-gzip';
    }
    if (strpos(' ' . $_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false) {
    $miniBB_gzipper_encoding = 'gzip';
    }
    if (isset($miniBB_gzipper_encoding)) {
    ob_start();
    }
    function percent($a, $b) {
    $c = $b/$a*100;
    return $c;
    }
//////////////////////////////
 	session_start();
	if (!($_SESSION['uid'] >0)) {  header("Location: index.php"); die(); }
	include 'connect.php';
	include 'functions.php';
	//if ($user[klan]=='radminion') {  echo "Admin-info:<!- GZipper_Stats -> <br>"; }
	if ($user['room'] != 42){ header("Location: main.php"); die(); }
    	if ($user['battle'] != 0) { header('location: fbattle.php'); die(); }


	function add_delo_lot($summ,$type,$tic_id=0,$add_info=''){
		global $user;
		$rec['owner']=$user[id];
		$rec['owner_login']=$user[login];
		$rec['owner_balans_do']=$user[money];
		if($type==287)
		{
			$rec['owner_balans_posle']=$user[money]-$summ;
		}
		else
		if($type==286)
		{
			$rec['owner_balans_posle']=$user[money]+$summ;
		}
		$rec['target']=0;
		$rec['target_login']='Лоттерея Сталкеров';
		$rec['type']=$type;//купили сообщения
		$rec['sum_kr']=$summ;
		$rec['item_id']=$tic_id;
		$rec['item_count']=($tic_id>0?1:0);
		$rec['add_info']=$add_info;
		add_to_new_delo($rec);
	}
class Lottery{
	function get_this_user_id(){
		// определеить id пользователя
		global $user;
		return $user['id'];
	}
	

	
	function buy($txt = ''){
		// списать сумму билета
		global $user;
		if ($user['money'] < 1) {
			$this->mess = 'Не хватает денег<BR>';
		} else {
			mysql_query("update users set money = money - 1 where id = ".$user['id'].";");
			mysql_query("insert into oldbk.inventory (`owner`,`name`,`maxdur`,`img`,`letter`,`type`,`idcity`) values ('".$user['id']."','Лотерейный билет','1','loto.gif','".$txt."','210','{$user[id_city]}');");
			$tic_id=mysql_insert_id();
			if($tic_id>0)
	        	{
	        		add_delo_lot(1,287,$tic_id,$txt);
	        	}
		}
	}

	function pay_for_5($summ,$txt){
		// оплата если 5 из 5 угадано
		global $user;
        	mysql_query("update users set money = money + '".$summ."' where id = ".$user['id'].";");
        	if(mysql_affected_rows()>0)
        	{
        		add_delo_lot($summ,286,0,$txt);
        	}
	}

	function pay_for_4($summ,$txt){
		// оплата если 4 из 5 угадано
		global $user;
        	mysql_query("update users set money = money + '".$summ."' where id = ".$user['id'].";");
        	if(mysql_affected_rows()>0)
        	{
        		add_delo_lot($summ,286,0,$txt);
        	}
	}

	function pay_for_3($summ,$txt){
		// оплата если 3 из 5 угадано
		global $user;
		mysql_query("update users set money = money + '".$summ."' where id = ".$user['id'].";");
		if(mysql_affected_rows()>0)
        	{
        		add_delo_lot($summ,286,0,$txt);
        	}
	}

	function pay_for_klan($summ){
		// 10% клану
		// хуй
		global $user;
		mysql_query("update users set money = money + '".$summ."' where id = 7014;");
	}

	function buy_ticket($selected_str){
		$selected_str = substr($selected_str,0,strlen($selected_str)-1);
		$selected_array = explode(',',$selected_str);
		sort($selected_array);

		$id_user = $this->get_this_user_id();

		if (sizeof($selected_array) > 5){
			$sql_ins_cheat = "insert into oldbk.lottery_cheaters(`id_user`,`values`,`date`) values('".$id_user."','".$selected_str."','".date('Y-m-d H:i:s')."')";
			mysql_query($sql_ins_cheat);
		}

		for($i=0;$i<5;$i++){
			if (intval($selected_array[$i]) > 30 || intval($selected_array[$i]) < 1) {
				$values .='1,';
			} else {
				$values .= ((int)($selected_array[$i])).',';
			}
		}

	        $sql = "select id from oldbk.lottery where end='0'";
		$res = mysql_query($sql);
		while($result_lottery = mysql_fetch_assoc($res)){
			$id_lottery = $result_lottery['id'];
		}

		$this->buy("Тираж № ".$id_lottery."<BR>Выбраные номера: ".$values);

        if($this->mess != null) {
        	return "<font color=red><B>".$this->mess."</font></b>";
        }
        echo "<font color=red><B>Билет куплен.<BR></font></b>";

		$date = date('Y-m-d H:i:s');



		$sql = "insert into oldbk.lottery_log(`id_user`,`values`,`date`,`id_lottery`) values('".$id_user."','".$values."','".$date."','".$id_lottery."')";
		$res = mysql_query($sql);

		$jackpot = 0;
		$sql = "select * from oldbk.`lottery` where end=0 limit 1";
		$res = mysql_query($sql);
		while($result = mysql_fetch_assoc($res)){
			$id = $result['id'];
			$jackpot = $result['jackpot'];
			$fond = $result['fond'];
		}

		$fond += 0.7;

		$sql = "update oldbk.lottery set fond='".$fond."' where id='".$id."' ";
		mysql_query($sql);
	}

	function get_result(){
		$array = range(1,30);
		shuffle($array);

		for($i=0;$i<5;$i++){
			$result[] = $array[$i];
		}

		return $result;
	}

	function get_count($win_combination,$user_combination){
		$user_array = explode(',',$user_combination);

		$count = 0;

		for($i=0;$i<5;$i++){
			if (strpos(",".$win_combination,",".$user_array[$i].",") !== FALSE){
				$count ++; //echo substr($win_combination,$z,1)." ";
			}
		}

		return $count;
	}

	function get_win_combination(){
		$win_combination = $this->get_result();

		for($i=0;$i<5;$i++){
			$win_combination_str .= $win_combination[$i].',';
		}


		$sql = "select id,jackpot,fond from oldbk.lottery where end='0'";
		$res = mysql_query($sql);
		while($result = mysql_fetch_assoc($res)){
			$id_lottery = $result['id'];
			$jackpot = $result['jackpot'];
			$fond = $result['fond'];
		}

		$sql = "insert into oldbk.lottery_win_combination(`values`,`date`,`id_lottery`) values('".$win_combination_str."','".date('Y-m-d H:i:s')."','".$id_lottery."') ";
		mysql_query($sql);

		$people_5 = 0;
		$people_4 = 0;
		$people_3 = 0;

		$sql = "select * from oldbk.lottery_log where id_lottery='".$id_lottery."' ";
		$res = mysql_query($sql);
		while($result = mysql_fetch_assoc($res)){
			$count = $this->get_count($win_combination_str,$result['values']);

			if ($count == 5){
				$people_5 ++;
			}
			if ($count == 4){
				$people_4 ++;
			}
			if ($count == 3){
				$people_3 ++;
			}
		}

		$klan_pay = $fond*0.05;
		$this->pay_for_klan($klan_pay);
		$fond = $fond - $klan_pay;

		if ($people_5 > 0 ){
			$summ_5 = ($jackpot+($fond*0.3))/$people_5;
			$jackpot = 0;
		}
		else{
			$summ_5 = ($fond*0.3);
			$jackpot += $fond*0.3;
		}
		if ($people_4 > 0){
			$summ_4 = ($fond*0.3)/$people_4;
		}
		else{
			$summ_4 = ($fond*0.3);
			$jackpot += $fond*0.3;
		}
		if ($people_3 > 0){
			$summ_3 = ($fond*0.4)/$people_3;
		} else{
			$summ_3 = $fond*0.4;
			$jackpot += $fond*0.4;
		}


		$sql_upd = "update oldbk.lottery set end='1' , fond='".$fond."' , summ_5='".$summ_5."' , summ_4='".$summ_4."' , summ_3='".$summ_3."' , count_5='".$people_5."' , count_4='".$people_4."' , count_3='".$people_3."' where id='".$id_lottery."'";
		mysql_query($sql_upd);

		$sql_ins = "insert into oldbk.lottery(`date`,`jackpot`,`fond`,`end`,`summ_5`,`summ_4`,`summ_3`,`count_5`,`count_4`,`count_3`) values('".date('Y-m-d H:i:s',strtotime("+1 week"))."','".$jackpot."','0','0','0','0','0','0','0','0')";
		mysql_query($sql_ins);
	}

	function check($id_lottery){
		$id_user = $this->get_this_user_id();

		//$sql_comb = "select values from lottery_win_combination where id_lottery='".$id_lottery."'";

		if ($id_lottery < 1)  {
			$sql_comb = "select * from oldbk.lottery where end=1 order by id DESC LIMIT 1;";
			$res_comb = mysql_fetch_array(mysql_query($sql_comb));
			$id_lottery = $res_comb['id'];
		}

        $sql_comb = "select * from oldbk.lottery_win_combination where id_lottery='".$id_lottery."'";

		$res_comb = mysql_query($sql_comb);


		while($result_comb = mysql_fetch_assoc($res_comb)){
			$win_combination_str = $result_comb['values'];
		}

		$sql_summ = "select * from oldbk.lottery where id='".$id_lottery."'";
		$res_summ = mysql_query($sql_summ);
		while($result_summ = mysql_fetch_assoc($res_summ)){
			$summ_5 = $result_summ['summ_5'];
			$summ_4 = $result_summ['summ_4'];
			$summ_3 = $result_summ['summ_3'];
			$jackpot = $result_summ['jackpot'];
		}

		$sql = "select * from oldbk.lottery_log where id_lottery='".$id_lottery."' and id_user='".$id_user."' and send='0' ";
		$res = mysql_query($sql);
		while($result = mysql_fetch_assoc($res)){
			$count = $this->get_count($win_combination_str,$result['values']);

			if ($count == 5){
				$this->pay_for_5($jackpot,$result['values']);
				$str.= "Билет <B>№ ".$result['id']."</B> выиграл <b>".$jackpot." кр.</b> Выбраные номера: ".$result['values']."<BR>";
				$zz = 1;
			}
			if ($count == 4){
				$this->pay_for_4($summ_4,$result['values']);
				$str.= "Билет <B>№ ".$result['id']."</B> выиграл <b>".$summ_4." кр.</b> Выбраные номера: ".$result['values']."<BR>";
				$zz = 1;
			}
			if ($count == 3){
				$this->pay_for_3($summ_3,$result['values']);
				$str.= "Билет <B>№ ".$result['id']."</B> выиграл <b>".$summ_3." кр.</b> Выбраные номера: ".$result['values']."<BR>";
				$zz = 1;
			}

			$sql_upd = "update oldbk.lottery_log set send='1' where id='".$result['id']."'";
			mysql_query($sql_upd);
		}
		if (!$zz) {
			$str.= "<font color=red><B>Нет выигрышных билетов</b></font><BR>";
		}
	
	return $str;
	}

	function view_results($id_lottery = 0){
		$str = '';
        if ($id_lottery > 0) {
			$sql = "select * from oldbk.lottery where id='".$id_lottery."' and end=1;";
		}
		else {
			$sql = "select * from oldbk.lottery where end=1 order by id DESC LIMIT 1;";
		}
        $res = mysql_query($sql);

		while ($result = mysql_fetch_assoc($res)){
			$id_lottery = $result['id'];
			$date = $result['date'];
			$jackpot = round($result['jackpot'],2);
			$fond = round($result['fond'],2);
			$summ_5 = round($result['summ_5'],2);
			$summ_4 = round($result['summ_4'],2);
			$summ_3 = round($result['summ_3'],2);
			$count_5 = $result['count_5'];
			$count_4 = $result['count_4'];
			$count_3 = $result['count_3'];
		}

		$summ = $summ_5 + $summ_4 + $summ_3;
		$count = $count_5 + $count_4 + $count_3;

		$sql_combination = "select * from oldbk.lottery_win_combination where id_lottery='".$id_lottery."'";
		$res_combination = mysql_query($sql_combination);
		while($result_combination = mysql_fetch_assoc($res_combination)){
			$combination = $result_combination['values'];
		}

		$sql = "select * from oldbk.lottery_log where id_lottery='".$id_lottery."'";
		$res = mysql_query($sql);
        	$allbillets = mysql_num_rows($res);

		$str .= '<div id="bottom-info">
                            <div class="check-input">
                            <form method="post" name="chfrm">
                            <span>Итоги тиража номер:</span> <input type="text" value="'.$id_lottery.'" size=4 name="tiraj"> <a href="javascript:void(0);" class="button-mid btn" title="Проверить" onClick="document.chfrm.submit();" >Проверить</a>
                            </form>
                            </div>';
		
		if (!$date) {
        	 return $str.'Лотерея не проводилась.';
        }
        
        	$str.='
        	                            <div class="check-info">
                                <ul>
                                    <li>
                                        Состоялся: <div class="date">'.$date.'</div>
                                    </li>
                                    <li>
                                        Призовой фонд: <strong>'.$fond.' кр.</strong>
                                    </li>
                                    <li>
                                        Джекпот: <strong>'.round($jackpot,2).'  кр.</strong>
                                    </li>
                                    <li>
                                        Всего было продано билетов: <strong>'.$allbillets.'</strong>
                                    </li>
                                </ul>
                            </div>
                        </div>
                  <div id="loto-numbers">';
                  $win_dig=explode(",",$combination);
          	$str.='   <div id="n1">'.$win_dig[0].'</div>
                            <div id="n2">'.$win_dig[1].'</div>
                            <div id="n3">'.$win_dig[2].'</div>
                            <div id="n4">'.$win_dig[3].'</div>
                            <div id="n5">'.$win_dig[4].'</div>
                        </div>

                         <div id="bottom-stats">
                            <table cellspacing="0" cellpadding="0">
                                <thead>
                                <tr class="head-line">
                                    <th>
                                        <div class="head-left"></div>
                                        <div class="head-title p"><a onclick="location.href=\'lotery.php?check=1\';" style="cursor:pointer;">Проверить лотерейные билеты</a></div>
                                        <div class="head-right"></div>
                                    </th>
                                    
                                </tr>
                                </thead>
                            </table>
                            <table class="stats" cellspacing="0" cellpadding="0">
                                <colgroup>
                                    <col>
                                    <col width="50px">
                                </colgroup>
                                <tbody>
                                <tr>
                                    <td>
                                        <ul>
                                            <li>
                                                Всего победителей тиража: <strong>'.$count.'</strong>
                                            </li>
                                            <li>
                                                Всего выиграно: <strong>'.$summ.' кр.</strong>
                                            </li>
                                        </ul>
                                    </td>
                                    <td id="show-stats">
                                        <a href="javascript:void(0);"></a>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div id="stats-table" style="top:470px;">
                            <table cellspacing="0" cellpadding="0">
                                <thead>
                                    <tr>
                                        <th>Угадано номеров</th>
                                        <th>Выиграно билетов</th>
                                        <th>Сумма выигрыша</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <strong>3</strong>
                                        </td>
                                        <td>
                                            <strong>'.$count_3.'</strong>
                                        </td>
                                        <td>
                                            '.($count_3==0?'Не выиграл ни один билет <strong>'.$summ_3.' кр.</strong> идут в джекпот':'<strong>'.$summ_3.' кр.</strong>').'
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong>4</strong>
                                        </td>
                                        <td>
                                            <strong>'.$count_4.'</strong>
                                        </td>
                                        <td>
                                            '.($count_4==0?'Не выиграл ни один билет <strong>'.$summ_4.' кр.</strong> идут в джекпот':'<strong>'.$summ_4.' кр.</strong>').'
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong>5</strong>
                                        </td>
                                        <td>
                                            <strong>'.$count_5.'</strong>
                                        </td>
                                        <td class="win">
                                            '.($count_5==0?'Не выиграл ни один билет <strong>'.$summ_5.' кр.</strong> идут в джекпот':'<strong>'.$summ_5.' кр.</strong>').'
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
		';
		
		
		return $str;
	}

	function view_buy_ticket(){
		$str = '';

		$str .= '
		<style>
		td.select{ width: 20px; text-align: center; background-color: #999; cursor: pointer; }
		td.unselect{ width: 20px; text-align: center; background-color: none; cursor: pointer; }
		</style>
		<script>
		function CheckFive() {
			var test = document.getElementById(\'value\').value;

			if (test.indexOf(",") > 0 ){
				array = test.split(",");
				if (array.length == 6) {
					return true;
				} else {
					alert("Выберите 5 номеров");
				}
			}
			return false;
		}

		function add(name){
			var array = new Array();
			var test = document.getElementById(\'value\').value;

			if (test.indexOf(",") > 0){
				array = test.split(",");

				if (array[5] != \'\'){
					document.getElementById(name).className=\'select\';
					document.getElementById(name).onclick = function() { del(name) };
					test = test + name + ",";
					document.getElementById(\'value\').value = test;
				}
				else{
					alert(\'Вы выбрали уже 5 номеров. Снимите выделение с любого номера.\');

				}
			}
			else{
				document.getElementById(name).className=\'select\';
				document.getElementById(name).onclick = function() { del(name) };
				test = test + name + ",";
				document.getElementById(\'value\').value = test;
			}
		}
		function del(name){
			var array = new Array();
			var test = document.getElementById(\'value\').value;

			document.getElementById(name).className=\'unselect\';
			document.getElementById(name).onclick = function() { add(name) };
			test = test.replace(name+",","");
			document.getElementById(\'value\').value = test;
		}
		</script>

		<table style="background-color: #ccc">
			<tr>
				<td class="unselect" id="1" onclick="add(\'1\')">1</td>
				<td class="unselect" id="2" onclick="add(\'2\')">2</td>
				<td class="unselect" id="3" onclick="add(\'3\')">3</td>
				<td class="unselect" id="4" onclick="add(\'4\')">4</td>
				<td class="unselect" id="5" onclick="add(\'5\')">5</td>
			</tr>
			<tr>
				<td class="unselect" id="6" onclick="add(\'6\')">6</td>
				<td class="unselect" id="7" onclick="add(\'7\')">7</td>
				<td class="unselect" id="8" onclick="add(\'8\')">8</td>
				<td class="unselect" id="9" onclick="add(\'9\')">9</td>
				<td class="unselect" id="10" onclick="add(\'10\')">10</td>
			</tr>
			<tr>
				<td class="unselect" id="11" onclick="add(\'11\')">11</td>
				<td class="unselect" id="12" onclick="add(\'12\')">12</td>
				<td class="unselect" id="13" onclick="add(\'13\')">13</td>
				<td class="unselect" id="14" onclick="add(\'14\')">14</td>
				<td class="unselect" id="15" onclick="add(\'15\')">15</td>
			</tr>
			<tr>
				<td class="unselect" id="16" onclick="add(\'16\')">16</td>
				<td class="unselect" id="17" onclick="add(\'17\')">17</td>
				<td class="unselect" id="18" onclick="add(\'18\')">18</td>
				<td class="unselect" id="19" onclick="add(\'19\')">19</td>
				<td class="unselect" id="20" onclick="add(\'20\')">20</td>
			</tr>
			<tr>
				<td class="unselect" id="21" onclick="add(\'21\')">21</td>
				<td class="unselect" id="22" onclick="add(\'22\')">22</td>
				<td class="unselect" id="23" onclick="add(\'23\')">23</td>
				<td class="unselect" id="24" onclick="add(\'24\')">24</td>
				<td class="unselect" id="25" onclick="add(\'25\')">25</td>
			</tr>
			<tr>
				<td class="unselect" id="26" onclick="add(\'26\')">26</td>
				<td class="unselect" id="27" onclick="add(\'27\')">27</td>
				<td class="unselect" id="28" onclick="add(\'28\')">28</td>
				<td class="unselect" id="29" onclick="add(\'29\')">29</td>
				<td class="unselect" id="30" onclick="add(\'30\')">30</td>
			</tr>
		</table>

		Выбраные Вами номера : <input style="border: 0px solid #000; background:transparent;" type="text" readonly="true" id="value" name="value" />
		';

		return $str;
	}
}

$Lottery = new Lottery();

/*
if ($_GET['startlotery'] == 'start-lot-manual-pass-is-gameover') {
	$Lottery->get_win_combination();
}
*/

$get_test_lot = mysql_fetch_array(mysql_query("select * from oldbk.lottery where end=0 and date <= '".date('Y-m-d H:i:s')."' LIMIT 1;")); 
if ($get_test_lot[0])
	{
	$Lottery->get_win_combination();
	}



	$sql = "select * from oldbk.lottery where end=0 order by id DESC LIMIT 1;";

        $res = mysql_query($sql);

		while ($result = mysql_fetch_assoc($res)){
			$id_lottery = $result['id'];
			$date = $result['date'];
			$jackpot = $result['jackpot'];
			$fond = $result['fond'];
			$summ_5 = $result['summ_5'];
			$summ_4 = $result['summ_4'];
			$summ_3 = $result['summ_3'];
			$count_5 = $result['count_5'];
			$count_4 = $result['count_4'];
			$count_3 = $result['count_3'];
		}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title></title>
    <link rel="StyleSheet" href="newstyle_loc4.css" type="text/css">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	<script type="text/javascript" src="/i/globaljs.js"></script>
	<script type="text/javascript" src="/i/bank9.js"></script>	
		
		<script>	

			function getformdata(id,param,event)
			{

				if (window.event) 
				{
					event = window.event;
				}
				if (event ) 
				{

				       $.get('payform.php?id='+id+'&param='+param+'', function(data) {
					  $('#pl').html(data);
					  $('#pl').show(200, function() {
						});
					});
				
				 $('#pl').css({ position:'absolute',left: (($(window).width()-$('#pl').outerWidth())/2)+200, top: '200px'  });	


				}
				
			}

		function closeinfo()
		{
			$('#pl').hide(200);
		}			
		
	
</script>	
	
	<style>
		#page-wrapper #loto .tab-block {
			position: relative;
		}
		#page-wrapper #loto .tab-block .tab-header {
			background-color: #C7C7C7;
			padding: 4px;
			cursor: pointer;
			color: #003585;
		}
		#page-wrapper #loto .tab-block .tab-header.active, #page-wrapper #loto .tab-block .tab-header:hover {
			background-color: #A5A5A5;
		}
		#page-wrapper #loto .tab-block .tab-content {
			display: none;
		}
		.tab-header-block {

		}
		.spoiler-click {
			cursor: pointer;
		}
		#page-wrapper .spoiler-click:hover .spoiler-up {
			background-image: url("http://i.oldbk.com/i/images/buttons/btt3b.png");
		}
		#page-wrapper .spoiler-click:hover .spoiler-down {
			background-image: url("http://i.oldbk.com/i/images/buttons/btt3.png");
		}
	</style>
</head>
<body>
<div id="pl" style="z-index: 300; position: absolute; left: 50%; top: 120px;
				width: 750px; height:365px; background-color: #eeeeee;
				margin-left: -375px;
				border: 1px solid black; display: none;"></div>		
<div id="page-wrapper">
    <div id="buttons" class="clearfix">
        <a class="button-dark-mid btn" href="javascript:void(0);" title="Подсказка" onclick="window.open('help/lotery.html', 'help', 'height=300,width=500,location=no,menubar=no,status=no,toolbar=no,scrollbars=yes');">Подсказка</a>
        <a class="button-mid btn" href="javascript:void(0);" title="Обновить" onclick="location.href='lotery.php?refresh=<?echo mt_rand(1111,9999);?>';" >Обновить</a>
        <? //<a href='http://capitalcity.oldbk.com/event_lotery.php'>Результаты Пасхальной лотереи</a> <br><br><hr> Лотерея ОлдБк </td> ?>
        <a class="button-mid btn" href="javascript:void(0);" title="Вернуться" onclick="location.href='city.php?cp=1&tmp=<?echo mt_rand(1111,9999);?>';" >Вернуться</a>
    </div>
    <div id="loto">
        <table cellspacing="0" cellpadding="0">
            <colgroup>
                <col width="50%">
                <col width="50%">
            </colgroup>
            <tbody>
            <tr>
                <td>
                    <div id="top-block-left">
                        <div id="right-info">
                            <div class="next">Следующий тираж <strong>№ <?=$id_lottery?></strong></div>
                            <div class="next-info">
                                <ul>
                                    <li>
                                        Состоится: <div class="date"><?=$date?></div>
                                    </li>
                                    <li>
                                        Призовой фонд: <strong><?=$fond?> кр.</strong>
                                    </li>
                                    <li>
                                        Джекпот: <strong><?=round($jackpot,2)?> кр.</strong>
                                    </li>
                                    <li>
                                        Стоимость лотерейного билета: <strong>1.00 кр.</strong>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div id="choose-numbers">
                            <table cellspacing="0" cellpadding="0">
                                <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>2</td>
                                    <td>3</td>
                                    <td>4</td>
                                    <td>5</td>
                                </tr>
                                <tr>
                                    <td>6</td>
                                    <td>7</td>
                                    <td>8</td>
                                    <td>9</td>
                                    <td>10</td>
                                </tr>
                                <tr>
                                    <td>11</td>
                                    <td>12</td>
                                    <td>13</td>
                                    <td>14</td>
                                    <td>15</td>
                                </tr>
                                <tr>
                                    <td>16</td>
                                    <td>17</td>
                                    <td>18</td>
                                    <td>19</td>
                                    <td>20</td>
                                </tr>
                                <tr>
                                    <td>21</td>
                                    <td>22</td>
                                    <td>23</td>
                                    <td>24</td>
                                    <td>25</td>
                                </tr>
                                <tr>
                                    <td>26</td>
                                    <td>27</td>
                                    <td>28</td>
                                    <td>29</td>
                                    <td>30</td>
                                </tr>
                                </tbody>
                            </table>
                            <div class="buy">
                            <form method=POST name=mknew>
				<input  type="hidden" readonly="true" id="value" name="value" />                            
                            </form>
                                <a href="javascript:void(0);">КУПИТЬ БИЛЕТ</a>
                            </div>
                        </div>
                                  <?
					echo $Lottery->view_results($_POST['tiraj']);

				?>
                    </div>
		<?
		if ($_POST['value']) 
			{
				echo $Lottery->buy_ticket($_POST['value']);
			}
		        else   if($_GET['check']) 
                                    	{
                                    	echo '<div class="head-title p">';
 					echo $Lottery->check($_POST['tiraj']);
 					echo '</div>';
					}
		?>
                </td>
                <td>
                <?
		        $get_lot=mysql_fetch_array(mysql_query("select * from oldbk.item_loto_ras where status=1 LIMIT 1;"));
			if ($get_lot[id] >0)
			{
		?>
                    <div id="top-block-right" style="top:20px;">
                        <div id="next-info-right">
                            <ul>
                              <?
				echo "<li>Следующий тираж <strong>№ $get_lot[id] </strong> </li>
				<li>Cостоится <div class=date>".date("d-m-Y H:i",$get_lot[lotodate])."</div> </li>";

				if (!($loto_bil_counts = $memcache->get("loto_bil_counts"))) 
				{
						$q = mysql_query("Select count(id) as kol from oldbk.item_loto where  loto={$get_lot[id]}");
						$loto_bil_counts = GetMCacheFromQuery($q);
						$memcache->set("loto_bil_counts",$loto_bil_counts,0,10);
				}
			
				$get_count=$loto_bil_counts[0][kol];
					
					if ($get_count>=100)
						{
						$get_count=round($get_count*1.3);
						}

				echo "<li>Проданых билетов:<strong>".$get_count."</strong></li>";
                                
                                ?>
                            </ul>
                        </div>
                        <div id="info-right">
                            <div class="hint-block">
     <?/*                   		Купить билеты Вы можете у любого <img src="http://i.oldbk.com/i/deal.gif"><a href="javascript:void(0);" onClick="location.href='/friends.php?pals=3';"><strong>Дилера</strong></a> или в <img src='http://i.oldbk.com/i/city/sub/bank_png.png' height=28px ><a href="javascript:void(0);" onClick="location.href='/bank.php?p=1&bytik=1';"><strong>Банке</strong>  */ ?>
                          		<?
                          		/*
					if ($_SESSION['bankid']>0)
					{
					echo ' <a onclick="getformdata(10,33333,event);" href="#"><img src="http://i.oldbk.com/i/bank/knopka_loto.gif"  alt="Купить еврокредиты через Банк" alt="Купить еврокредиты через Банк"></a>';
					}
					else
					{
					echo ' <a href="bank.php"><img src="http://i.oldbk.com/i/bank/knopka_loto.gif"  alt="Купить еврокредиты через Банк" alt="Купить еврокредиты через Банк"></a>';
					}
					*/
                          		?>
                          		</a>
                            </div>
							<?php $Loto = new \components\Component\Loto\LotoView($get_lot['id']); ?>
							<table class="table" style="margin-top: 10px" cellspacing="0" cellpadding="0">
								<thead>
								<tr class="head-line spoiler-click">
									<th>
										<div class="head-left"></div>
										<div class="head-title">
											<?
											$old=$get_lot[id]-1;
											$get_lot_old=mysql_fetch_array(mysql_query("select * from oldbk.item_loto_ras where id={$old} LIMIT 1;"));
											?>
											Топ-100 победителей розыгрыша <strong>№ <?= $get_lot_old['id'] ?></strong> от <div class="date"><?= date("d-m-Y H:i",$get_lot_old['lotodate']) ?></div>
										</div>
										<div class="head-right"></div>
										<a class="spoiler right spoiler-down" href="javascript:void(0);"></a>
									</th>
								</tr>
								</thead>
								<tbody>
								<tr class="even hidden">
									<td>
										<table>
											<colgroup>
												<col width="30px">
												<col>
												<col width="80px">
												<col width="150px">
											</colgroup>
											<?php foreach ($Loto->getLastWin() as $key => $item): ?>
												<tr>
													<td><?= $key + 1; ?>.</td>
													<td nowrap=""><?= $item['user']->fullHtmlLogin(); ?></td>
													<td>№<?= $item['ticket_id'] ?></td>
													<td><?= $item['item_name'] ?></td>
												</tr>
											<?php endforeach; ?>
										</table>
									</td>
								</tr>
								</tbody>
							</table>
							
							<table class="table" style="margin-top: 10px" cellspacing="0" cellpadding="0">
                                <thead>
                                <tr class="head-line spoiler-click">
                                    <th>
                                        <div class="head-left"></div>
                                        <div class="head-title">
                                            Розыгрываются в тираже <strong>№ <?=$get_lot[id];?></strong>
                                        </div>
                                        <div class="head-right"></div>
                                        <a class="spoiler right spoiler-up" href="javascript:void(0);"></a>
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                  <tr class="even a_strong">
                                    <td>
										<div class="tab-block">
											<div class="tab-header-block">
												<table border="0" cellpadding="0" cellspacing="0">
													<thead>
													<tr>
														<?php $cat_count = 0; foreach (\components\Helper\CategoryHelper::getLabels() as $category_id => $label): ?>
															<th class="tab-header<?= $category_id == \components\Helper\CategoryHelper::CATEGORY_ITEM ? ' active' : ''?>" data-tab="loto-<?= $category_id ?>">
																<?= $label ?>
															</th>
															<?php $cat_count++; endforeach; ?>
													</tr>
													</thead>
													<tbody>
													<tr>
														<td bgcolor="#A5A5A5" style="height: 10px;" colspan="<?= $cat_count ?>"></td>
													</tr>
													</tbody>
												</table>
											</div>
											<div class="tab-content-block" style="max-height: 500px;overflow: hidden;overflow-y: scroll;">
												<?php foreach ($Loto->getViewList() as $category_id => $_prototypes_): ?>
													<TABLE class="tab-content" data-tab-content="loto-<?= $category_id ?>" BORDER=0 WIDTH=100% CELLSPACING="1" CELLPADDING="2" BGCOLOR="#A5A5A5">
														<?php foreach ($_prototypes_ as $key => $_prototype_): ?>
															<tr data-item-id="<?= $_prototype_['item_id'] ?>" bgcolor="<?= $key%2 ? '#D5D5D5' : '#C7C7C7' ?>">
																<td align=center style="width:130px">
																	<IMG SRC="http://i.oldbk.com/i/sh/<?= !empty($_prototype_['img_big']) ? $_prototype_['img_big'] : $_prototype_['img'] ?>" BORDER=0>
																</td>
																<td valign="top">
																	<?= showitem ($_prototype_); ?>
																</td>
															</tr>
														<?php endforeach; ?>
													</table>
												<?php endforeach; ?>
											</div>
										</div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?
                }
                ?>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
<script>
    var numbers = {0: null, 1: null, 2: null, 3: null, 4: null};
    $(function(){
		$.each($('.tab-header.active'), function(i, el) {
			$('[data-tab-content="'+$(el).data('tab')+'"]').show();
		});

		$(document.body).on('click', '.tab-header', function(){
			var $self = $(this);
			var wrapper = $self.closest('.tab-block');
			wrapper.find('.tab-header').removeClass('active');
			wrapper.find('.tab-content').hide();

			$self.addClass('active');

			$('[data-tab-content="'+$self.data('tab')+'"]').show();
			wrapper.find('.tab-content-block').animate({'scrollTop':0});
		});

        $(document.body).on('touchstart click', '#show-stats a', function(){
            $('#stats-table').toggle();
        });
        $(document.body).on('touchstart click', '#choose-numbers table td', function(){
            var $self = $(this);
            var number = parseInt($self.text());
            var key = in_array(numbers, number);
            if($self.hasClass('active') && key !== false) {
                $self.removeClass('active');
                numbers[key] = null;
            } else {
                if(insertNumber(number) === false) {
                    remove(numbers[0]);
                    numbers[0] = number;
                }

                $self.addClass('active');
            }
        });
        $(document.body).on('touchstart click', '.buy a', function(){
            var string = '';
            var k=0;
            $.each(numbers, function(i, num){
            	if (num)
            		{
	                string += num+','
			k++;
	                }
	                else
	                {
	                alert('Выберите 5 номеров');
	                return (num !==null);
	                }
            });
            if (k==5)
            	{
	            document.getElementById('value').value = string;
		        if (confirm("Купить билет с номерами:"+string))
	        	   {
		            document.mknew.submit();
		            }
        	}
        	else
        	{
        	return false;
        	}
        });
        $(document.body).on('touchstart click', '.spoiler-click', function(){
            var $self = $(this);
			var $spoiler = $self.find('.spoiler');

            var $table = $spoiler.closest('table');
            var $td = $table.find('tr td');
            $td.slideToggle('fast');

            if($spoiler.hasClass('spoiler-down')) {
				$spoiler.removeClass('spoiler-down').addClass('spoiler-up');
            } else {
				$spoiler.removeClass('spoiler-up').addClass('spoiler-down');
            }
        });
    });
    function remove(number) {
        $("#choose-numbers table td.active").filter(function(index, element){
            return parseInt($(element).text()) == number;
        }).removeClass('active');
    }
    function in_array(array, value) {
        var flag = false;
        for (var i = 0; i < 5; i++){ if(array[i] == value){flag = i;break;}}
        return flag;
    }
    function insertNumber(value) {
        var flag = false;
        for (var i = 0; i < 5; i++){if(numbers[i] === null){numbers[i] = value;flag = true;break;}}
        return flag;
    }
</script>
</body>
</html>
<?
/*
<input type="button" value="Купить лотерейный билет" onclick="document.getElementById('adde').style.visibility='visible';document.getElementById('adde').style.display='block';">
<div style="display:none;visivility:hidden;" id="adde">
<h4>Выберите 5 номеров</h4>
<form method='post' style="margin:0px;">
<? echo $Lottery->view_buy_ticket(); ?>
<BR><input type=submit OnClick="return CheckFive();" value='Купить билет'></form></div>
<BR>
<input type="button" value="Проверить лотерейные билеты" onclick="location.href='lotery.php?check=1';">
<BR>
*/
?>

<?


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