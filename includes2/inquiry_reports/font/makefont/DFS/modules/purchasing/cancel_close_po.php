<?
#Description: Cancel/Close Purchase Order
#Author: Jhae Torres
#Date Created: January 12, 2009


session_start();
$company_code = $_SESSION['comp_code'];
$userid = $_SESSION['userid'];

require_once "index.php";
require_once "../../includes/config.php";
require_once "../../functions/db_function.php";
require_once "purchasing.obj.php";
require_once "../etc/etc.obj.php";

$gmt = time() + (8 * 60 * 60);
$date = date("m-d-Y", $gmt);
$datetime = date("m-d-Y H:i:s", $gmt);

$db = new DB;
$db->connect();

$etcTrans = new etcObject;
//$datetime = $etcTrans->formatDateTime($gmt);
$purchasingTrans = new purchasingObject;

$trans = $_GET['trans'];
if($trans=='1' or $trans==''){ //transaction = cancel po
	$trans_type = 'cancel';
	$new_stat = 'X';
	$def_cancel = 'selected';
	$def_close = '';
	$stat_1 = 'R';
	$stat_2 = 'H';
	$warning_code = 'PO0105';
	$error_code = 'PO0103';
} else{ //transaction = close po
	$trans_type = 'close';
	$new_stat = 'C';
	$def_cancel = '';
	$def_close = 'selected';
	$stat_1 = 'D';
	$stat_2 = 'P';
	$warning_code = 'PO0106';
	$error_code = 'PO0104';
}


#Search Page: PO List
$search_po_number = $_GET['po_number'];
$po_list = $purchasingTrans->getPO($search_po_number);
if(!empty($po_list)){
	$cnt = 1;
	foreach($po_list as $x){
		if($x['poStat']==$stat_1 or $x['poStat']==$stat_2){
			$po .= "<tr>
					<td width='5%'>".$cnt."</td>
					<td width='4%'><input type='checkbox' class='check' name='select_po[]' id='select_po[]' value='".$x['poNumber']."' /></td>
					<td width='10%'>".$x['poNumber']."</td>
					<td width='15%'>".$etcTrans->formatDate($x['poDate'])."</td>
					<td width='15%'>".$etcTrans->formatDate($x['poExpDate'])."</td>
					<td width='41%'>".stripslashes($x['suppName'])."</td>
					<td>".$x['poTerms']."</td>
				</tr>";
			$cnt++;
		} else{
			if($cnt==1 and $x['poNumber']==$_GET['po_number']){
				if($x['poStat']=='O' or $x['poStat']==''){
					$_GET['msg'] = $etcTrans->getMessage('PO0102');
				} elseif($x['poStat']==$new_stat){
					$_GET['msg'] = $etcTrans->getMessage($warning_code);
				} else{
					$_GET['msg'] = $etcTrans->getMessage($error_code);
				}
			}
		}
	}
} else{
	$_GET['msg'] = $etcTrans->getMessage('PO0030');
}
?>

<link rel='stylesheet' type='text/css' href='../../includes/style.css'></link>
<script type='text/javascript' src='../../functions/javascript_function.js'></script>

<body onLoad="setFocus('po_number')">

<!-- message area -->
<div id='msg'> 
  <?=$_GET['msg']?>
</div>

<div id='frame_body'>
	<form name='cancel_close_po_form' id='cancel_close_po_form' action='purchasing.trans.php?' method='POST'>
		<input type='hidden' name='transaction' id='transaction' />
	
		<div class='header'> 
	      <table>
	        <tr> 
	          <th>Date of Cancel/Close:</th>
	          <td>
	          	<input type='text' class='readonly_textbox' name='release_date' id='release_date' value='<?=$datetime?>' readOnly />
	          </td>
	        </tr>
	        <tr> 
	          <th width='15%'>Operator:</th>
	          <td><input type='text' class='readonly_textbox' name='release_operator' id='release_operator' value='<?=$userid;?>' readOnly /></td>
	        </tr>
	      </table>
	    </div>
			<div class='search_header'>
				Transaction Type:&nbsp;
				<select class='textbox' name='trans_type' id='trans_type'>
					<option value='1' <?=$def_cancel?> >CANCEL PO</option>
					<option value='2' <?=$def_close?> >CLOSE PO</option>
				</select>&nbsp;&nbsp;
				PO Number:&nbsp;
				<input type='text' class='textbox' name='po_number' id='po_number' tabIndex='1' maxLength='6' />
			  	&nbsp; 
				<input type='submit' class='go' name='search_po_cancel_close' id='search_po_cancel_close' value='GO' title='SEARCH PO NUMBER' tabIndex='2'
					onClick='assignTransaction(this.id);' />
			</div>
			
	    <div class='details'> 
	      <table>
	        <th width='5%'>#</th>
	        <th width='4%'>&nbsp;</th>
	        <th width='10%'>PO Number</th>
	        <th width='14%'>PO Date</th>
	        <th width='15%'>Expected Date of Delivery</th>
	        <th width='40%'>Vendor</th>
	        <th>Terms</th>
	      </table>
	    </div>
			
	    <div class='search_result_2'> 
	      <table>
	        <?=$po?>
	      </table>
	    </div>
	     
	    <input type='button' class='cancel_po_button' name='<?=strtolower($trans_type)?>_po' id='<?=strtolower($trans_type)?>_po' tabIndex='' title='<?=strtoupper($trans_type)?> PURCHASE ORDER' value='<?=strtoupper($trans_type)?> PO'
	  		onClick="assignTransaction(this.id); confirm<?=ucfirst($trans_type)?>PO();" />
	</form>
</div>

</body>