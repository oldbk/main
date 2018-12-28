<?
session_start();
if (!($_SESSION['uid'] >0))
{
   header("Location: http://oldbk.com?noenter");
    die;
}
include "connect.php";
include "functions.php";

$key='aYHlGLIxCG1lI1aryrjNzI1YVHLbyMTI';

echo '<html><body><form id="bookauth" method=POST action="http://b.oldbk.com/api/auth">';

echo "<input type=hidden name=uid value='{$user[id]}'>";
$hash=md5($user[id]."/".$user['sid']."/".$key);
echo "<input type=hidden name=hash value='{$hash}'>";

echo "</form>";
echo '<script>document.getElementById("bookauth").submit();</script>';
echo '</body></html>';
?>