<?
session_start();
include("../../includes/db.inc.php");
include("../../includes/common.php");
include("maintenanceObj.php");
$maintObj = new maintenanceObj;



?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Rentables</title>

<style type="text/css" title="currentStyle">
			@import "../../includes/css/demo_page.css" "";
			@import "../../includes/css/demo_table_jui.css";
</style>
<link type="text/css" href="../../includes/jquery/css/redmond/jquery-ui-1.8.16.custom.css" rel="stylesheet" />
<link type="text/css" href="../../includes/jquery/development-bundle/demos/demos.css" rel="stylesheet" />
<link type="text/css" href="../../includes/jqGrid/css/ui.jqgrid.css"rel="stylesheet" />
<script type="text/javascript" src="../../includes/jquery/js/jquery-1.6.2.min.js"></script>
<script type="text/javascript" src="../../includes/jquery/js/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="../../includes/jqGrid/js/i18n/grid.locale-en.js"></script>
<script type="text/javascript" src="../../includes/jqGrid/js/jquery.jqGrid.min.js"></script>
<script type="text/javascript" src="../../includes/js/jquery.dataTables.min.js"></script>


<script type="text/javascript">

	$(function(){
		$(function(){
			$("#btnSave").button();
			$('#startDate,#endDate').datepicker();
		});
	
		$("#btnViewDtl").button({
			icons: {
				primary: 'ui-icon-print',
			}
		});
		$("#btnRefresh,#btnSave").button();
	});
	
	function viewDtl(){
		if (validateInputs2()) {
			$.ajax({
				url: "rentableMaintenanceDtl.php",
				type: "GET",
				traditional: true,
				data: '&txtRefNo='+txtRefNo+'&action=getBrandDtl',
				success: function(msg){
					//eval(msg);
					//reloadGrid2();
					$("#rentableDtl").html(msg);
				}	
			});
		}
	}
	function validateInputs2() {
		var val = true;
		/*if (validateFixedString($("#cmbSpecs"),0)== false)
			val = false;*/
		if (validateFixedString($("#cmbStore"),0)== false)
			val = false;
		
		return val;
	}
	function validateFixedString(ObjName,errvalue){
		if(ObjName.val()== errvalue){
			ObjName.addClass("ui-state-error");
			dialogAlert("Required Field!");
			return false;
		}
		else{
			ObjName.removeClass("ui-state-error");
			return true;
		}
	}	
	function dialogAlert(msg){
		$("#dialogAlert").dialog("destroy");
		$("#dialogMsg").html(msg);
		$("#dialogAlert").dialog({
			modal: true,
			buttons: {
					Ok: function() {
						$(this).dialog('close');
					}
				}
			});	
	}
	function emptyDiv(){
		$('#rentableDtl').empty();
	}
	function searchReference(){
		if(validateRefNo()){
			$.ajax({
				url: "rentableMaintenanceDtl.php",
				type: "GET",
				traditional: true,
				data: 'action=view&refNo='+$("#txtRefNo").val(),
				success: function(msg){
					$("#rentableDtl").html(msg);
				}				
			});
		}
	}
	function validateRefNo() {
		var val = true;
		if (validateString($("#txtRefNo"))== false){
			dialogAlert("Required Field!");		
			val = false;	
		}
		return val;
	}
	function valNumInputDec(event) {
		var key, keyChar;
	  	if (window.event)
			key = window.event.keyCode;
	  	else if (event)
			key = event.which;
	  	else
			return true;
	  	// Check for special characters like backspace
	  	if (key == null || key == 0 || key == 8 || key == 13 || key == 27)
			return true;
	  	// Check to see if it's a number
	  	keyChar =  String.fromCharCode(key);
	  	if ((/\d/.test(keyChar)) || (/\./.test(keyChar))) {
			window.status = "";
			return true;
		} 
		else {
			window.status = "Field accepts numbers only.";
			return false;
	   	}
	}
	function validateString(ObjName){
		if(ObjName.val().length == 0){
			ObjName.addClass("ui-state-error");
			return false;
		}
		else{
			ObjName.removeClass("ui-state-error");
			return true;
		}
	}
