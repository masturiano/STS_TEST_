<?
include "../../functions/inquiry_session.php";
require_once "../../includes/config.php";
require_once "../../functions/db_function.php";
require_once "../../functions/inquiry_function2.php";
$db = new DB;
$db->connect();

function view_date() {
		include "../../functions/inquiry_session.php";
		$today = date("m/d/Y");
		$from_date=$_POST['from_date'];
		$to_date=$_POST['to_date'];
		if ($from_date =="") {
			$from_date=$today;
			$to_date=$today;
		}
			
		echo "From Date 
        <input name='from_date' type='text' id='from_date5'   onBlur='val_date();' value='$from_date' size='10' readonly='true'>
        <a href='javascript:void(0)' onClick='showCalendar(form_search.from_date,\"mm/dd/yyyy\",\"Choose date\")'><img src='../../functions/inquiry_reports/CAL-icon.gif' border='0' width='16' height='16' alt='Click Here to use a calendar'></a> 
        To Date 
        <input name='to_date' type='text' id='to_date5' onBlur='val_date();' value='$to_date' size='10' readonly='true'>
        <a href='javascript:void(0)' onClick='showCalendar(form_search.to_date,\"mm/dd/yyyy\",\"Choose date\")'><img src='../../functions/inquiry_reports/CAL-icon.gif' border='0' width='16' height='16' alt='Click Here to use a calendar'></a> 
        <input name='view_transfer' type='button' class='queryButton' id='view_transfer5' title='Display the Inventory Transaction' value='Search' onKeyDown='' onClick='val_all();'/>";
}

function view_record() {
		include "../../functions/inquiry_session.php";
		require_once "../../includes/config.php";
		require_once "../../functions/db_function.php";
		require_once "../../functions/inquiry_function2.php";
		$gmt = time() + (8 * 60 * 60);
		$date = date("m-d-Y", $gmt);
		$today = date("m/d/Y");
		$db = new DB;
		$db->connect();
		$from_date=$_POST['from_date'];
		$to_date=$_POST['to_date'];
		if ($from_date =="") {
			$from_date=$today;
			$to_date=$today;
		}
		
		echo "<table width='754' border='0' align='center' cellpadding='0' cellspacing='0'>
		  	  <tr> 
			  <td colspan='6'><div align='right'><strong><font size='2' face='Arial, Helvetica, sans-serif'> ";
					if ($from_date=="") {
						$query_date = "";
					} else {
						$query_date = " AND (trfDate >= '$from_date') AND (trfDate <= '$to_date')";
					}
					############################# dont forget to get the company code ##################################
					$query_trans_header="SELECT *
						FROM tblTransferHeader 
						WHERE (compCode = $company_code) $query_date
						ORDER BY trfNumber DESC";
					$result_trans_header=mssql_query($query_trans_header);
					$num_trans_header = mssql_num_rows($result_trans_header);
					for ($i=0;$i<$num_trans_header;$i++){ 
						$grid_transfer_no=mssql_result($result_trans_header,$i,"trfNumber");
						$grid_date=mssql_result($result_trans_header,$i,"trfDate");
						$grid_from_loc_code=mssql_result($result_trans_header,$i,"fromLocCode");
						$grid_to_loc_code=mssql_result($result_trans_header,$i,"toLocCode");
						$stock_tag=mssql_result($result_trans_header,$i,"stockTag");
						$status=mssql_result($result_trans_header,$i,"trfStatus");
						if ($status=="R") {
							$status="Released";
						} else {
							$status="Open";
						}
						$query_from_loc="SELECT *
							FROM tblLocation 
							WHERE locCode = $grid_from_loc_code";
						$result_from_loc=mssql_query($query_from_loc);
						$grid_from_loc=mssql_result($result_from_loc,0,"locName");
						$grid_from_loc=$grid_from_loc_code." - ".$grid_from_loc;
						$query_from_loc="SELECT *
							FROM tblLocation 
							WHERE locCode = $grid_to_loc_code";
						$result_from_loc=mssql_query($query_from_loc);
						$grid_to_loc=mssql_result($result_from_loc,0,"locName");
						$grid_to_loc=$grid_to_loc_code." - ".$grid_to_loc;
						if ($stock_tag>1) {
							$grid_stock_tag="BO Stocks";
						} else {
							$grid_stock_tag="Good Stocks";
						}
				echo "<input name='hide_num' type='hidden' id='hide_num' value='$num_trans_header'>
					  </font></strong></div></td>
		  			  </tr>
		  			  <tr> 
					  <td width='100'><div align='center'> <strong><font size='2' face='Arial, Helvetica, sans-serif'> 
					  </font></strong> 
					  <input name='check$i' type='checkbox'  onClick='enabled_print_button(this.checked);' value='$grid_transfer_no'>
			  		  </div></td>
					  <td width='100'><font size='2'>$grid_transfer_no</font><font size='2' face='Arial, Helvetica, sans-serif'><span class='style20'><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><span class='style18'><b><b><span class='style1'><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b> 
			  		  </b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></b></b></span></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></font></td>
					  <td width='100'><font size='2'>";
			  	$date = new DateTime($grid_date);
				$grid_date = $date->format('m-d-Y');
				echo $grid_date; 
			  	echo "</font></td>
					  <td width='100'><font size='2'>$grid_from_loc</font></td>
					  <td width='100'><font size='2'>$grid_to_loc</font></td>
				      <td width='100'><font size='2'>$grid_stock_tag</font></td>
					  <td width='100'><font size='2'>$status</font></td>";
		}		
		echo "</tr>
			  </table>";
}

