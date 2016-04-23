<?
include "../../functions/inquiry_session.php";
require_once "../../includes/config.php";
require_once "../../functions/db_function.php";
require_once "../../functions/inquiry_function2.php";
require_once "../../functions/inquiry_function.php";
$gmt = time() + (8 * 60 * 60);
$date = date("m-d-Y", $gmt);
$today = date("m/d/Y");
$db = new DB;
$db->connect();
$hide_save=$_POST['hide_save'];

if($hide_save=="save_records") {
	$from_date=$_POST['from_date'];
	$from_location=$_POST['from_location'];
	$file_name= basename($_FILES['file_name']['name']);
	$local_file = $_POST['hidden_local'];
	$sales_amt = $_POST['sales_amt'];
	$returns_amt = $_POST['returns_amt'];
	$void_amt = $_POST['void_amt'];
	
	$handle = fopen ($local_file, "rb");
	$contents = "";
	while(!feof($handle)) {
		$line = fgets($handle);
		$contents .= "$line";
	}
	fclose ($handle);
	
	$array = split ("\r\n", $contents);
	$array_count = count($array);
	
	for ($i=0;$i<$array_count;$i++){ 
		$array2 = split (" ", $array[$i]);
		$grid0=$array2[0]; //rec
		$grid1=$array2[1]; //sto
		$grid2=$array2[2]; //term
		$grid3=$array2[3]; //trans
		$grid7=$array2[7]; //upc
		$grid8=$array2[8]; //qty
		$grid9=$array2[9]; //up
		
		$new_grid7=$grid7[10].$grid7[11].$grid7[12].$grid7[13].$grid7[14].$grid7[15].$grid7[16].$grid7[17].$grid7[18].$grid7[19].$grid7[20].$grid7[21].$grid7[22];
		$qryUpc = mssql_query("SELECT * FROM tblUpc WHERE upcCode = '$new_grid7'");
		$num_upc = mssql_num_rows($qryUpc);
		if ($num_upc>0) {
				$grid0_s=split("\"",$grid0);
				$grid0_split = $grid0_s[1];
				$grid7_s=split("\"",$grid7);
				$grid7_split=$grid7_s[1];
				$grid7_len = strlen($grid7_split) - 1;
				$grid8_split = $grid8[0].$grid8[1].$grid8[2].$grid8[3].$grid8[4].$grid8[5].".".$grid8[6].$grid8[7];
				$new_grid7=$grid7[10].$grid7[11].$grid7[12].$grid7[13].$grid7[14].$grid7[15].$grid7[16].$grid7[17].$grid7[18].$grid7[19].$grid7[20].$grid7[21].$grid7[22];
				$grid9_split = $grid9[0].$grid9[1].$grid9[2].$grid9[3].$grid9[4].$grid9[5].".".$grid9[6].$grid9[7];
				$x_amount = $grid9_split * $grid8_split;
				$discount = 0;
				$from_location=getCodeofString($from_location); ///pick in inventory_inquiry_function.php
				$from_location=trim($from_location);
				$InsertSQL = "INSERT INTO tblPosSalesTrans(compCode, storeNo, termNo, tranNo, slsRecCode, slsTranDate, slsUpcNo, slsUnitPrice, slsQty, slsExtAmt, slsDiscAmt)";
				$InsertSQL .= "VALUES (". $company_code. ", ". $from_location. ", " . $grid2 . ", " . $grid3 . ", '" . strtoupper($grid0_split) . "', '" . $from_date . "', '" . $new_grid7 . "'," . $grid9_split . "," . $grid8_split . ", " . $x_amount . ", " . $discount . ")";
				mssql_query($InsertSQL);
		}
	}
	echo "<script>alert('<<< Sales Transactions successfully saved >>>')</script>";
}

