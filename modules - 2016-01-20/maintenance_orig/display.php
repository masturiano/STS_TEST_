<?
session_start();
include("../../includes/db.inc.php");
include("../../includes/common.php");
include("maintenanceObj.php");
$maintObj = new maintenanceObj;


$now = date('Y-m-d H:i:s');
if ($_SESSION['sts-username'] == "") {
	echo "<SCRIPT>window.parent.location.href='../../index.php';</SCRIPT>";
}
switch($_GET['action']){	
	case 'Load':
		$page = $_GET['page']; // get the requested page 
		$limit = $_GET['rows']; // get how many rows we want to have into the grid 
		$sidx = $_GET['sidx']; // get index row - i.e. user click to sort 
		$sord = $_GET['sord']; // get the direction 
		if(!$sidx) $sidx =1; 
		
		$totUser = $maintObj->countDisplay();
		$count = $totUser['count']; 
		if( (int)$count>0 ) { 
			$total_pages = ceil($count/$limit); 
		} else { 
			$total_pages = 0; 
		} 
		
		if ($page > $total_pages) 
			$page=$total_pages; 
		$start = ($limit*$page) - ($limit); // do not put $limit*($page - 1) 
		
		$response->page = $page; 
		$response->total = $total_pages; 
		$response->records = $count; 
		
		if($_GET['_search']=='true'){
			$arrSTS= $maintObj->searchDisplayType($sidx,$sord,$start,$limit,$_GET['searchField'],$_GET['searchString']);
		}else{
			$arrSTS = $maintObj->getPaginatedDisplayType($sidx,$sord,$start,$limit);
		}
		$i = 0;
		foreach($arrSTS as $val){
			$response->rows[$i]['id']=$val['displaySpecsId']; 
			$response->rows[$i]['cell']=array($val['displaySpecsId'],$val['displaySpecsDesc'],$val['stat']);
			$i++;
		}
		echo json_encode($response);
	exit();
	break;
	
	case 'AddBrand':
		if($maintObj->checkIfSpecExists($_GET['txtBrand']) == 0){
			if($maintObj->addSpecs($_GET)){
				echo "$('#dialogAdd').dialog('close');\n";
				echo "dialogAlert('Brand succesfully added!');";
			}else{
				echo "dialogAlert('There was an error adding a new Brand!');";	
			}
		}else{
			echo "dialogAlert('Brand already exists!');";
		}
	exit();
	break;
	
	case 'getInfo':
			$arrInfo = $maintObj->specsInfo($_GET['id']);
			echo "$('#txtBrand').val('{$arrInfo['displaySpecsDesc']}');\n";
			echo "$('#txtAmount').val('{$arrInfo['specsAmount']}');\n";

	exit();
	break;
	
	case 'updateBrand':
		if($maintObj->checkIfSpecsExistsWId($_GET['txtBrand'],$_GET['hdnBrandId']) == 0){
			if ($maintObj->updateSpecsInfo($_GET)){
				echo '$("#dialogAdd").dialog("close");';
				echo "dialogAlert('Specs successfully updated.');";	
			} else {
				echo "dialogAlert('Error updating Brand.');";
			}	
		}else{
			echo "dialogAlert('Brand already exists!');";
		}
	exit();
	break;
	
	case 'deleteBrand':
		if($maintObj->deleteSpecs($_GET['id'])){
			echo "dialogAlert('Brand succesfully deleted.')";
		}else{
			echo "dialogAlert('There was an error during the deletion of Brand!')";	
		}
	exit();
	break;
	
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>User Maintenance</title>
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
	
	$("#brandTable").jqGrid({ 
		url:'display.php?action=Load', 
		datatype: "json", 
		colNames:['ID','Description','Status'], 
		colModel:[ 
			{name:'displaySpecsId', index:'displaySpecsId', width:20, align:"right"}, 
			{name:'displaySpecsDesc', index:'displaySpecsDesc', width:100, align:"left"}, 
			{name:'stat', index:'stat', width:40, align:"center"}
		], 
		rowNum:10, 
		rowList:[10,20,30], 
		pager: '#brandPager', 
		sortname: 'displaySpecsId', 
		viewrecords: true, 
		sortorder: "desc", 
		caption:"Lists", 
		height:"auto",
		width:"450",
		onSelectRow: function(id){ 
			var id = jQuery("#brandTable").jqGrid('getGridParam','selrow'); 
			var ret = jQuery("#brandTable").jqGrid('getRowData',id);
			var stsBrand = ret.displaySpecsId;
			$("#hdnBrandId").val(stsBrand);
			$("#editBrand").addClass("link");
			$("#editBrand").attr("onclick","editBrandInfo();");
			$("#deleteBrand").addClass("link");
			$("#deleteBrand").attr("onclick","deleteBrand();");
			
			$("#specsDet").addClass("link");
			$("#specsDet").attr("onclick","displayDetail();");
	   }
        
	}).navGrid("#brandPager",{edit:false,add:false,del:false}); 
	
	
	$("#AddBrand").click( function(){ 
		clearAllText();
		$("#dialogAdd").dialog("destroy");
		$("#dialogAdd").dialog({
			title: "NEW DISPLAY SPECIFICATION",
			height: 210,
			width: 380,
			modal: true,
			closeOnEscape: false,
			close: function() {
				reloadGrid();
			},
			buttons: {
				'Save': function(){
					if (validateInputs()) {
						$.ajax({
							url: "display.php",
							type: "GET",
							traditional: true,
							data: $("#formBrand").serialize()+'&action=AddBrand',
							success: function(msg){
								eval(msg);
								reloadGrid();
							}	
						});
					}
				}
			}
		});				
	});
});
function reloadGrid() {
	$("#brandTable").trigger("reloadGrid"); 
	$("#editBrand").removeClass("link");
	$("#editBrand").addClass("disable");
	$("#deleteBrand").removeClass("link");
	$("#deleteBrand").addClass("disable");
}
function clearAllText(){
	$("#txtBrand").val("");	
	$("#txtAmount").val("");	
}
function validateString(ObjName){
	if(ObjName.val().length == 0){
		ObjName.addClass("ui-state-error");
		dialogAlert("Required Field!");
		return false;
	}
	else{
		ObjName.removeClass("ui-state-error");
		return true;
	}
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
function validateInputs() {
	var val = true;
	if (validateString($("#txtBrand"))== false)
		val = false;
	if (validateString($("#txtAmount"))== false)
		val = false;
	
	return val;
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
function editBrandInfo(){
	clearAllText();
	var id = $("#hdnBrandId").val();
	$.ajax({
		url: "display.php",
		type: "GET",
		traditional: true,
		data: 'action=getInfo&id='+id,
		success: function(msg){
			eval(msg);
		}				
	});	
	$("#dialogAdd").dialog("destroy");
	$("#dialogAdd").dialog({
		title: "Edit User",
		height: 210,
		width: 380,
		modal: true,
		closeOnEscape: false,
		close: function() {
			reloadGrid();
		},
		buttons: {
			'Save': function(){
				if (validateInputs()) {
					$.ajax({
						url: "display.php",
						type: "GET",
						traditional: true,
						data: $("#formBrand").serialize()+'&action=updateBrand',
						success: function(msg){
							reloadGrid();
							eval(msg);
							
						}	
					});
				}
			}
		}
	});
}
function deleteBrand(){
	var id = $("#hdnBrandId").val();	
	$("#dialogMsg").html('Are you sure you want to delete?');
	$("#dialogAlert").dialog("destroy");
	$("#dialogAlert").dialog({
		modal: true,
		buttons: {
			'Yes': function(){
				$.ajax({
					url: "display.php",
					type: "GET",
					traditional: true,
					data: 'action=deleteBrand&id='+id,
					success: function(msg){
						reloadGrid();
						eval(msg);
						
					}
				});
			},
			'No': function (){
				$(this).dialog('close');
				reloadGrid();
			}
		}
	});
}

function displayDetail(){
	var dispId=$('#hdnBrandId').val();
	$("#Access").dialog("destroy");
	$("#Access").dialog({
		title: "Display Specification Details",
		height: 365,
		width: 480,
		modal: true,
		closeOnEscape: false,
		close: function() {
			$('#AccessData').html('');
			$("#Access").dialog("destroy");
		}				
	});	
	$.ajax({
		url: 'displayDtl.php',
		type: "GET",
		data: "dispId="+dispId,
		success: function(Data){
			$('#AccessData').html(Data);
		}				
	});		
	
				
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
</script>
<style type="text/css">
<!--
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
	width:541px; 
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
	height: 50px;
	border: solid 1px #222;
	padding: 2px;
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
</style>
</head>

<body>

<h2 class=" ui-widget-header">DISPLAY SPECS MAINTENANCE</h2>
<br />


<center>
<span id="AddBrand" style="cursor: pointer; font-size:11px;" class="link" title="New">
	<img src="../../images/file_add.png" title="Add Brand" /> New
</span>
&nbsp;&nbsp;&nbsp;&nbsp;
<span class="style23, disable" id="editBrand" title="Edit" onclick="">
	<img src="../../images/file_edit.png" title="Edit Brand" /> Edit
</span>
&nbsp;&nbsp;&nbsp;&nbsp;
<span class="style23, disable" id="deleteBrand" title="Delete" onclick="">
	<img src="../../images/file_delete.png" title="Delete Brand" /> Delete
</span>
&nbsp;&nbsp;&nbsp;&nbsp;
<span class="style23, disable" id="specsDet" title="Specification Details" onclick="">
	<img src="../../images/application_view_detail.png" title="Specification Details" /> Specification Details
</span>
<div align="right" style="float: right;">

</div>
<br /><br />
<table id="brandTable"></table> </center>
<div id="brandPager"></div> <br /> 

	<div style=" visibility:hidden;  overflow:hidden;">
        <div id='dialogAdd' title='Add'>
        	<div class="headerContent ui-corner-all">
            <form id="formBrand">
            <input type="hidden" name="hdnBrandId" id="hdnBrandId" value="" />    	
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td width="60%" height="25" class="hd">Display Specification: </td>
                  <td width="%"><input type="text" name="txtBrand" maxlength="20"  id="txtBrand" class="textBox" /></td>
                </tr>
                <tr>
                	<td height="25" class="hd">Amount Fee:</td>
                    <td><input type="text" name="txtAmount" maxlength="20"  id="txtAmount" class="textBox" onKeyPress='return valNumInputDec(event);' style="text-align:right;"/></td>
                </tr>
           </table>  
            </form> 
            </div>
        </div>
        <div id="Access">
        	<span id="AccessData"></span>
     	</div>
        <div id='dialogProcess' style=" overflow:hidden;"><br />
            <div style="text-align:center"><img src="../../images/progress2.gif" /></div>
            <div id='Process' style="text-align:center"></div>
        </div> 
        <div id='dialogAlert' title='STS'>
            <p id='dialogMsg'></p>
    	</div>
       
	</div>
</body>
</html>