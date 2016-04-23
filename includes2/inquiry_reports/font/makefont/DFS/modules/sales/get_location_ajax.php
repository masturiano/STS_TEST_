<?
	session_start();
	require("../inventory/lbd_function.php");
	require_once "../../includes/config.php";
	require_once "../../functions/db_function.php";
	
	$db = new DB;
	$db->connect();
	$compCode = $_SESSION['comp_code'];

if($_GET['action'] == 'getLocDesc'){
	$qrygetlocDesc  = "SELECT * FROM tblLocation WHERE locCode = '{$_GET['locCode']}' AND compCode = '{$compCode}' AND locStat = 'A'";
	$resgetlocDesc = mssql_query($qrygetlocDesc);
	$cntgetlocDesc = mssql_num_rows($resgetlocDesc);
 	$rowgetlocDesc = mssql_fetch_assoc($resgetlocDesc);
	if($cntgetlocDesc > 0){
		echo strtoupper($rowgetlocDesc['locName']);
	}
	else{
		echo "<font color='red'>Invalid Location Code</font>";		
	}
}
?>