#!/usr/bin/php
<?php
include "/www/capitalcity.oldbk.com/cron/init.php";
if( !lockCreate("cron_battle_job") ) {
    exit("Script already running.");
}

	// убиваем ботов/клонов из таблицы
$query = mysql_query("SELECT b.id, count(u.id) AS count
                      FROM `bots` AS b, `users` AS u
                      WHERE  b.`hp` >= 0 AND u.battle = b.battle
                      GROUP by b.battle");
while($row = mysql_fetch_array($query)) {
    if($row['count'] == 0) {
        mysql_query("DELETE FROM bots WHERE id = " . $bot['id']);
    }
}

$battleIds = array();
$query = mysql_query("SELECT DISTINCT battle FROM `battle_vars`");
while($row = mysql_fetch_array($query)) {
    $battleIds[] = $row['battle'];
}
if(!empty($battleIds)) {
    $query = mysql_query("SELECT count(*) AS count, battle FROM users WHERE battle IN (" . implode(", ", $battleIds) . ") AND battle > 0 GROUP by battle");
    while($row = mysql_fetch_assoc($query)) {
        if($row['count'] == 0) {
	        mysql_query("DELETE FROM battle_vars WHERE battle='".$row['battle']."'");	
		}
    }
}
unset($battleIds);  
  
// START Global fights. TeNi, 02.05.2010
$battles = array();
$battleIds = array();
$query = mysql_query("SELECT t1, timeout, id FROM `battle` WHERE type!=61 and type!=60 and status_flag > 1");
while($row = mysql_fetch_array($query)) {
    $battles[$row['id']] = $row;
	$battleIds[] = $row['id'];
}
if(!empty($battleIds)) {
    $query = mysql_query("SELECT battle, update_time, owner FROM `battle_vars` WHERE `battle` IN (" . implode(",", $battleIds) . ")");
    while($row = mysql_fetch_array($query)) {
        if(!isset($battles[$row['battle']]['vars'])) {
            $battles[$row['battle']]['vars'] = array();
        }
        $battles[$row['battle']]['vars'][] = $row;
    }
}
unset($battleIds);

foreach($battles as $gf) {
    echo "We are in battle...\n";
    // Set timeout 2 mins
    if($gf['timeout'] != 3) { 
	    mysql_query("UPDATE battle SET timeout='3' WHERE type!=60 and type!=61 and id='".$gf['id']."'"); 
		$gf['timeout'] = 3; 
	}
    // Get out 20%hp if no action of fighting person for 2 minutes
    /* foreach((array)$gf['vars'] as $bv) {
        echo "Checking for ".$bv['owner']."\n";
        // While for fighting people
        $current_time = time(); // Current timestamp
        $need_diff = round($gf['timeout']*60);
        // GET hp
        $ud = mysql_fetch_array(mysql_query("SELECT sex,hp,maxhp,battle FROM users WHERE id='".$bv['owner']."'"));
        if($ud['hp']>0 && $ud['battle'] == $gf['id']) {
       // If he alive
       // First check if user have alive clones
       $clonsq = mysql_query("SELECT id FROM users_clons WHERE id_user='".$bv['owner']."'");
       $clons_hp = 0;  // Summ of clons current HP. No matter at whitch team clon is.
       while($clons = mysql_fetch_array($clonsq)) {
          $cd = mysql_fetch_array(mysql_query("SELECT hp FROM bots WHERE prototype='".$clons['id']."'"));
          if($cd['hp']>0) {$clons_hp=$clons_hp+$cd['hp'];}
       }
       if($clons_hp>0) { continue; }
       // Check if he dont make any move in {timeout} mins.
       if( ($current_time-$bv['update_time']) >= $need_diff ) {
         // Get out 20% HP.
         $rand = mt_rand(0,5);
         $percent_array1 = array('0'=>'0.1','1'=>'0.12','2'=>'0.14','3'=>'0.16','4'=>'0.18','5'=>'0.20');
         $percent_array2 = array('0'=>'10%','1'=>'12%','2'=>'14%','3'=>'16%','4'=>'18%','5'=>'20%');
         $twenty_percent = round($ud['maxhp']*$percent_array1[$rand]);
         $new_hp = round($ud['hp']-$twenty_percent); if($new_hp<0) {$new_hp=0;}
         mysql_query("UPDATE users SET hp=hp-".$twenty_percent." WHERE id='".$bv['owner']."'");
         // Add log
         if($ud['sex'] == 1) { $sexa = ''; } else { $sexa = 'а'; }
         $t1 = explode(";",$gf['t1']);
         if(in_array($bv['owner'],$t1)) {$myclass = 'B1';}else{$myclass='B2';}
         if($gf['timeout'] == 1) {$min = "одной минуты";}
         elseif($gf['timeout'] == 2) {$min = "двух минут";}
         elseif($gf['timeout'] == 3) {$min = "трех минут";}
         elseif($gf['timeout'] == 4) {$min = "четырех минут";}
         elseif($gf['timeout'] == 5) {$min = "пяти минут";}
         elseif($gf['timeout'] == 6) {$min = "шести минут";}
         elseif($gf['timeout'] == 7) {$min = "семи минут";}
         elseif($gf['timeout'] == 8) {$min = "восьми минут";}
         elseif($gf['timeout'] == 9) {$min = "девяти минут";}
         elseif($gf['timeout'] == 10) {$min = "десяти минут";}
         else {$min = $gf['timeout']." минут";}
		 $hidden = mysql_fetch_array(mysql_query("SELECT * FROM `effects` WHERE owner='{$bv['owner']}' and type='200' LIMIT 1;"));
		 if($hidden[0]) {$new_hp = '??'; $ud['maxhp'] = '??';}
         addlog($gf['id'],"<span class=date>".date("H.i")."</span> ".nick5($bv['owner'],$myclass)." почувствовал{$sexa} слабость от бездействия в течении {$min}... <b>-{$twenty_percent}</b> ({$percent_array2[$rand]}) [{$new_hp}/{$ud['maxhp']}]<BR>");
         // Update last "move" time.
         mysql_query("UPDATE battle_vars SET update_time='".$current_time."' WHERE battle='".$gf['id']."' and owner='".$bv['owner']."'");
       } else {
         $diff = ($current_time-$bv['update_time']);
         echo "No passed needed time: {$current_time} / {$bv['update_time']}\n"."Need more: ".$diff."\n";
       }
     }

 } */
}

lockDestroy("cron_battle_job");
?>