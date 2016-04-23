<?
#Description: Etc. Module Object
#Author: Jhae Torres
#Date Created: March 10, 2008


class etcObject
{
	function redirectURL($file){
		global $config;
		header("Location: http://".$config['sys_host']."/".$config['sys_dir']."/".$file);
	}
	
	function formatDate($date){
		$date = new DateTime($date);
		$date = $date->format("m-d-Y");
		return $date;
	}
	
	function formatDateTime($datetime){
		$datetime = new DateTime($datetime);
		$datetime = $datetime->format("m-d-Y H:i:s");
		return $datetime;
	}
	
	function getMessage($msg_code){
		global $db;
		$query = "SELECT msgCode, msgDesc
				FROM tblMessage
				WHERE msgCode = '$msg_code'";
		$db->query($query);
		$msg = mssql_fetch_array($db->result(), MSSQL_ASSOC);
		return $msg['msgCode'].': '.$msg['msgDesc'];
	}
	
	function getStatus($table_name, $level){
		global $db;
		if($level!='') $and.=" AND statLevel = '$level'";
		
		$query = "SELECT statCode, statName
				FROM tblStatus
				WHERE statCode != ''
					AND tableName = '$table_name'
					$and
				ORDER BY statCode ASC";
		$db->query($query);
		$status = $db->getArrResult();
		return $status;
	}
	
	/*function getNumber($company_code, $field, $table){
		global $db;
		$query = "SELECT ".$field."
				FROM ".$table."
				WHERE compCode = '$company_code'";
		$db->query($query);
		$line = mssql_fetch_array($db->result(), MSSQL_ASSOC);
		$number = $line[$field] + 1;
		return $number;
	}
	
	function updateNumber($company_code, $field, $table){
		global $db;
		$number = $this->getNumber($company_code, $field, $table);
		
		$query = "UPDATE ".$table."
				SET ".$field." = '".$number."'
				WHERE compCode = '$company_code'";
		$db->query($query);
	}*/
	
	function getNumber($company_code, $field, $table){
		global $db;
		$query = "SELECT ".$field."
				FROM ".$table."
				WHERE compCode = '$company_code'";
		$db->query($query);
		$line = mssql_fetch_array($db->result(), MSSQL_ASSOC);
		$number = $line[$field] + 1;
		
		$update_query = "UPDATE ".$table."
						SET ".$field." = '".$number."'
						WHERE compCode = '$company_code'";
		$db->query($update_query);
		
		return $number;
	}
	
	function getCompanyName($company_code){
		global $db;
		$query = "SELECT compShortName
				FROM tblCompany
				WHERE compCode = '$company_code'";
		$db->query($query);
		$line = mssql_fetch_array($db->result(), MSSQL_ASSOC);
		$company_name = $line['compShortName'];
		return $company_name;
	}
	
	function checkIfUserExist($username, $password){
		if($password!='') $and .= " AND password = '".sha1($password)."'";
		
		global $db;
		$query = "SELECT compCode, userid, username, password, privilegeId,
					firstName, middleName, lastName, activeOnline, dateRegistered,
					lastLogIn, lastLogOut, status, dateStatusUpdated, statusUpdatedBy
				FROM tblUsers
				WHERE username = '$username'
					$and";
		$db->query($query);
		$user = mssql_fetch_array($db->result(), MSSQL_ASSOC);
		return $user;
	}
	
	function writePassword($username, $password){
		global $db;
		$query = "UPDATE tblUsers
				SET password = '".sha1($password)."',
					status = 'A'
				WHERE username = '$username'";
		$db->query($query);
		$user = $this->checkIfUserExist($username, $password);
		return $user;
	}
	
	function tagUserOnline($username, $tag, $trans, $current_datetime){
		global $db;
		$query = "UPDATE tblUsers
				SET activeOnline = '".$tag."',
					".$trans." = '".$current_datetime."'
				WHERE username = '$username'";
		$db->query($query);
	}
}
?>