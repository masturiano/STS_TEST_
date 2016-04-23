<?
#Description: RA001P. RA Printing/Reprinting Main Program
#Author: Jhae Torres
#Date Created: July 03, 2008


session_start();
$company_code = $_SESSION['comp_code'];
$userid = $_SESSION['userid'];

require_once "../../includes/config.php";
require_once "../../functions/db_function.php";
require_once "inventory_po.obj.php";
require_once "../etc/etc.obj.php";



$gmt = time() + (8 * 60 * 60);
$date = date("m-d-Y", $gmt);
$operator = "Jhae Torres";

$db = new DB;
$db->connect();

$inventoryTrans = new inventoryObject;
$etcTrans = new etcObject;
$ra_number = $etcTrans->getNumber($company_code, 'raNumber', 'tblRaNumber');
?>

<link rel='stylesheet' type='text/css' href='../../includes/style.css'></link>
<script type='text/javascript' src='../../functions/javascript_function.js'></script>
<script type='text/javascript' src='../../functions/prototype.js'></script>
<body onLoad='setRAReportDefaults("");'>

<!-- message area -->
<div id='msg'> 
  <? 
  	$msg=$_GET['msg'];
	$msgko=$_GET['msgko'];
	echo $msg;
	if ($msgko=="ON") {
		$po_numberko=$_GET['po_number'];
		$ra_numberko=$_GET['ra_number'];
		echo "<script type='text/javascript'>
			  window.open(\"ra_pdf.php?ra_number=".$ra_numberko."&po_number=".$po_numberko."\",\"\",\"width=800,height=800,left=250,top=100\");
			  </script>"; 
	}
	if ($msgko=="YES") {
		$ra_numberko=$_GET['ra_number'];
		echo "<script type='text/javascript'>
			window.open(\"ra_pdf.php?ra_number=".$ra_numberko."\",\"\",\"width=800,height=800,left=250,top=100\");
			</script>"; 
	}
	
  ?>
</div>

<div id='frame_body'>
	<form name='ra_report_form' id='ra_report_form' action='inventory_po.trans.php' method='POST'>
	<input type='hidden' name='transaction' id='transaction' />
		<div class='ra_report'>
			<table>
				<tr>
					<th>Receiving Authorization</th>
					<td>
						<input type='radio' class='radio_button' name='ra_trans' id='ra_trans' value='print' onClick='setRAReportDefaults(this);' checked />Print&nbsp;&nbsp;&nbsp;
						<input name='ra_trans' type='radio' disabled="true" class='radio_button' id='ra_trans' onClick='setRAReportDefaults(this);' value='reprint' />Re-print	
					</td>
				</tr>
			</table>
				<div id='print_div'>
					<table>
					<tr>
						<th>RA Number:</th>
						<td><input type='text' class='readonly_textbox' name='ra_number' id='ra_number' value='<?=$ra_number?>' readOnly /></td>
					</tr>
					<tr>
						<th>PO Number:</th>
						<td><input type='text' class='textbox' name='po_number' id='po_number' maxLength='6' /></td>
					</tr>
					<tr>
						<td colspan='2'><input class='button' type='button' name='print_ra' id='print_ra' value='PRINT'
							onClick='assignTransaction(this.id); validateRAReportEntry("po_number");' />
                        </td>
					</tr>
					</table>
		  </div>
				<div id='reprint_div'>
					<table>
					<tr>
						<th>RA Number:</th>
						<td><input type='text' class='textbox' name='reprint_ra_number' id='reprint_ra_number' onChange="reprint_ra();" maxLength='6' /></td>
					</tr>
					<tr>
						<td colspan='2'><input class='button' type='button' name='reprint_ra' id='reprint_ra' value='REPRINT'
							onClick='assignTransaction(this.id); validateRAReportEntry("ra_number");' /></td>
					</tr>
					</table>
				</div>
		</div>	
	</form>
  
</div>

</body>