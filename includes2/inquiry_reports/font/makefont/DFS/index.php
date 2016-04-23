<?
#Description: Main Page
#Author: Jhae Torres
#Date Created: March 08, 2008


session_start();
require_once "includes/config.php";
require_once "functions/db_function.php";
require_once "modules/etc/etc.obj.php";

$sys_title = $config['sys_title'];
$db = new DB;
$etcTrans = new etcObject;
$error_id = $_GET['msg'];
$db->connect();
//$db->disconnect();

$html = "<html>";
$html .= "<head>";
$html .= "<title>".$sys_title."</title>";
$html .= "<link rel='stylesheet' type='text/css' href='includes/style.css'></link>";
$html .= "<script type='text/javascript' src='functions/javascript_function.js'></script>";
$html .= "</head>";


if( isset($_SESSION['username']) and isset($_SESSION['password']) ){
	#HOME PAGE
	require_once "modules/home/home.php";
} else{
	#LOGIN PAGE
	$html .= "<body onLoad='setFocus(\"username\");'>";
	$html .= "<div id='dfs'><img src='attachments/DFS.png'></img></div>";
	$html .= "<form name='login_form' action='modules/etc/etc.trans.php' method='POST'>";
	$html .= "<div id='login_backdrop'>";
	if($error_id){
		$error_msg = $etcTrans->getMessage($error_id);
		$html .= "<input type='text' name='login_error' id='login_error' class='login_error' value='ErrorID ".$error_msg."' readOnly />";
	} else{
		$html .= "<input type='text' name='login_error' id='login_error' class='login_error' value='' readOnly />";
	}
	$html .= "<table>";
	$html .= "<tr>
				<th>USERNAME:</th>
				<td><input type='text' class='textbox' name='username' id='username' maxlength='8' value='".$_SESSION['username']."' /></td>
			</tr>";
	$html .= "<tr>
				<th>PASSWORD:</th>
				<td><input type='password' class='textbox' name='password' id='password' maxlength='8' /></td>
			</tr>";
	$html .= "</table>";
	$html .= "<p><input type='submit' class='button' name='login' id='login' value='Log-in'
					onClick='checkInput();' /></p>";
	$html .= "<p><input type='hidden' name='transaction' id='transaction' /></p>";
	$html .= "</div>";
	$html .= "</form>";
	$html .= "</body>";
}

$html .= "</html>";
echo $html;
?>