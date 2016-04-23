<?
include "../../functions/inquiry_session.php";
require_once "../../includes/config.php";
require_once "../../functions/db_function.php";
require_once "../../functions/inquiry_function.php";
$db = new DB;
$db->connect();
$combo_transfer=$_POST['combo_transfer'];
$text_transfer=$_POST['text_transfer'];
$hide_find_transfer=$_POST['hide_find_transfer'];
$hide_num_transfer=$_POST['hide_num_transfer'];

find_transfer_function($text_transfer); /// inquiry_function.php

if (($text_transfer=="") || ($text_transfer=="Transfer No or Date")) { // display record in transfer #############################################################
	if ($hide_find_transfer=="") {
		$query_transfer="SELECT * FROM tblTransferHeader WHERE trfStatus = 'Z'";
	} else {
		if ($hide_num_transfer=="YES") {
			$query_transfer="SELECT * FROM tblTransferHeader 
			WHERE (compCode =  $company_code) AND (trfStatus <> 'R') AND (trfNumber LIKE '%$hide_find_transfer%')
			ORDER BY trfNumber ASC";	
		} else {
			$query_transfer="SELECT * FROM tblTransferHeader 
			WHERE (compCode =  $company_code) AND (trfStatus <> 'R') AND (trfDate = '$hide_find_transfer')
			ORDER BY trfDate ASC";
		}
	}
} else {
	if ($text_transfer=="*") {
		$query_transfer="SELECT * FROM tblTransferHeader WHERE (compCode =  $company_code) AND (trfStatus <> 'R') ORDER BY trfNumber ASC";
		$hide_find_transfer="";
	} else {
		if(is_numeric($text_transfer)) {
			$query_transfer="SELECT * FROM tblTransferHeader 
			WHERE (compCode =  $company_code) AND (trfStatus <> 'R') AND (trfNumber LIKE '%$text_transfer%')
			ORDER BY trfNumber ASC";
			$hide_num_transfer="YES";
		} else {
			$query_transfer="SELECT * FROM tblTransferHeader 
			WHERE (compCode =  $company_code) AND (trfStatus <> 'R') AND (trfDate = '$text_transfer')
			ORDER BY trfDate ASC";
			$hide_num_transfer="NO";
		}
		$hide_find_transfer=$text_transfer;
	}
}
$result_transfer=mssql_query($query_transfer);
$num_transfer = mssql_num_rows($result_transfer);
?>

<!--THIS IS THE CSS-->
<link rel='stylesheet' type='text/css' href='../../includes/style1.css'></link>
<div id='frame_body'>
<!--THIS IS THE HEADER-->
<hr>
    <form action="trf_main.php" method="post" name="form_transfer" id="form_transfer">
<div class='header'> 
      <table width="465" align="center">
        <tr> 
          <td><div align="center"><font size="2" face="Arial, Helvetica, sans-serif"><fieldset>
              <legend>Search Transfer Number - Date</legend>
		  <font size="2" face="Arial, Helvetica, sans-serif">
<select name="combo_transfer" id="select3" style="width:200px; height:20px;" onChange="move_transfer_number()">
                <option selected><? echo $combo_transfer; ?></option>
                <?
				for ($i=0;$i<$num_transfer;$i++){  
					$combo_trans_no=mssql_result($result_transfer,$i,"trfNumber"); 
					$combo_trans_date=mssql_result($result_transfer,$i,"trfDate"); 
					$date = new DateTime($combo_trans_date);
					$combo_trans_date = $date->format("m/d/Y");		
				?>
                <option><? echo $combo_trans_no." - ".$combo_trans_date; ?></option>
                <? } ?>
                <option> </option>
              </select>
              <b><b><b> </b></b></b> 
              <input name="text_transfer" type="text" id="text_transfer" onFocus="if(this.value=='Transfer No or Date')this.value='';" value="Transfer No or Date">
              <input name='button_transfer' type='submit' class='queryButton' id='continue' title='Search Products' onClick="javascript:document.form_transfer.submit();" value='Find'/>
              <span class="style20"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><span class="style18"><b><b><span class="style1"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b> 
              <input name="hide_find_transfer" type="hidden" id="cbqtybo" value="<?php echo $hide_find_transfer; ?>">
              <input name="hide_num_transfer" type="hidden" id="hide_find_product" value="<?php echo $hide_num_transfer; ?>"></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></b></b></span></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></font>
		  </fieldset>
              </font></div></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
        </tr>
        <tr> 
          <td><center>
              <br />
            </center></td>
        </tr>
        <tr> 
          <td><center>
              Transfer Number: 
            </center></td>
        </tr>
        <tr> 
          <td width="510"><center>
              <input name='trfNo' type='text' class='searchBox' id='trfNo' tabindex='1' onchange='updateRecord()' maxlength='6' readonly="true" />
            </center></td>
        </tr>
        <tr> 
          <td><span class="search_header"> 
            <center>
              <input name='Update Transfer' type='button' class='queryButton' id='Update Transfer' title='Search Ref Number' onClick="updateRecord()" value='Update Transfer' />
              <input type='button' class='queryButton' name='Create a Transfer' id='Create a Transfer' value='Create a Transfer' title='Search Ref Number' onClick="newRecord()" />
              <input type='button' class='queryButton' name='Receive a Transfer' id='Receive a Transfer' value='Receive a Transfer' title='Search Ref Number' onClick="receive()" />
            </center>
            </span></td>
        </tr>
        <tr> 
          <td>&nbsp;</td>
        </tr>
        <tr> 
          <td>&nbsp;</td>
        </tr>
      </table>
</div>

<!--Standard Info-->
<div class='details'></div>
<!--SAMPLE FROM-->
</form>
</div>

<!-- footer - status/message -->
<div id='footer'> 
  <? echo $message;?>
</div>
<!--setfocus on reference search box-->
<script type="text/javascript">
document.getElementById('trfNo').focus();
</script>
<script type='text/javascript'>
////////////////////////////////////////////////////////////////////////////////////
function newRecord()////////////////////////////////////////////////////////////////
	{
	window.location="transfers_transaction.php?do=newRecord";
	}
////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////-----
function updateRecord()/////////////////////////////////////////////////////////////
	{
	var numericExpression = /^(\d+\.\d{0,4}|\d+)$/;
	var trfNo = document.getElementById('trfNo').value;
	if(!trfNo.match(numericExpression))
		{
		alert("Key-in Transfer Number. \n\(Example: 123456\)");
		document.getElementById('trfNo').focus();
		return false;
		}
	window.location = "transfers_transaction.php?do=updateRecord&while=newIte&trfNo="+trfNo;
	}
function receive()
	{
	window.location="transfers_transaction.php?do=receiveRecord";
	}
function move_transfer_number() {
	///"-" = separator
	var combo_string = document.form_transfer.combo_transfer.value;
	var stringlen=combo_string.length;
	var i = 0;
	
	stringlen=stringlen-1;
	var stringnew="";
	for (i=0;i<=stringlen;i++){
		if (combo_string[i]!="-") { 
			stringnew=stringnew+combo_string[i];
		} else {
			break;
		}
	}
	stringnew=stringnew*1;
	document.form_transfer.trfNo.value=stringnew;
}
////////////////////////////////////////////////////////////////////////////////////
</script>
