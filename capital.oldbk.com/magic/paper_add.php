
<?php
if (!($_SESSION['uid'] >0)) header("Location: index.php");

$rowm = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`inventory` WHERE `owner` = '{$_SESSION['uid']}' AND `id` = '{$_GET['use']}' ;"));

if($rowm['magic'] >= 100 && $rowm['magic']<=102 && (strlen($rowm['letter']))>0)
{
	echo '<font color = red><b>Эта бумага уже использованная.</b></font>';
}
elseif
($rowm['magic'] >= 100 && $rowm['magic']<=102 && $rown['letter']=='')
{
	$len=strlen($_POST['target']);
	if($len>$num_w)
	{
		$delta=$num_w-$len;

		$_POST['target']=substr($_POST['target'],0,$delta);
	}


	$text1=$_POST['target'];
	$text1 = preg_replace("~&amp;~i","&",$text1);
	$text1 = preg_replace("~&lt;B&gt;~i","<B>",$text1);
	$text1 = preg_replace("~&lt;/B&gt;~i","</B>",$text1);
	$text1 = preg_replace("~&lt;U&gt;~i","<U>",$text1);
	$text1 = preg_replace("~&lt;/U&gt;~i","</U>",$text1);
	$text1 = preg_replace("~&lt;I&gt;~i","<I>",$text1);
	$text1 = preg_replace("~&lt;/I&gt;~i","</I>",$text1);
	$text1 = preg_replace("~&lt;CODE&gt;~i","<CODE>",$text1);
	$text1 = preg_replace("~&lt;/CODE&gt;~i","</CODE>",$text1);
	$text1 = preg_replace("~&lt;b&gt;~i","<b>",$text1);
	$text1 = preg_replace("~&lt;/b&gt;~i","</b>",$text1);
	$text1 = preg_replace("~&lt;u&gt;~i","<u>",$text1);
	$text1 = preg_replace("~&lt;/u&gt;~i","</u>",$text1);
	$text1 = preg_replace("~&lt;i&gt;~i","<i>",$text1);
	$text1 = preg_replace("~&lt;/i&gt;~i","</i>",$text1);
	$text1 = preg_replace("~&lt;code&gt;~i","<code>",$text1);
	$text1 = preg_replace("~&lt;/code&gt;~i","</code>",$text1);
	$text1 = preg_replace("~&lt;br&gt;~i","<br>",$text1);
	$text1 = makeClickableLinks($text1);
	###############
# Fix 2FED , 22.04.2010, closing unclosed tags.
# Tag B
      preg_match_all("/(\<b\>)/i", $text1, $unclosed11); preg_match_all("/(\<\/b\>)/i", $text1, $unclosed12);
      $unclosed_count11 = count($unclosed11[1]);         $unclosed_count12 = count($unclosed12[1]);
      $diff1 = $unclosed_count11-$unclosed_count12;
      if($diff1 > 0) { for($i = 0; $i < $diff1; $i++) {$text1 = $text1."</B>";}}
      # Tag I
      preg_match_all("/(\<i\>)/i", $text1, $unclosed21); preg_match_all("/(\<\/i\>)/i", $text1, $unclosed22);
      $unclosed_count21 = count($unclosed21[1]);         $unclosed_count22 = count($unclosed22[1]);
      $diff1 = $unclosed_count21-$unclosed_count22;
      if($diff1 > 0) { for($i = 0; $i < $diff1; $i++) {$text1 = $text1."</I>";}}
      # Tag U
      preg_match_all("/(\<u\>)/i", $text1, $unclosed31); preg_match_all("/(\<\/u\>)/i", $text1, $unclosed32);
      $unclosed_count31 = count($unclosed31[1]);         $unclosed_count32 = count($unclosed32[1]);
      $diff1 = $unclosed_count31-$unclosed_count32;
      if($diff1 > 0) { for($i = 0; $i < $diff1; $i++) {$text1 = $text1."</U>";}}
      # Tag CODE
      preg_match_all("/(\<code\>)/i", $text1, $unclosed41); preg_match_all("/(\<\/code\>)/i", $text1, $unclosed42);
      $unclosed_count41 = count($unclosed41[1]);         $unclosed_count42 = count($unclosed42[1]);
      $diff1 = $unclosed_count41-$unclosed_count42;
      if($diff1 > 0) { for($i = 0; $i < $diff1; $i++) {$text1 = $text1."</CODE>";}}


	if(mysql_query('UPDATE oldbk.inventory SET duration=1, letter="'.strip_tags(nl2br($text1),"<b><i><u><code><BR><a>").'" WHERE id = '.$rowm['id'].' AND owner = '.$_SESSION['uid'].';'))
	{
		echo '<font color = red><b>Надпись нанесена на бумагу.</b></font>';
	}
}


?>

