<?
#Description: Etc. Module Transactions
#Author: Jhae Torres
#Date Created: December 11, 2008


session_start();
require_once "../../includes/config.php";
require_once "../../functions/db_function.php";
require_once "etc.obj.php";

$gmt = time() + (8 * 60 * 60);
$current_datetime = date("m-d-Y H:i:s", $gmt);

$db = new DB;
$db->connect();
$etcTrans = new etcObject;
$transaction = $_POST['transaction'];
$username = stripslashes(strip_tags(substr($_POST['username'], 0, 32)));
$password = stripslashes(strip_tags(substr($_POST['password'], 0, 32)));
#$db->disconnect();

switch($transaction){
	case 'no_input':
		$etcTrans->redirectURL("index.php");
		session_destroy();
		break;
		
	case 'invalid_login':
		$etcTrans->redirectURL("index.php");
		session_destroy();
		break;
	
	case 'check_login':
		$new_user = $etcTrans->checkIfUserExist($username, '');
		if(!empty($new_user) and ($new_user['status']=='R' or $new_user['status']=='r')){
			$user = $etcTrans->writePassword($username, $password);
			if(!empty($user)){
				$_SESSION['comp_code'] = $user['compCode'];
				$_SESSION['userid'] = $user['userid'];
				$_SESSION['username'] = $user['username'];
				$_SESSION['password'] = $user['password'];
				$_SESSION['privilege_id'] = $user['privilegeId'];
				$_SESSION['first_name'] = $user['firstName'];
				$_SESSION['middle_name'] = $user['middleName'];
				$_SESSION['last_name'] = $user['lastName'];
				$etcTrans->tagUserOnline($_SESSION['username'], '1', 'lastLogIn', $current_datetime);
				$etcTrans->redirectURL("index.php");
			} else{
				$msg = "LOG005";
				$etcTrans->redirectURL("index.php?msg=".$msg);
				session_destroy();
			}			
		} else{
			$user = $etcTrans->checkIfUserExist($username, $password);
			if(empty($user)){
				$msg = "LOG001";
				$etcTrans->redirectURL("index.php?msg=".$msg);
				session_destroy();
			} else{
				if($user['status']=='D' or $user['status']=='d'){
					$msg = "LOG002";
					$etcTrans->redirectURL("index.php?msg=".$msg);
					session_destroy();
				} elseif($user['status']=='X' or $user['status']=='x'){
					$msg = "LOG003";
					$etcTrans->redirectURL("index.php?msg=".$msg);
					session_destroy();
				} elseif($user['activeOnline']==1){
					$msg = "LOG004";
					$etcTrans->redirectURL("index.php?msg=".$msg);
					session_destroy();
				} else{				
					$_SESSION['comp_code'] = $user['compCode'];
					$_SESSION['userid'] = $user['userid'];
					$_SESSION['username'] = $user['username'];
					$_SESSION['password'] = $user['password'];
					$_SESSION['privilege_id'] = $user['privilegeId'];
					$_SESSION['first_name'] = $user['firstName'];
					$_SESSION['middle_name'] = $user['middleName'];
					$_SESSION['last_name'] = $user['lastName'];
					$etcTrans->tagUserOnline($_SESSION['username'], '1', 'lastLogIn', $current_datetime);
					$etcTrans->redirectURL("index.php");
				}
			}
		}
		break;
		
	case 'logout':
		$username = $_POST['session_username'];
		$etcTrans->tagUserOnline($username, '0', 'lastLogOut', $current_datetime);
		$etcTrans->redirectURL("index.php");
		session_destroy();
		break;
}
?>