################################ if search button is click
if(isset($_POST['explode_data'])) {
	$hoy=$_FILES['file_name']['name'];
	$local_file2 = $_POST['hidden_local'];
	$sales_amt = $_POST['sales_amt'];
	$returns_amt = $_POST['returns_amt'];
	$void_amt = $_POST['void_amt'];
	$total_sales = $_POST['total_sales'];
	$total_returns = $_POST['total_returns'];
	$total_void = $_POST['total_void'];
	if($hoy) {
		$from_date=$_POST['from_date'];
		$file_name= basename($_FILES['file_name']['name']);
		$local_file = $_FILES['file_name']['tmp_name'];
		
		$file= $_POST['file_name'];
		$handle = fopen ($local_file, "rb");
		$contents = "";
		while(!feof($handle)) {
			$line = fgets($handle);
			$contents .= "$line";
		}
		fclose ($handle);
		
		#####################################
		//$handle2 = fopen ($local_file, "w+");
		///fwrite($handle2, "art");
		
		//fclose ($handle2);
		#####################################
	
		$array = split ("\r\n", $contents);
		$array_count = count($array);
	}
}
################################ if first run the module
if ($from_date =="") {
	$from_date=$today;
	$to_date=$today;
}

################################ if print button is click
if(isset($_POST['print_ci'])) {
	$from_date=$_POST['hide_from_date'];
	$to_date=$_POST['hide_to_date'];
	
} 

if ($array_count>0) {
	$meron="";
	$msg="Processing data.... Please wait";
} else {
	$meron="disabled=\"true\"";
	$msg="";
}
?>

<html>
<head>
<title>End of Day - Update Product Sales</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script type='text/javascript' src='../../functions/inquiry_reports/calendar.js'></script>
<script type="text/javascript">

function val_date() {
	var today_date = new Date();
 	var from_date=document.form_search.from_date.value
    var to_date=document.form_search.to_date.value
	var today_date_mm = Date.parse(today_date);
	var from_date_mm = Date.parse(from_date);
	var to_date_mm = Date.parse(to_date);
	if(from_date_mm > today_date_mm) {
		alert("From Date must not be greater than to Current Date.");
		document.form_search.from_date.value="";
		document.form_search.from_date.focus();
		return false;
	}
	if(to_date_mm > today_date_mm) {
		alert("To Date must not be greater than to Current Date.");
		document.form_search.to_date.value="";
		document.form_search.to_date.focus();
		return false;
	}
	if(from_date_mm > to_date_mm) {
		alert("From Date must not be greater than to To Date.");
		document.form_search.from_date.value="";
		document.form_search.from_date.focus();
		return false;
	} 
}
function val_all() {
	var today_date = new Date();
 	var from_date=document.form_search.from_date.value
	var from_location=document.form_search.from_location.value
	var file_name=document.form_search.file_name.value
	var today_date_mm = Date.parse(today_date);
	var from_date_mm = Date.parse(from_date);
	var sales_amt=document.form_search.sales_amt.value
	var returns_amt=document.form_search.returns_amt.value
	var void_amt=document.form_search.void_amt.value
	var numeric = /^(\d+\.\d{0,2}|\d+)$/;
	//var theTable = document.getElementById("theTable");
	//var numberRowsInTable = theTable.rows.length;
	//alert (numberRowsInTable);
	var fcount = file_name.replace(/\\/g,"\\\\");
	document.form_search.hidden_local.value=fcount;
	
	if(from_date_mm > today_date_mm) {
		alert("Transaction Date must not be greater than to Current Date.");
		document.form_search.from_date.value="";
		document.form_search.from_date.focus();
		return false;
	}
	
	if(from_date=="") { 
		alert("Key-in Transaction Date.");
		document.form_search.from_Date.focus();
		return false;
	}
	
	if(file_name=="") { 
		alert("Browse file");
		document.form_search.file_name.focus();
		return false;
	}	
	if(from_location=="") { 
		alert("Select Location");
		document.form_search.from_location.focus();
		return false;
	}	
	document.form_search.submit();
}

function checkAll() {	
	var frm = document.form_print;
	var cnt = parseInt(frm.txtCOUNT.value);
	for (i=0; i<=cnt; i++)
		eval("frm.btncheck" + i + ".checked=true;");
}
	
function clearSelection() {
	var frm = document.form_print;
	var cnt = parseInt(frm.txtCOUNT.value);
	for (i=0; i<=cnt; i++)
		eval("frm.btncheck" + i + ".checked=false;");
}

