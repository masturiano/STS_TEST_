<?
session_start();
require_once "../../includes/config.php";
require_once "../../functions/db_function.php";
require_once "../../modules/etc/etc.obj.php";
$db = new DB;
$db->connect();
$etcTrans = new etcObject;
##################################################################
$company_name = $etcTrans->getCompanyName($_SESSION['comp_code']);
$company_code = $_SESSION['comp_code'];
$user_id = $_SESSION['userid'];
$user_name = $_SESSION['username'];
$user_first = $_SESSION['first_name'];
$user_last = $_SESSION['last_name'];
$user_first_last = $user_first." ".$user_last;
if (trim($user_id) ==""){
	echo "<script type='text/javascript'>
		  alert('Please Login Again!');
		  window.location = \"../../index.php\";
		  </script>";
	exit(0);
} 
##################################################################

?>