</script>
<style type="text/css">
.textBox {
	border: solid 1px #222; 
	border-width: 1px; 
	width:190px; 
	height:18px;
	font-size: 11px;
}
.textBoxLong {
	border: solid 1px #222; 
	border-width: 1px; 
	width:527px; 
	height:18px;
	font-size: 11px;
}
.textBoxMin {
	border: solid 1px #222; 
	border-width: 1px; 
	width:130px; 
	height:18px;
	font-size: 11px;
}
.headerContent {
	height: 220px;
	border: solid 1px #222;
	padding: 1px;
	background-color:#E9E9E9;
}	
.detailContent {
	height: 50px;
	border: solid 1px #222;
	padding: 3px;
	background-color:#E9E9E9;
}	
.selectBox {
	border: 1px solid #222; 
	width:192px; 
	height:22px;
	font-size: 11px;
}
.selectBox2 {
	border: 1px solid #222; 
	width:120px; 
	height:22px;
	font-size: 11px;
}

.selectBoxMin {
	border: 1px solid #222; 
	width:100px; 
	height:22px;
	font-size: 11px;

}
.errMsg {
	padding:5px;
	height:30px;
	text-align:center;
}
.disable {
	color:#CCCCCC;
	font-size:11px; 
	cursor:default;
}
.link {
	cursor: pointer; 
	font-size:11px;
	font-weight: bolder; 
	color:#fff;
}
.link2 {
	cursor: pointer; 
	font-size:11px;
	font-weight: bolder; 
	color:#09F;
}
.hd {
	font-size: 11px;
	font-family: Verdana;
	font-weight: bold;
}
.hd2 {
	font-size:11px;
	font-family: Verdana;
}
.style20 {
	font-size: 14px;
	font-weight: bold;
	font-family: "Trebuchet MS";
	color: #EEEEEE;
}
.style21 {
	font-size: 14px;
	font-family: "Trebuchet MS";
	color: #EEEEEE;
}
.style22 {color: #EEEEEE; background-color:#EEEEEE}
.style23 {font-size:11px; cursor: pointer;}

body {
	background-color: #56789a;
}
.style24 {color: #000000}


-->
.dataTables_wrapper {
	
	min-height: 190px;
	_height: 190px;
	clear: both;
}
</style>
</head>

<body>

<h2 class=" ui-widget-header">STORE RENTABLES ADJUSTMENTS</h2>

<div class="ui-widget-content" style="padding:5px;">
	<table width="80%" border="0" cellspacing="0" cellpadding="0">
            <tr> 
               <td class="hd">Reference Number:</td>
               <td><input type="text" name="txtRefNo" id="txtRefNo" style="height:11px; text-align:right; width:70px;" class="ui-corner-all" onKeyPress='return valNumInputDec(event);' onKeyDown="if(event.keyCode==13) searchReference();"/></td>
               <td class="hd">Implementation Start Date:</td>
               <td><input type="text" name="startDate" id="startDate" style="height:11px; text-align:right; width:100px;" class="ui-corner-all"  readonly="readonly"/></td>
               <td class="hd">Implementation End Date:</td>
               <td><input type="text" name="endDate" id="endDate" style="height:11px; text-align:right; width:100px;" class="ui-corner-all"  readonly="readonly"/></td>
               <td align="center"><button id="btnViewDtl" onclick="searchReference();">VIEW</button> </td>
               <td align="center"><button id="btnRefresh" onClick="document.location.reload();" >REFRESH</button> </td>
            </tr>
          </table>
     <hr />
     
    <div id="rentableDtl">
    	
    </div>
    <div id='dialogAlert' title='STS'>
            <p id='dialogMsg'></p>
    	</div>
</div>
</body>
</html>