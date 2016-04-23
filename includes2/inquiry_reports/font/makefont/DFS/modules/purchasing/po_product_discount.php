<?
#Description: Purchase Order Products' Discounts Pop-up Window
#Author: Jhae Torres
#Date Created: May 28, 2008

require_once "../../includes/config.php";
require_once "../../functions/db_function.php";
require_once "purchasing.obj.php";
require_once "../etc/etc.obj.php";

$db = new DB;
$db->connect();
$purchasingTrans = new purchasingObject;
#$db->disconnect();

$company_code = $_GET['company_code'];
$po_number = $_GET['po_number'];
$product_code = $_GET['product_code'];
$po_discount = $purchasingTrans->checkIfPOItemDiscountExist($company_code, $po_number, $product_code, '');
foreach($po_discount as $x){
	$po_discount_list .= "<tr>
							<td>".$x['poDiscSeq']."</td>
							<td>".$product_code."</td>
							<td>".$x['allwDesc']."</td>
							<td>".$x['poItemDiscPcnt']."</td>
							<td>".$x['poItemDiscAmt']."</td>
							<td>".$x['allwCostTag']."</td>
						</tr>";
}
?>

<html>
<head>
	<link rel='stylesheet' type='text/css' href='../../includes/style.css'></link>
<head>
<body>
<div id='popup'>
	<h1>PO ITEM DISCOUNT(S):</h1>
	<table>
		<tr>
			<th>Sequence<br>Number</th>
			<th>Product<br>Code</th>
			<th>Allowance<br>Type</th>
			<th>Discount<br>Percent</th>
			<th>Discount<br>Amount</th>
			<th>Affects<br>COG</th>
		</tr>
		<?=$po_discount_list?>
	</table>
</div>
</body>
</html>