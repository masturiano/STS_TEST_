<?
include "../../functions/inquiry_session.php";
require_once "../../includes/config.php";
require_once "../../functions/db_function.php";
require_once "../../functions/inquiry_function2.php";
require_once "../../functions/inquiry_function.php";
$db = new DB;
$db->connect();
	
if(isset($_POST['update'])) { 
	$result_products = mssql_query("SELECT * FROM tblProdMast ORDER BY prdNumber");
	$num_products = mssql_num_rows($result_products);
	for ($i=0;$i<$num_products;$i++){ 
		$prd_no=mssql_result($result_products,$i,"prdNumber");
		$result_products_art = mssql_query("SELECT * FROM tblProdMast_art WHERE prdNumber = $prd_no");
		$prdSellUnit=mssql_result($result_products_art,0,"prdSellUnit");
		$prdBuyUnit=mssql_result($result_products_art,0,"prdBuyUnit");
		
		
		$UpdateSQL = "UPDATE tblProdMast SET ";
		$UpdateSQL .= "prdSellUnit = '".strtoupper($prdBuyUnit)."', ";
		$UpdateSQL .= "prdBuyUnit = '".strtoupper($prdSellUnit)."' ";
		$UpdateSQL .= "WHERE prdNumber = " . $prd_no;
	}
}
?>

<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<font size="2" face="Arial, Helvetica, sans-serif"> </font> 
<form name="form1" method="post" action="">
  <font size="2" face="Arial, Helvetica, sans-serif">
  <input name='update' <? echo $meron; ?> type='submit' class='queryButton' id='explode_data3' title='Display the Inventory Transaction'  value='Update Sell / Buy UM'/>
  </font> 
</form>
</body>
</html>