function val_file_date() {
	var from_date=document.form_search.from_date.value
	var from_location=document.form_search.from_location.value
	var today_p = Date.parse(from_date);      
	var file_name=document.form_search.file_name.value
	var file_split = file_name.split(/\\/);
	var array_length = file_split.length - 1;
	var file_slice_file_name = file_split[array_length];
	var slice_date = file_slice_file_name[2]+file_slice_file_name[3]+"/"+file_slice_file_name[4]+file_slice_file_name[5]+"/20"+file_slice_file_name[6]+file_slice_file_name[7];
	var slice_date_p = Date.parse(slice_date);    
	var valid_date = /^([0-9]{2,2}[\/]{1,1}[0-9]{2,2}[\/]{1,1}[0-9]{4,4})$/;
	
	if(!slice_date.match(valid_date)) {
		alert("Sales Date of file : \""+ slice_date +"\" \r\n\r\nInvalid date format!!! Sales Date will set to the current date...");
		//document.form_search.from_date.value="";
		return false;
	} else {
		if (slice_date_p != today_p) {
			var change_date = confirm("Sales Date of file : \""+ slice_date +"\" \r\nCurrent Date       : \""+ from_date +"\" \r\n\r\nSales Date of file do not match in the current date... Do you want to change the Sales Date?");
			if(change_date) {
				document.form_search.from_date.value = slice_date;
			}
		}
	}

	var sales_amt=document.form_search.sales_amt.value
	var returns_amt=document.form_search.returns_amt.value
	var void_amt=document.form_search.void_amt.value
	var numeric = /^(\d+\.\d{0,2}|\d+)$/;
	if(sales_amt=="") { 
		alert("Key-in sales Amount.");
		document.form_search.sales_amt.focus();
		document.form_search.explode_data.disabled=true;
		return false;
	}
	
	if(!sales_amt.match(numeric)) {
		alert("sales amount Quantity: Numbers only or decimal places should not greater than 2");
		document.form_search.sales_amt.value="";
		document.form_search.explode_data.disabled=true;
		return false;
	}

	
	if(returns_amt=="") { 
		alert("Key-in returns amount.");
		document.form_search.returns_amt.focus();
		document.form_search.explode_data.disabled=true;
		return false;
	}
	
	if(!returns_amt.match(numeric)) {
		alert("returns amount Quantity: Numbers only or decimal places should not greater than 2");
		document.form_search.returns_amt.value="";
		document.form_search.explode_data.disabled=true;
		return false;
	}
	
	if(void_amt=="") { 
		alert("Key-in void amount.");
		document.form_search.void_amt.focus();
		document.form_search.explode_data.disabled=true;
		return false;
	}
	
	if(!void_amt.match(numeric)) {
		alert("void amount Quantity: Numbers only or decimal places should not greater than 2");
		document.form_search.void_amt.value="";
		document.form_search.explode_data.disabled=true;
		return false;
	}
	
	if(file_name=="") { 
		alert("Browse file");
		document.form_search.file_name.focus();
		document.form_search.explode_data.disabled=true;
		return false;
	}	
	if(from_location=="") { 
		alert("Select Location");
		document.form_search.from_location.focus();
		document.form_search.explode_data.disabled=true;
		return false;
	}
	document.form_search.explode_data.disabled=false;
}

function val_sales_returns_void_amt() {
	var file_name=document.form_search.file_name.value
	var from_location=document.form_search.from_location.value
	var sales_amt=document.form_search.sales_amt.value
	var returns_amt=document.form_search.returns_amt.value
	var void_amt=document.form_search.void_amt.value
	var numeric = /^(\d+\.\d{0,2}|\d+)$/;
	if(sales_amt=="") { 
		alert("Key-in sales Amount.");
		document.form_search.sales_amt.focus();
		document.form_search.explode_data.disabled=true;
		return false;
	}
	
	if(!sales_amt.match(numeric)) {
		alert("sales amount Quantity: Numbers only or decimal places should not greater than 2");
		document.form_search.sales_amt.value="";
		document.form_search.explode_data.disabled=true;
		return false;
	}

	
	if(returns_amt=="") { 
		alert("Key-in returns amount.");
		document.form_search.returns_amt.focus();
		document.form_search.explode_data.disabled=true;
		return false;
	}
	
	if(!returns_amt.match(numeric)) {
		alert("returns amount Quantity: Numbers only or decimal places should not greater than 2");
		document.form_search.returns_amt.value="";
		document.form_search.explode_data.disabled=true;
		return false;
	}
	
	if(void_amt=="") { 
		alert("Key-in void amount.");
		document.form_search.void_amt.focus();
		document.form_search.explode_data.disabled=true;
		return false;
	}
	
	if(!void_amt.match(numeric)) {
		alert("void amount Quantity: Numbers only or decimal places should not greater than 2");
		document.form_search.void_amt.value="";
		document.form_search.explode_data.disabled=true;
		return false;
	}
	
	if(file_name=="") { 
		alert("Browse file");
		document.form_search.file_name.focus();
		document.form_search.explode_data.disabled=true;
		return false;
	}	
	if(from_location=="") { 
		alert("Select Location");
		document.form_search.from_location.focus();
		document.form_search.explode_data.disabled=true;
		return false;
	}
	document.form_search.explode_data.disabled=false;
}

