<?
#Description: Homepage Module Transactions
#Author: Jhae Torres
#Date Created: March 10, 2008


session_start();
require_once "../../includes/config.php";
require_once "../../functions/db_function.php";
require_once "home.obj.php";
require_once "../etc/etc.obj.php";

$db = new DB;
$db->connect();
$homeTrans = new homeObject;
$etcTrans = new etcObject;
$transaction = $_POST['transaction'];
#$db->disconnect();

switch($transaction){
	case '':
		break;
		
	default:
		break;
}
?>