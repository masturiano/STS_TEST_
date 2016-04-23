<?
	require("../inventory/lbd_function.php");
		
	require_once "../../includes/config.php";
	require_once "../../functions/db_function.php";

	$db = new DB;
	$db->connect();
	
if($_GET['do'] == 'getUser'){
	$qryUser = "SELECT * FROM tblUsers WHERE userid = '".$_GET['type']."'";
	$resUser = mssql_query($qryUser);
	$num = mssql_num_rows($resUser);
	$row = mssql_fetch_array($resUser);
	$user = $row[5]. " " . $row[7];
	//if ($num>0) {
		echo "document.getElementById('txtBuyerName').value='".$user."';";
	//else {
		//echo "document.getElementById('txtBuyerName').value='';";
	//}
}
?>