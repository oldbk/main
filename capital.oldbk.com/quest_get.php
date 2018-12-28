<?
//проверяет ответы на квесты
	session_start();
	if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }
	include "connect.php";
	include "functions.php";


  if((int)$_GET[q] && (int)$_GET[s])
  {
     echo "<br><table cellpadding=3 cellspasing=3 style=\"background: url('http://capitalcity.oldbk.com/i/quest/fp_2.jpg') repeat-y;\">
			<tr>
		      <td>&nbsp;&nbsp;&nbsp;&nbsp;";
     use_quest_dialogs($_GET[q], $_GET[s]);
     echo "</td>
     	<td>&nbsp;</td>
		     </tr>
		    </table>
           <img src=\"http://capitalcity.oldbk.com/i/quest/fp_3.png\">";
     //перенести в функцию диалога
  }



?>