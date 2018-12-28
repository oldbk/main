<?
$run = 'fights.php';
$ps = exec("ps auxf | grep $run | grep -v grep");
if(strlen($ps)>10) {
  echo "Script $run is running now...\n$ps\n";
} else {
  echo "Not running\n";
  exec("/usr/local/bin/php /www/capitalcity.oldbk.com/$run &");
  echo "Runned?\n";	
}
?>
