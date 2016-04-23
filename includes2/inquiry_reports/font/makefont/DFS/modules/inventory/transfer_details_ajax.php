<?
	require("../inventory/lbd_function.php");
		
	require_once "../../includes/config.php";
	require_once "../../functions/db_function.php";

	$db = new DB;
	$db->connect();
	

	$qryTransDtl = "SELECT * FROM tblTransferDtl WHERE trfNumber = ".$_GET['trans_no']." AND compCode = ".$_GET['comp_code']." AND upcCode = '".$_GET['upc_code']."'";
	$resTransDtl = mssql_query($qryTransDtl);
	$num = mssql_num_rows($resTransDtl);
	$row = mssql_fetch_array($resTransDtl);
	$qty_id = $_GET['qty_id'];
	if($_GET['do'] == 'getQtyOut'){
		$qty_out = $row[4];
		$qty_out = number_format($qty_out,0);
		echo "document.getElementById('$qty_id').value='".$qty_out."';";
	}
	if($_GET['do'] == 'getQtyIn'){
		$qty_in = $row[5];
		$qty_in = number_format($qty_in,0);
		echo "document.getElementById('$qty_id').value='".$qty_in."';";
	}
?>