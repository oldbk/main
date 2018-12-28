<?
session_start();
if (!($_SESSION['uid'] >0))
{
   header("Location: https://blog.oldbk.com/");
    die;
}
include "connect.php";
include "functions.php";

$key='7XttXsFvpOmUQebCbgMGOpUXG0QI';

echo '<html><body><form id="blogauth" method=POST action="https://blog.oldbk.com/api/auth">';
//echo "USER:{$user[login]} <br>";
echo "<input type=hidden name=uid value='{$user[id]}'>";
$hash=md5($user[id]."/".$user['sid']."/".$key);
echo "<input type=hidden name=hash value='{$hash}'>";
//echo "<input type=submit value='Войти в блоги'>";
echo "</form>";
echo '<script>document.getElementById("blogauth").submit();</script>';
echo '</body></html>';
?>
