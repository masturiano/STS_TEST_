<?
#Description: Sales Module Transactions
#Author:
#Date Created: 


require_once "includes/config.php";
require_once "functions/db_function.php";
require_once "sales.obj.php";

$db = new DB;
$db->connect();
$salesTrans = new salesObject;
#$transaction = $_POST['transaction'];
#$db->disconnect();

switch($transaction){
	case '':
		break;
		
	default:
		break;
}
?>