function hide_record() {
		include "../../functions/inquiry_session.php";
		require_once "../../includes/config.php";
		require_once "../../functions/db_function.php";
		require_once "../../functions/inquiry_function2.php";
		$gmt = time() + (8 * 60 * 60);
		$date = date("m-d-Y", $gmt);
		$today = date("m/d/Y");
		$db = new DB;
		$db->connect();
		
		$today = date("m/d/Y");
		$from_date=$_POST['from_date'];
		$to_date=$_POST['to_date'];
		if ($from_date =="") {
			$from_date=$today;
			$to_date=$today;
		}
		
		if ($from_date=="") {
			$query_date = "";
		} else {
			$query_date = " AND (trfDate >= '$from_date') AND (trfDate <= '$to_date')";
		}
		############################# dont forget to get the company code ##################################
		$query_trans_header="SELECT *
			FROM tblTransferHeader 
			WHERE (compCode = $company_code) $query_date
			ORDER BY trfNumber DESC";
		$result_trans_header=mssql_query($query_trans_header);
		$num_trans_header = mssql_num_rows($result_trans_header);

		echo "<input name='hide_from_date' type='hidden' id='hide_from_date3' value='$from_date'>
			  <input name='hide_to_date' type='hidden' id='hide_to_date2' value='$to_date'>
			  <input name='hide_num_trans_header' type='hidden' id='hide_to_date3' value='$num_trans_header'>";
}
?>

<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script type='text/javascript' src='../../functions/inquiry_reports/calendar.js'></script>
<script type="text/javascript">
function val_trans_no() {
	var numeric_expression = /^(\d+\.\d{0,4}|\d+)$/;
	var transfer_no = document.form_search.transfer_no.value;
	if (transfer_no!="*") {
		if(!transfer_no.match(numeric_expression)) {
			alert("Transfer No: Numbers only");
			document.form_search.transfer_no.value="";
			document.form_search.transfer_no.focus();
			return false;
		} 
	}
}

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

function val_from_loc(complete_name) {
	var from_loc = document.form_search.from_loc.value;
	var to_loc = document.form_search.to_loc.value;
	if(from_loc==to_loc) {
		alert("From Location must not be equal to To Location.");
		document.form_search.from_loc.value="";
		document.form_search.from_loc.focus();
		return false;
	} 
	tempstr=complete_name.split("-");
   	get_code=tempstr[0];
   	document.form_search.hide_from_loc.value = get_code;
}

function val_to_loc(complete_name) {
	var from_loc = document.form_search.from_loc.value;
	var to_loc = document.form_search.to_loc.value;
	if(from_loc==to_loc) {
		alert("From Location must not be equal to To Location.");
		document.form_search.to_loc.value="";
		document.form_search.to_loc.focus();
		return false;
	} 
	tempstr=complete_name.split("-");
   	get_code=tempstr[0];
   	document.form_search.hide_to_loc.value = get_code;
}

function val_all() {
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

	if((from_date=="") && (to_date=="")) { 
		
	} else {
		if(from_date=="" || to_date=="") {
			alert("Key-in From Date or To Date.");
			document.form_search.from_Date.focus();
			return false;
		}
	}
	document.form_search.submit();
}
function enabled_print_button(selected) {
		if (selected == true) {
			document.form_print.print_transfer.disabled=false;
		}
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
<fieldset>
<legend>Search by Date</legend>
<form name="form_search" method="post" action="transfer_slip.php">
  <table width="561" border="0" align="left" cellpadding="0" cellspacing="0">
    <tr> 
      <td width="392"><font size="2" face="Arial, Helvetica, sans-serif">
	  <? view_date(); ?>
	  </font></td>
    </tr>
  </table>
</form>
</fieldset>
<form action="transfer_slip_pdf.php" method="post" name="form_print" target="_blank" id="form_print">
  <div id="Layer2" style="position:absolute; left:10px; top:88px; width:98%; height:30px; z-index:2"> 
    <table width="734" border="0" align="center">
      <tr bgcolor="#6AB5FF"> 
        <td width="85"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif"> 
            <input name='print_transfer' type='submit' disabled="true" class='queryButton' id='print_transfer23' title='Display the Inventory Transaction' onClick="onClick='document.form_print.submit();'" value='Print'/>
            <!--<a href="javascript:void(0)" onClick="document.form_print.submit();"><img src="../../functions/inquiry_reports/pdf.jpg" alt="Click Here to print in PDF Format."></a> -->
            </font></strong></div></td>
        <td width="102"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">Transfer 
            No </font></strong></div></td>
        <td width="113"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">Date</font></strong></div></td>
        <td width="100"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">From 
            Location</font></strong></div></td>
        <td width="100"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">To 
            Location</font></strong></div></td>
        <td width="100"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">Stock 
            Desc </font></strong></div></td>
        <td width="104"><div align="center"><strong>Status</strong></div></td>
      </tr>
    </table>
  </div>
  <div id="Layer1" style="position:absolute; left:10px; top:128px; width:98%; height:350px; z-index:1; overflow: auto;"> 
    <strong><font size="2" face="Arial, Helvetica, sans-serif"> </font></strong> 
    <? view_record(); ?>
  </div>
  <font size="2" face="Arial, Helvetica, sans-serif"><span class="style20"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><span class="style18"><b><b><span class="style1"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b> 
   <? hide_record(); ?>
  </b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></b></b></span></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></font> 
</form>
</body>
</html>
