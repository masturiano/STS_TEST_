<?
#Description: Open a Released Purchase Order
#Author: Jhae Torres
#Date Created: September 22, 2008


session_start();
$company_code=$_SESSION['comp_code'];

require_once "index.php";
require_once "../../includes/config.php";
require_once "../../functions/db_function.php";
require_once "purchasing.obj.php";
require_once "../etc/etc.obj.php";

$gmt = time() + (8 * 60 * 60);
$date = date("m-d-Y", $gmt);
$datetime = date("m-d-Y H:i:s", $gmt);
$operator = $_SESSION['userid'];

$db = new DB;
$db->connect();

$etcTrans = new etcObject;
//$datetime = $etcTrans->formatDateTime($gmt);
$purchasingTrans = new purchasingObject;

#Search Page: PO List
$search_po_number = $_GET['po_number'];
$po_list = $purchasingTrans->getPO($search_po_number);
if(!empty($po_list)){
	$cnt = 1;
	foreach($po_list as $x){
		if($x['statCode']=='R' and $x['poReopenTag']!='Y'){
			$po .= "<tr>
					<td width='5%'>".$cnt."</td>
					<td width='4%'><input type='radio' class='check' name='reopen_po' id='reopen_po' value='".$x['poNumber']."' title='Re-open PO# ".$x['poNumber']."'
						onClick='assignTransaction(this.id); confirmReopenPO(this.value);' /></td>
					<td width='10%'>".$x['poNumber']."</td>
					<td width='15%'>".$etcTrans->formatDate($x['poDate'])."</td>
					<td width='15%'>".$etcTrans->formatDate($x['poExpDate'])."</td>
					<td width='40%'>".stripslashes($x['suppName'])."</td>
					<td>".$x['poTerms']."</td>
				</tr>";
			$cnt++;
		} else{
			if($cnt==1 and $x['poNumber']==$_GET['po_number']) $_GET['msg'] = $etcTrans->getMessage('PO0084');
		}
	}
} else{
	$_GET['msg'] = $etcTrans->getMessage('PO0030');
}
?>

<link rel='stylesheet' type='text/css' href='../../includes/style.css'></link>
<script type='text/javascript' src='../../functions/javascript_function.js'></script>
<script type='text/javascript' src='../../functions/prototype.js'></script>
<body onLoad='setFocus("po_number");'>

<!-- message area -->
<div id='msg'> 
  <?=$_GET['msg']?>
</div>

<div id='frame_body'>
	<form name='reopen_po_form' id='reopen_po_form' action='purchasing.trans.php' method='POST'>
		<input type='hidden' name='transaction' id='transaction' />
		
    <div class='header'> 
      <table>
        <tr> 
          <th>Date of Re-open:</th>
          <td>
          	<input type='text' class='readonly_textbox' name='reopen_date' id='reopen_date' value='<?=$datetime?>' readOnly />
          </td>
        </tr>
        <tr> 
          <th width='15%'>Operator:</th>
          <td><input type='text' class='readonly_textbox' name='reopen_operator' id='reopen_operator' value='<?=$operator;?>' readOnly /></td>
        </tr>
      </table>
    </div>
		<div class='search_header'>
			PO Number:&nbsp;
			<input type='text' class='textbox' name='po_number' id='po_number' tabIndex='1' maxLength='6' />
		  	&nbsp; 
			<input type='submit' class='go' name='search_reopen_po' id='search_reopen_po' value='GO' title='SEARCH PO NUMBER' tabIndex='2'
				onClick='assignTransaction(this.id);' />
		</div>
		
    <div class='details'> 
      <table>
        <th width='5%'>#</th>
        <th width='4%'>Re-<br>Open</th>
        <th width='10%'>PO Number</th>
        <th width='15%'>PO Date</th>
        <th width='15%'>Expected Date of Delivery</th>
        <th width='39%'>Vendor</th>
        <th>Terms</th>
      </table>
    </div>
		
    <div class='search_result'> 
      <table>
        <?=$po?>
      </table>
    </div>
	</form>
</div>

</body>