function val_totals() {
	var sales_amt=document.form_search.sales_amt.value
	var returns_amt=document.form_search.returns_amt.value
	var void_amt=document.form_search.void_amt.value
	var sales_amt2=document.form_search.sales_amt2.value
	var returns_amt2=document.form_search.returns_amt2.value
	var void_amt2=document.form_search.void_amt2.value
	var concat_msg=""
	sales_amt=sales_amt*1
	returns_amt=returns_amt*1
	void_amt=void_amt*1
	sales_amt2=sales_amt2*1
	returns_amt2=returns_amt2*1
	void_amt2=void_amt2*1
	
	
	if (sales_amt>sales_amt2 || sales_amt<sales_amt2) {
		concat_msg="Total Sales in file : "+ sales_amt +" \r\nTotal Sales upload : "+ sales_amt2 +" \r\nTotal Sales in file do not match the total sales upload...\r\n\r\n";
	}
	if (returns_amt>returns_amt2 || returns_amt<returns_amt2) {
		concat_msg=concat_msg + "Total Returns in file : "+ returns_amt +" \r\nTotal Returns upload : "+ returns_amt2 +" \r\nTotal Returns in file do not match the total Returns upload...\r\n\r\n";
	}
	if (void_amt>void_amt2 || void_amt<void_amt2) {
		concat_msg=concat_msg + "Total Void in file : "+ void_amt +" \r\nTotal Void upload : "+ void_amt2 +" \r\nTotal Void in file do not match the total Void upload...\r\n\r\n";
	}
	concat_msg = concat_msg + "Do you want to continue to update this sales in the database?";
	if (concat_msg>"") {
		var message = confirm(concat_msg);
		if (!message) {
			alert("no sales record to be updated...");
			return false;
		}
	}

	document.form_search.hide_save.value = "save_records";
	document.form_search.submit();
}
</script>
<script language="JavaScript" type="text/JavaScript">
<!--
function MM_reloadPage(init) {  //reloads the window if Nav4 resized
  if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
    document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
  else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
}
MM_reloadPage(true);
//-->
</script>
</head>

