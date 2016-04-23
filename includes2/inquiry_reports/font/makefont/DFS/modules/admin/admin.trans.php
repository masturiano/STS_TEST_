<?
#Description: Admin Module Transactions
#Author: Jhae Torres
#Date Created: January 19, 2009


session_start();
$sess_company = $_SESSION['comp_code'];
$operator = $_SESSION['userid'];

require_once "../../includes/config.php";
require_once "../../functions/db_function.php";
require_once "admin.obj.php";
require_once "../etc/etc.obj.php";

$db = new DB;
$db->connect();
$adminTrans = new adminObject;
$etcTrans = new etcObject;
$transaction = $_POST['transaction'];
$ajax_trans = $_GET['ajax_trans'];
#$db->disconnect();

$gmt = time() + (8 * 60 * 60);
$date = date("m-d-Y", $gmt);
$datetime = date("m-d-Y H:i:s", $gmt);

switch($transaction){
	case 'add_user':
		$company_code = $_POST['company'];
		$userid = $_POST['userid'];
		$privilege_id = $_POST['privilege'];
		$first_name = ucwords(strtolower($_POST['first_name']));
		$middle_name = ucwords(strtolower($_POST['middle_name']));
		$last_name = ucwords(strtolower($_POST['last_name']));
		$count = $_POST['count'];
		$date_registered = $datetime;
		$status = 'R';
		
		$find = array('-', ' ');
		$replace   = array('', '');
		$surname  = str_replace($find, $replace, $last_name);
		$username = strtolower(substr($first_name, 0, 1).substr($surname, 0, 5).$count);
		
		$user = $etcTrans->checkIfUserExist($username, '');
		if(!empty($user)){
			$msg = 'ADM001';
			$etcTrans->redirectURL("modules/admin/add_user.php?msg=".$msg."&company_code=".$company_code."&privilege=".$privilege_id."&first_name=".
							$first_name."&middle_name=".$middle_name."&last_name=".$last_name."&count=".$count."&username=".$username);
		} else{
			$user_info = $adminTrans->addUser($company_code, $userid, $privilege_id, $first_name, $middle_name, $last_name, $username, $date_registered, $status);
			(!empty($user_info)) ? $msg='ADM002' : $msg='ADM003';
			$etcTrans->redirectURL("modules/admin/add_user.php?msg=".$msg."&username=".$username);
		}
		break;
		
	case 'search_delete_user':
		$company_code = $_POST['company'];
		$first_name = $_POST['first_name'];
		$middle_name = $_POST['middle_name'];
		$last_name = $_POST['last_name'];
		$username = $_POST['username'];
		$etcTrans->redirectURL("modules/admin/delete_user.php?company_code=".$company_code."&first_name=".$first_name."&middle_name=".$middle_name."&last_name=".$last_name."&username=".$username);
		break;
		
	case 'delete_user':
		if($_POST['select_user']){
			foreach($_POST['select_user'] as $username){
				$adminTrans->deleteUser($username, $operator);
			}
			$msg = 'ADM005';
		} else{
			$msg = 'ADM004';
		}
		$etcTrans->redirectURL("modules/admin/delete_user.php?msg=".$msg);
		break;
		
	case 'search_logout_user':
		$company_code = $_POST['company'];
		$first_name = $_POST['first_name'];
		$middle_name = $_POST['middle_name'];
		$last_name = $_POST['last_name'];
		$username = $_POST['username'];
		$etcTrans->redirectURL("modules/admin/logout_user.php?company_code=".$company_code."&first_name=".$first_name."&middle_name=".$middle_name."&last_name=".$last_name."&username=".$username);
		break;
		
	case 'logout_user':
		if($_POST['select_user']){
			foreach($_POST['select_user'] as $username){
				$adminTrans->logoutUser($username);
			}
			$msg = 'ADM007';
		} else{
			$msg = 'ADM006';
		}
		$etcTrans->redirectURL("modules/admin/logout_user.php?msg=".$msg);
		break;
		
	case 'search_reset_password':
		$company_code = $_POST['company'];
		$first_name = $_POST['first_name'];
		$middle_name = $_POST['middle_name'];
		$last_name = $_POST['last_name'];
		$username = $_POST['username'];
		$etcTrans->redirectURL("modules/admin/reset_password.php?company_code=".$company_code."&first_name=".$first_name."&middle_name=".$middle_name."&last_name=".$last_name."&username=".$username);
		break;
		
	case 'reset_password':
		if($_POST['select_user']){
			foreach($_POST['select_user'] as $username){
				$adminTrans->resetPassword($username, $operator);
			}
			$msg = 'ADM009';
		} else{
			$msg = 'ADM008';
		}
		$etcTrans->redirectURL("modules/admin/reset_password.php?msg=".$msg);
		break;
		
	case 'search_activate_user':
		$company_code = $_POST['company'];
		$first_name = $_POST['first_name'];
		$middle_name = $_POST['middle_name'];
		$last_name = $_POST['last_name'];
		$username = $_POST['username'];
		$etcTrans->redirectURL("modules/admin/activate_user.php?company_code=".$company_code."&first_name=".$first_name."&middle_name=".$middle_name."&last_name=".$last_name."&username=".$username);
		break;
		
	case 'activate_user':
		if($_POST['select_user']){
			foreach($_POST['select_user'] as $username){
				$adminTrans->activateUser($username, $operator);
			}
			$msg = 'ADM011';
		} else{
			$msg = 'ADM010';
		}
		$etcTrans->redirectURL("modules/admin/activate_user.php?msg=".$msg);
		break;
		
	case 'search_deactivate_user':
		$company_code = $_POST['company'];
		$first_name = $_POST['first_name'];
		$middle_name = $_POST['middle_name'];
		$last_name = $_POST['last_name'];
		$username = $_POST['username'];
		$etcTrans->redirectURL("modules/admin/deactivate_user.php?company_code=".$company_code."&first_name=".$first_name."&middle_name=".$middle_name."&last_name=".$last_name."&username=".$username);
		break;
		
	case 'deactivate_user':
		if($_POST['select_user']){
			foreach($_POST['select_user'] as $username){
				$adminTrans->deactivateUser($username, $operator);
			}
			$msg = 'ADM013';
		} else{
			$msg = 'ADM012';
		}
		$etcTrans->redirectURL("modules/admin/deactivate_user.php?msg=".$msg);
		break;
}

?>