<?
#Description: Admin Module Object
#Author: Jhae Torres
#Date Created: January 19, 2009


class adminObject
{
	function getCompany(){
		global $db;
		$query = "SELECT compCode, compName
				FROM tblCompany
				WHERE compStat IN('', 'A')
				ORDER BY compName";
		$db->query($query);
		$company = $db->getArrResult();
		return $company;
	}
	
	function getPrivilege(){
		global $db;
		$query = "SELECT privilegeId, privilegeDesc
				FROM tblPrivilege
				ORDER BY privilegeId";
		$db->query($query);
		$privilege = $db->getArrResult();
		return $privilege;
	}
	
	function getUserID(){
		global $db;
		$query = "SELECT max(userid)+1 AS userid
				FROM tblUsers";
		$db->query($query);
		$line = mssql_fetch_array($db->result(), MSSQL_ASSOC);
		$userid = $line['userid'];
		return $userid;
	}
	
	function getUserInfo($company_code, $userid, $username, $privilege_id,
							$first_name, $middle_name, $last_name,
							$active_online, $date_registered, $last_login, $last_logout,
							$status, $date_status_updated, $status_updated_by){
		global $db;
		if($company_code != '') $and .= " AND compCode = '".$company_code."'";
		if($userid != '') $and .= " AND userid = '".$userid."'";
		if($username != '') $and .= " AND username LIKE '%".$username."%'";
		if($privilege_id != '') $and .= " AND privilegeId = '".$privilege_id."'";
		if($first_name != '') $and .= " AND firstName LIKE '%".$first_name."%'";
		if($middle_name != '') $and .= " AND middleName LIKE '%".$middle_name."%'";
		if($last_name != '') $and .= " AND lastName LIKE '%".$last_name."%'";
		if($active_online != '') $and .= " AND activeOnline = '".$active_online."'";
		if($date_registered != '') $and .= " AND dateRegistered >= '".$date_registered."'";
		if($last_login != '') $and .= " AND lastLogIn >= '".$last_login."'";
		if($last_logout != '') $and .= " AND lastLogOut >= '".$last_logout."'";
		if($status != '') $and .= " AND status = '".$status."'";
		if($date_status_updated != '') $and .= " AND dateStatusUpdated >= '".$date_status_updated."'";
		if($status_updated_by != '') $and .= " AND statusUpdatedBy = '".$status_updated_by."'";
		
		$query = "SELECT compCode, userid, username, privilegeId,
					firstName, middleName, lastName,
					activeOnline, dateRegistered, lastLogIn, lastLogOut,
					status, dateStatusUpdated, statusUpdatedBy
				FROM tblUsers
				WHERE username != ''
					$and
				ORDER BY username";
		$db->query($query);
		$user_info = $db->getArrResult();
		return $user_info;
	}
	
	function addUser($company_code, $userid, $privilege_id, $first_name, $middle_name, $last_name, $username, $date_registered, $status){
		global $db;
		$query = "INSERT INTO tblUsers(compCode, userid, username, privilegeId,
					firstName, middleName, lastName,
					dateRegistered, status)
				VALUES('".$company_code."', '".$userid."', '".$username."', '".$privilege_id."',
					'".$first_name."', '".$middle_name."', '".$last_name."',
					'".$date_registered."', '".$status."')";
		$db->query($query);
		$user_info = $this->getUserInfo($company_code, $userid, $username, $privilege_id,
								$first_name, $middle_name, $last_name,
								'', $date_registered, '', '',
								$status, '', '');
		return $user_info;
	}
	
	function deleteUser($username, $operator){
		global $db;
		$query = "UPDATE tblUsers
				SET status = 'X',
					dateStatusUpdated = getdate(),
					statusUpdatedBy = '".$operator."'
				WHERE username = '$username'";
		$db->query($query);
	}
	
	function logoutUser($username){
		global $db;
		$query = "UPDATE tblUsers
				SET activeOnline = '0',
					lastLogOut = getdate()
				WHERE username = '$username'";
		$db->query($query);
	}
	
	function resetPassword($username, $operator){
		global $db;
		$query = "UPDATE tblUsers
				SET status = 'R',
					password = NULL,
					dateStatusUpdated = getdate(),
					statusUpdatedBy = '".$operator."'
				WHERE username = '$username'";
		$db->query($query);
	}
	
	function activateUser($username, $operator){
		global $db;
		$query = "UPDATE tblUsers
				SET status = 'A',
					dateStatusUpdated = getdate(),
					statusUpdatedBy = '".$operator."'
				WHERE username = '$username'";
		$db->query($query);
	}
	
	function deactivateUser($username, $operator){
		global $db;
		$query = "UPDATE tblUsers
				SET status = 'D',
					dateStatusUpdated = getdate(),
					statusUpdatedBy = '".$operator."'
				WHERE username = '$username'";
		$db->query($query);
	}
}

?>