<body>
<form action="<?$_SERVER["PHP_SELF"];?>" method="post" enctype="multipart/form-data" name="form_search">
  <div id="Layer2" style="position:absolute; left:8px; top:11px; width:98%; height:99px; z-index:2;"> 
    <table width="596" border="0" align="center" >
      <tr nowrap="wrap"  bgcolor="#DEEDD1"> 
        <th width="92" height="26" nowrap="nowrap" class="style6"><div align="left"><font size="2" face="Arial, Helvetica, sans-serif">File 
            Name</font></div></th>
        <th width="237" nowrap="nowrap" class="style6"><div align="left"><font size="2" face="Arial, Helvetica, sans-serif"> 
            <input name="file_name" type="file" id="file_name5" onClick="val_file_date();">
            </font></div></th>
        <th width="253" height="26" nowrap="nowrap" bgcolor="#DEEDD1" class="style6"><div align="left"><font size="2" face="Arial, Helvetica, sans-serif">Company 
            <span class="style3">
            <select name="cmbCompany" disabled="disabled" id="cmbCompany">
              <?
							 	if ($cmbCompany=="") {
									$cmbCompany=$company_code;
									$cmbCompanyName = $etcTrans->getCompanyName($cmbCompany);
								} else {
									$cmbCompanyName = $etcTrans->getCompanyName($cmbCompany);
								}
								echo "<option selected value='$cmbCompany'>
									  $cmbCompanyName
									  </option>";
								$query_company="SELECT * FROM tblCompany ORDER BY compName ASC";
								$result_company=mssql_query($query_company);
								$num_company = mssql_num_rows($result_company);
								for ($i=0;$i < $num_company;$i++){
									$cmb_compcode=mssql_result($result_company,$i,"compCode");								
									$cmb_compname=mssql_result($result_company,$i,"compName");
									echo "<option value='$cmb_compcode'>$cmb_compname</option>";
								}
							?>
            </select>
            </span> </font></div></th>
      </tr>
      <tr nowrap="wrap"  bgcolor="#DEEDD1"> 
        <th height="26" nowrap="nowrap" class="style6"><div align="left"><font size="2" face="Arial, Helvetica, sans-serif">Sales 
            Date</font></div></th>
        <th nowrap="nowrap" class="style6"><div align="left"><font size="2" face="Arial, Helvetica, sans-serif"> 
            <input name="from_date" type="text" id="from_date8"   onBlur="val_date();" value="<? echo $from_date?>" size="10" >
            <a href="javascript:void(0)" onClick="showCalendar(form_search.from_date,'mm/dd/yyyy','Choose date')"><img src="../../functions/inquiry_reports/CAL-icon.gif" border="0" width="16" height="16" alt="Click Here to use a calendar"></a> 
            </font></div></th>
        <th width="253" height="26" nowrap="nowrap" bgcolor="#DEEDD1" class="style6"><div align="left">Location<font size="2" face="Arial, Helvetica, sans-serif">
            <select name="from_location" id="select" style="width:100px; height:20px;" onChange="val_sales_returns_void_amt();">
              <option selected><? echo $from_location;  ?></option>
              <?
		  	$query_from_location="SELECT * FROM tblLocation WHERE compCode = $company_code AND locType = 'S' ORDER BY locCode ASC";
			$result_from_location=mssql_query($query_from_location);
			$num_from_location = mssql_num_rows($result_from_location);	
			for ($i=0;$i<$num_from_location;$i++){  
					$from_loc_code=mssql_result($result_from_location,$i,"locCode"); 
					$from_loc_name=mssql_result($result_from_location,$i,"locName"); 
				?>
              <option><? echo $from_loc_code."-".$from_loc_name; ?></option>
              <? } ?>
            </select>
            </font></div></th>
      </tr>
      <tr nowrap="wrap"  bgcolor="#DEEDD1"> 
        <th width="92" height="26" rowspan="3" nowrap="nowrap" bgcolor="#DEEDD1" class="style6"> 
          <div align="left"><font size="2" face="Arial, Helvetica, sans-serif">Control 
            Totals </font></div></th>
        <th nowrap="nowrap" class="style6"><div align="right"><font size="2" face="Arial, Helvetica, sans-serif">Sales 
            Amt 
            <?
			if (!$sales_amt) {
				$sales_amt="0.00";
			}
			if (!$returns_amt) {
				$returns_amt="0.00";
			}
			if (!$void_amt) {
				$void_amt="0.00";
			}
			?>
            <input name="sales_amt" style="text-align: right;" type="text" value="<? echo $sales_amt; ?>" id="sales_amt" onChange="val_sales_returns_void_amt();">
            </font></div></th>
        <th width="253" height="2" rowspan="3" nowrap="nowrap" bgcolor="#DEEDD1" class="style6"><font size="2" face="Arial, Helvetica, sans-serif"> 
          <input name='explode_data' type='submit' disabled="true" class='queryButton' id='explode_data3' title='Display the Inventory Transaction' onClick="val_all();" value='Import Data'/>
          </font></th>
      </tr>
      <tr nowrap="wrap"  bgcolor="#DEEDD1"> 
        <th height="5" nowrap="nowrap" class="style6"><div align="right"><font size="2" face="Arial, Helvetica, sans-serif"> 
            Returns Amt 
            <input name="returns_amt" style="text-align: right;" type="text" value="<? echo $returns_amt; ?>" id="returns_amt" onChange="val_sales_returns_void_amt();">
            </font></div></th>
      </tr>
      <tr nowrap="wrap"  bgcolor="#DEEDD1"> 
        <th width="237" height="13" nowrap="nowrap" class="style6"><div align="right"><font size="2" face="Arial, Helvetica, sans-serif">Void 
            Amt 
            <input name="void_amt" style="text-align: right;" type="text" value="<? echo $void_amt; ?>" id="void_amt" onChange="val_sales_returns_void_amt();">
            </font></div></th>
      </tr>
    </table>
    
  </div>
  <p>&nbsp;</p>
  <p>&nbsp;</p>
  <p>&nbsp;</p>
  <p>&nbsp;</p>
  <p>&nbsp;</p>
  <p>&nbsp;</p>
  <div id="Layer1" style="position:absolute; left:10px; top:150px; width:98%; height:270px; z-index:1; overflow: auto; background-color: #F0F0F0; layer-background-color: #F0F0F0; border: 1px none #000000;"> 
    <strong><font size="2" face="Arial, Helvetica, sans-serif"> </font></strong> 
    <table width="100%" id="theTable" border="0" align="center">
      <tr nowrap="wrap" bgcolor="#6AB5FF"> 
        <td><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">Rec 
            Code </font></strong></div></td>
        <td><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">Sto 
            Code</font></strong></div></td>
        <td><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">Term 
            Code</font></strong></div></td>
        <td><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">Tran 
            Code</font></strong></div></td>
        <td bgcolor="#6AB5FF"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">UPC</font></strong></div></td>
        <td><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">Qty</font></strong></div></td>
        <td><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">Unit 
            Price</font></strong></div></td>
        <td><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">Ext 
            Amt 
            <? 
				$total_error=0;
				$total_sales=0;
				$total_returns=0;
				$total_void=0;
				for ($i=0;$i<$array_count;$i++){ 
					
					
					$array2 = split (" ", $array[$i]);
					$grid0=$array2[0]; //rec
					$grid1=$array2[1]; //store
					$grid2=$array2[2]; //term
					$grid3=$array2[3]; //trans
					$grid7=$array2[7]; //sku
					$grid8=$array2[8]; //qty
					$grid9=$array2[9]; //up
					
					
					$new_grid7=$grid7[10].$grid7[11].$grid7[12].$grid7[13].$grid7[14].$grid7[15].$grid7[16].$grid7[17].$grid7[18].$grid7[19].$grid7[20].$grid7[21].$grid7[22];
					$qryUpc = mssql_query("SELECT * FROM tblUpc WHERE upcCode = '$new_grid7'");
					$num_upc = mssql_num_rows($qryUpc);
					if ($num_upc>0) {
							##################################### record code validation
							if ($grid0!="\"S\"" && $grid0!="\"R\"" && $grid0!="\"V\"") {
								$total_error=$total_error+1;
								if ($total_error<2) {
									$error="";
									$error="<<< First Error Found : Record Code $grid0 is not valid >>>";
								}
							}
							
							##################################### store code validation
							if (!is_numeric($grid1) || $grid1<1 || strlen($grid1)>2) {
								$total_error=$total_error+1;
								if ($total_error<2) {
									$error="";
									$error="<<< First Error Found : Store Code $grid1 is not valid >>>";
								}
							}
							##################################### terminal code validation
							if (!is_numeric($grid2) || $grid2<1 || strlen($grid2)>4) {
								$total_error=$total_error+1;
								if ($total_error<2) {
									$error="";
									$error="<<< First Error Found : Terminal Code $grid2 is not valid >>>";
								}
							}
							##################################### transaction code validation
							if (!is_numeric($grid3) || $grid3<1 || strlen($grid3)>4) {
								$total_error=$total_error+1;
								if ($total_error<2) {
									$error="";
									$error="<<< First Error Found : Transaction Code $grid3 is not valid >>>";
								}
							}
							##################################### upc Code validation
							$new_grid7="";
							$grid7_split=split("\"",$grid7);
							$new_grid7=$grid7[10].$grid7[11].$grid7[12].$grid7[13].$grid7[14].$grid7[15].$grid7[16].$grid7[17].$grid7[18].$grid7[19].$grid7[20].$grid7[21].$grid7[22];
							
							if (!is_numeric($grid7_split[1]) || ($grid7_split[1]<1) || strlen($grid7_split[1])>22) {
								$total_error=$total_error+1;
								if ($total_error<2) {
									$error="";
									$error="<<< First Error Found : UPC Code $grid7 is not valid >>>";
								}
							}
							##################################### quantity validation
							$grid8_split = $grid8[0].$grid8[1].$grid8[2].$grid8[3].$grid8[4].$grid8[5].".".$grid8[6].$grid8[7];
							if (!is_numeric($grid8) || ($grid8<1) || strlen($grid8)>8) {
								$total_error=$total_error+1;
								if ($total_error<2) {
									$error="";
									$error="<<< First Error Found : Quantity $grid8 is not valid >>>";
								}
							}
							##################################### unit price validation
							if (!is_numeric($grid9) || ($grid9<1) || strlen($grid9)>8) {
								$total_error=$total_error+1;
								if ($total_error<2) {
									$error="";
									$error="<<< First Error Found : Unit Price $grid9 is not valid >>>";
								}
							}
							##################################### extended amount computation
							$grid9_split = $grid9[0].$grid9[1].$grid9[2].$grid9[3].$grid9[4].$grid9[5].".".$grid9[6].$grid9[7];
							$grid_x_amt = $grid8_split * $grid9_split;
							$grid_x_amt = number_format($grid_x_amt,2);
							
							if ($grid0=="\"S\"") {
								$total_sales=$total_sales+$grid_x_amt;
							}
							if ($grid0=="\"R\"") {
								$total_returns=$total_returns+$grid_x_amt;
							}
							if ($grid0=="\"V\"") {
								$total_void=$total_void+$grid_x_amt;
							}
					} 
					
			?>
            </font></strong></div></td>
        <td width="10"><strong><font size="2" face="Arial, Helvetica, sans-serif"> 
          </font></strong></td>
      </tr>
      <tr> 
        <td width="70" bgcolor="#DEEDD1"><div align="center"><font size="2"><? echo $grid0; ?></font></div></td>
        <td width="70" bgcolor="#DEEDD1"><div align="center"><font size="2"><? echo $from_location; ?></font></div></td>
        <td width="70" bgcolor="#DEEDD1"><div align="center"><font size="2"><? echo $grid2; ?></font></div></td>
        <td width="70" bgcolor="#DEEDD1"><div align="center"><font size="2"><? echo $grid3; ?></font></div></td>
        <td width="154" bgcolor="#DEEDD1"><div align="center"><font size="2"><? echo $new_grid7; ?></font></div></td>
        <td width="100" bgcolor="#DEEDD1"><div align="right"><font size="2"><? echo $grid8_split; ?></font></div></td>
        <td width="102" bgcolor="#DEEDD1"><div align="right"><font size="2"><? echo $grid9_split; ?></font></div></td>
        <td width="100" bgcolor="#DEEDD1"><div align="right"><font size="2"><? echo $grid_x_amt; }?></font></div></td>
      </tr>
      <tr> 
        <td colspan="10"><input name="hidden_local" type="hidden" id="hidden_local2" value="<? echo $local_file2; ?>"></td>
      </tr>
      <tr> 
        <td colspan="10"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif"> 
            </font></strong></div></td>
      </tr>
      <tr> 
        <td colspan="10"><div align="center"></div>
          <div align="center"></div>
          <div align="center"></div>
          <div align="center"></div>
          <div align="center"></div>
          <div align="center"></div>
          <div align="left"></div>
          <div align="right"></div>
          <div align="right"></div>
          <div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif"> 
            </font></strong></div></td>
      </tr>
    </table>
  </div>
  <p>&nbsp;</p>
  <p>&nbsp;</p>
  <p>&nbsp;</p>
  <p>&nbsp;</p>
  <p>&nbsp;</p>
  <div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif"> 
    </font></strong> 
    <div id="Layer2" style="position:absolute; left:10px; top:423px; width:98%; height:99px; z-index:2;">
      <table width="100%" border="0" align="center" >
        <tr nowrap="wrap"  bgcolor="#DEEDD1"> 
          <th width="169" nowrap="nowrap" class="style6"><div align="left"><font color="#000000" size="2" face="Arial, Helvetica, sans-serif">Total 
              Records in the POS</font></div></th>
          <th width="22" height="22" nowrap="nowrap" class="style6"><div align="left"><font color="#000000" size="2"> 
              <? 
																							if ($array_count == "") {
																								$array_count=0;
																							}
																							echo $array_count; 
																						?>
              </font></div></th>
          <th width="122" nowrap="nowrap" class="style6"><div align="left"><font color="#000000">Total 
              Error/s</font></div></th>
          <th width="27" nowrap="nowrap" class="style6"><div align="left"><font color="#FF0000"><font size="2"> 
              <?  
				echo $total_error; 
			?>
              </font></font></div></th>
          <th width="140" rowspan="3" nowrap="nowrap" class="style6"><div align="right"><font color="#000000">Upload 
              </font><font color="#000000">Control Totals </font></div></th>
          <th width="200" nowrap="nowrap" class="style6"><div align="right"><font color="#000000"><font size="2" face="Arial, Helvetica, sans-serif">Sales 
              Amt</font><font color="#000000"><font size="2" face="Arial, Helvetica, sans-serif"> 
              <?
			  	$total_sales=number_format($total_sales,2);
				$total_returns=number_format($total_returns,2);
				$total_void=number_format($total_void,2);
			  ?>
              <input name="sales_amt2" style="text-align: right;" type="text" id="sales_amt22" onChange="val_sales_returns_void_amt();" value="<? echo $total_sales; ?>" readonly="true">
              </font></font></font></div></th>
        </tr>
        <tr nowrap="wrap"  bgcolor="#DEEDD1"> 
          <th width="169" nowrap="nowrap" class="style6"><div align="left"><font color="#000000" size="2" face="Arial, Helvetica, sans-serif">Total 
              Records Upload</font></div></th>
          <th width="22" height="20" nowrap="nowrap" class="style6"><div align="left"><font color="#000000" size="2"><? echo $i; ?> 
              </font></div></th>
          <th height="20" colspan="2" rowspan="2" nowrap="nowrap" class="style6"><div align="left"><font color="#000000" size="2" face="Arial, Helvetica, sans-serif"></font><font color="#000000" size="2"> 
              <?  
				if ($error=="") {
					$error="";
				} 
				echo $error; 
			?>
              </font></div></th>
          <th width="229" nowrap="nowrap" class="style6"><div align="right"><font color="#000000" size="2" face="Arial, Helvetica, sans-serif">Returns 
              Amt 
              <input name="returns_amt2" style="text-align: right;" type="text" id="returns_amt22" onChange="val_sales_returns_void_amt();" value="<? echo $total_returns; ?>" readonly="true">
              </font></div></th>
        </tr>
        <tr nowrap="wrap"  bgcolor="#DEEDD1"> 
          <th width="169" nowrap="nowrap" class="style6"><div align="left"><font color="#000000" size="2" face="Arial, Helvetica, sans-serif">Difference</font></div></th>
          <th width="22" height="20" nowrap="nowrap" class="style6"><div align="left"><font color="#FF0000" size="2"> 
              <? 
																										$diff=$array_count- $i;
																										echo $diff; 
																									?>
              </font></div></th>
          <th width="229" nowrap="nowrap" class="style6"><div align="right"><font color="#000000" size="2" face="Arial, Helvetica, sans-serif">Void 
              Amt 
              <input name="void_amt2" style="text-align: right;" type="text" id="void_amt22" onChange="val_sales_returns_void_amt();" value="<? echo $total_void; ?>" readonly="true">
              </font></div></th>
        </tr>
      </table>
      <div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif"> 
        <?
		  if (($total_error<1) && ($array_count>"")) {
				$meron="";
				$msg="Processing data.... Please wait";
			} else {
				$meron="disabled=\"true\"";
				$msg="";
			}
		  ?>
        <input name='update' type='button' class='queryButton' id='update2' title='Display the Inventory Transaction' onClick="val_totals();" value='Update to Sales' <? echo $meron; ?>/>
        <input name="hide_save" type="hidden" id="hide_save">
        </font></strong></div>
    </div>
    <strong><font size="2" face="Arial, Helvetica, sans-serif"> 
    </font></strong> </div>
</form>
</body>
</html>
