<?
#Description: Admin Page - User Maintenance (Add User)
#Author: Jhae Torres
#Date Created: January 19, 2009


session_start();
$company_code=$_SESSION['comp_code'];

require_once "../../includes/config.php";
require_once "../../functions/db_function.php";
require_once "admin.obj.php";
require_once "../etc/etc.obj.php";

$gmt = time() + (8 * 60 * 60);
$date = date("m-d-Y", $gmt);
$datetime = date("m-d-Y H:i:s", $gmt);

$db = new DB;
$db->connect();

$etcTrans = new etcObject;
$adminTrans = new adminObject;

$userid = $adminTrans->getUserID();

if($_GET['msg']) $msg=$etcTrans->getMessage($_GET['msg']);

$company = $adminTrans->getCompany();
($_GET['company_code']) ? $selected_company=$_GET['company_code'] : $selected_company='1000';
$company_list = $db->selectOption2D($company, 'company', 'company', 'class="admin_combo" tabIndex="4" ', '', $selected_company, 'compCode', 'compName', 'compCode');

$privilege = $adminTrans->getPrivilege();
($_GET['privilege']) ? $selected_privilege=$_GET['privilege'] : $selected_privilege='9';
$privilege_list = $db->selectOption2D($privilege, 'privilege', 'privilege', 'class="admin_combo" tabIndex="5" ', '', $selected_privilege, 'privilegeId', 'privilegeDesc', 'privilegeId');

/*
$user_info = $adminTrans->getUserInfo($company_code, $userid, $username, $privilege_id,
							$first_name, $middle_name, $last_name,
							$active_online, $date_registered, $last_login, $last_logout,
							$status, $date_status_updated, $status_updated_by);
*/
$user_info = $adminTrans->getUserInfo('', '', $_GET['username'], '',
							'', '', '',
							'', '', '', '',
							'', '', '');
							
$cnt = 1;
foreach($user_info as $user){
	(!empty($user['lastLogIn'])) ? $lastLogin=$etcTrans->formatDateTime($user['lastLogIn']) : $lastLogin='&nbsp';
	(!empty($user['lastLogOut'])) ? $lastLogout=$etcTrans->formatDateTime($user['lastLogOut']) : $lastLogout='&nbsp';
	(!empty($user['dateStatusUpdated'])) ? $lastUpdate=$etcTrans->formatDateTime($user['dateStatusUpdated']) : $lastUpdate='&nbsp';
	(!empty($user['statusUpdatedBy'])) ? $updatedBy=$etcTrans->formatDateTime($user['statusUpdatedBy']) : $updatedBy='&nbsp';

	($cnt % 2 == 0) ? $row_color='#DCDBDD' : $row_color='#EFEBED';
	($user['activeOnline'] == '1') ? $online_color='#5F762E' : $online_color='#994354';
	if($user['status']=='A' or $user['status']=='a'){
		$stat_color='#5F762E';
	} elseif($user['status']=='D' or $user['status']=='d'){
		$stat_color='#994354';
	} elseif($user['status']=='X' or $user['status']=='x'){
		$stat_color='#FF0A37';
	} else{
		$stat_color='#C8C4C6';
	}
	
	$user_list .= "<tr bgcolor='".$row_color."'
						onMouseOver='this.style.backgroundColor=\"#81A987\"'
						onMouseOut='this.style.backgroundColor=\"".$row_color."\"'>
					<td width='30px'>".$cnt."</td>
					<td width='50px'>".$user['compCode']."</td>
					<td width='40px'>".$user['userid']."</td>
					<td width='50px'>".$user['username']."</td>
					<td width='40px'>".$user['privilegeId']."</td>
					<td width='160px'>".$user['firstName']." ".$user['middleName']." ".$user['lastName']."</td>
					<td width='20px' bgcolor='".$online_color."'>".$user['activeOnline']."</td>
					<td width='120px'>".$etcTrans->formatDateTime($user['dateRegistered'])."</td>
					<td width='120px'>".$lastLogin."</td>
					<td width='120px'>".$lastLogout."</td>
					<td width='30px' bgcolor='".$stat_color."'>".$user['status']."</td>
					<td width='120px'>".$etcTrans->formatDateTime($user['dateStatusUpdated'])."</td>
					<td>".$user['statusUpdatedBy']."</td>
				</tr>";
	$cnt++;
}
?>


<link rel='stylesheet' type='text/css' href='../../includes/style.css'></link>
<script type='text/javascript' src='../../functions/javascript_function.js'></script>

<body onLoad="setFocus('first_name');">
<!-- message area -->
<div id='msg'>
  <?=$msg?>
</div>

<div id='frame_body'>
<form name='add_user_form' id='add_user_form' action='admin.trans.php' method='POST'>
	<div class='admin_header'>
	  	<input type='hidden' name='transaction' id='transaction' />
	  	<input type='hidden' name='date_today' id='date_today' value='<?=$date?>' />
		<table>
			<tr>
				<th width='7%'>User ID:</th>
				<td width='10%'><input type='text' class='userid' name='userid' id='userid' value='<?=$userid?>' readOnly /></td>
				<th colspan='6'>&nbsp;</th>
				<th width='7%'>Company:</th>
				<td width='10%' colspan='3'><?=$company_list?></td>
			</tr>
			<tr>
				<th>First Name:</th>
				<td><input type='text' class='admin_txtbx' name='first_name' id='first_name' maxLength='25' tabIndex='1' value="<?=$_GET['first_name']?>" /></td>
				<th width='7%'>Middle Name:</th>
				<td width='10%'><input type='text' class='admin_txtbx' name='middle_name' id='middle_name' maxLength='25' tabIndex='2' value="<?=$_GET['middle_name']?>" /></td>
				<th width='7%'>Last Name:</th>
				<td width='10%'><input type='text' class='admin_txtbx' name='last_name' id='last_name' maxLength='25' tabIndex='3' value="<?=$_GET['last_name']?>" /></td>
				<th width='5%'>Count:</th>
				<td width='3%'><input type='text' class='count' name='count' id='count' maxLength='2' value="<?=$_GET['count']?>" /></td>
				<th>Privilege:</th>
				<td><?=$privilege_list?></td>
			</tr>
		</table>
		<input type='button' class='admin_button' name='add_user' id='add_user' value='ADD USER' tabIndex='6'
			onClick="assignTransaction(this.id); validateAddUser();" />
	</div>
	<div class='user_list_hdr'>
		<table>
			<tr>
				<th width='30px'>#</th>
				<th width='50px'>Comp</th>
				<th width='40px'>Userid</th>
				<th width='50px'>Username</th>
				<th width='40px'>PID</th>
				<th width='160px'>Full Name</th>
				<th width='20px'>On</th>
				<th width='120px'>Registration Date</th>
				<th width='120px'>Last Login</th>
				<th width='120px'>Last Logout</th>
				<th width='30px'>Stat</th>
				<th width='120px'>Last Update</th>
				<th width=''>UBy</th>
			</tr>
		</table>
	</div>
	<div class='user_list'>
		<table>
			<?=$user_list?>
		</table>
	</div>
</form>
</div>
</body>