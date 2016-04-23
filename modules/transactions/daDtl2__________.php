<?
session_start();

include("../../includes/db.inc.php");
include("../../includes/common.php");

include("daObj2.php");
$daObj = new daObj2();

if ($_SESSION['sts-username'] == "") {
	echo "<SCRIPT>window.parent.location.href='../../index.php';</SCRIPT>";
}

switch($_POST['action']) {
    case 'displayLocation':	
        ?>
        <? $daObj->DropDownMenu($daObj->makeArr($daObj->getLocation($_POST['display_specs_id']),'locId','description',''),'cmb_location','','class="selectBox3"'); ?>
        <?php
    exit();
    break; 
    
    case 'displaySpecsData':    
            $displaySpecsData = $daObj->getDiplaySpecs($_POST['display_specs_id']);
            echo $displaySpecsData['specsAmount'];
    exit();
    break; 
}
?>
<html>
<head>
<title>Untitled Document</title>

<script type="text/javascript">
    // ON FIRST LOAD
    $(function(){      
        value = $('#cmb_specs').val()
        $.ajax({           
            url: "daDtl2.php",
            type: "POST",
            data: "action=displayLocation&display_specs_id="+value,
            success: function(data){   
                $('#div_display_location').html(data);    
            }         
        });  
    });  
         
    // ON CHANGE SPECS
    $(function(){  
        $('#cmb_specs').change(function() {
            var specs = $(this).val();
            $.ajax({           
                url: "daDtl2.php",
                type: "POST",
                    data: "action=displayLocation&display_specs_id="+specs,
                success: function(data){   
                    $('#div_display_location').html(data);  
                    $.ajax({           
                        url: "daDtl2.php",
                        type: "POST",
                        data: "action=displaySpecsData&display_specs_id="+specs,
                        success: function(data){   
                            $('#txtUnitAmt').val(data);  
                        }         
                    });        
                }         
            });    
        });
    }); 
    
    // ON CHANGE LOCATION
    $(function(){  
        $('#cmb_location').change(function() {
            var specs = $('#cmb_specs').val();
            var location = $(this).val();
            
            if(specs == 0){
                alert('Please select display specs first!');    
            }
            else{
                $.ajax({           
                    url: "daDtl2.php",
                    type: "POST",
                        data: "action=displayRentables&display_specs_id="+specs+"&display_location="+location,
                    success: function(data){   
                        $('#div_display_location').html(data);     
                    }         
                });      
            }
              
        });
    });  
    
    
    
</script>
<style type="text/css">
<!--
.txtInv {
	visibility:hidden;
}

.txtVis2 {
	visibility:visible;
}
.textBox2 {
	border: solid #999999; 
	border-width: 1px; 
	width:100px; 
	height:18px;
	font-size: 11px;
}
#formExpenseDtl input.error {
	background: #f8dbdb;
	border-color: #e77776;
}
#formExpenseDtl select{
	border: 1px solid #999999; 
	width:192px; 
	height:22px;
	font-size: 11px;
}
#formExpenseDtl select.error {
	background: #f8dbdb;
	border-color: #e77776;
}

.headerContentDtl {
	border: solid #666666 1px;
	padding: 3px;
	background-color:#E9E9E9;
}
.textBox1 {	border: solid #999999; 
	border-width: 1px; 
	width:190px; 
	height:18px;
	font-size: 11px;
}
.txtInv {
	visibility:hidden;
}
.txtVis {
	visibility:visible;
	cursor:pointer;
}
.txtVis2 {
	visibility:visible;
}
.enabletxt {
	background: #C7DAFE;
	border-color: #e77776;
}
.selectBox2 {
	border: 1px solid #222; 
	width:192px; 
	height:22px;
	font-size: 11px;
}
.selectBox3 {
	border: 1px solid #222; 
	width:190px; 
	height:18px;
	font-size: 11px;
}
legend {
	color:#F60;	
}
.hd2 {
	font-size: 9px;
	font-family: Verdana;
	font-weight: bold;
}
.textBoxTemp {
	border: solid 1px #222; 
	border-width: 1px; 
	width:190px; 
	height:14px;
	font-size: 11px;
}
.textBoxTemp2 {
	border: solid 1px #222; 
	border-width: 1px; 
	width:80px; 
	height:14px;
	font-size: 11px;
}
.textBoxTempSmall {
	border: solid 1px #222; 
	border-width: 1px; 
	width:40px; 
	height:14px;
	font-size: 11px;
}
.line { 
	border-bottom: solid 1px; 
	border-bottom-color: #C3C3C3;
}
-->
</style>     
</head>

<body>
	<form id="formSTSDtl" name="formSTSDtl">
	<div class="headerContentDtl ui-corner-all">
    	<fieldset>
        <legend>HEADER</legend>
    	<table width="99%" border="0" cellspacing="0" cellpadding="2" align="center">
        	<tr>
             	<td class="hd">Display Type</td>
                 <td><? $daObj->DropDownMenu($daObj->makeArr($daObj->getDisplayType(),'displayTypeId','displayTypeDesc',''),'txtDispTyp','','class="selectBox2" '); ?></td>
            
                <td class="hd">Brand Name</td>
                <td><input type="text" name="txtBrand" id="txtBrand" class="textBox" /></td>         
            	<td class="hd">Remarks</td>
                <td><input type="text" name="txtRem" id="txtRem" class="textBox"/></td>
            </tr>
        </table>
        </fieldset>
        <fieldset>
        <legend>TEMPLATE</legend>
            <table width="100%" border="1" cellspacing="0" cellpadding="2" align="center">
                <tr> 
                    <td class="hd2">No. of Units</td>
                    <td>
                        <input type="text" name="txtNoUnits" id="txtNoUnits" class="textBoxTemp" onChange="computeMonthly();" onKeyPress='return valNumInputDec(event); ' style="text-align:right" disabled/>
                    </td>    
                    <td class="hd2">Display Fee per Unit</td>
                    <td>
                        <input type="text" name="txtUnitAmt" id="txtUnitAmt" class="textBoxTemp" onKeyPress='return valNumInputDec(event);' style="text-align:right" onChange="computeMonthly();"/>
                    </td>
                <tr/>
                <tr> 
                    <td class="hd2">Display Specs</td>
                    <td>
                        <? $daObj->DropDownMenu($daObj->makeArr($daObj->listDiplaySpecs(),'displaySpecsId','displaySpecsDesc',''),'cmb_specs','','class="selectBox3"'); ?>
                    </td> 
                    <td class="hd2">Location</td>
                    <td>
                        <div style="width: 100%;" id="div_display_location"> 
                        </div>
                    </td> 
                </tr> 
                <!-- <tr> -->
                    <!-- <td colspan="4" align="center"> -->
                        <!-- <input type="button" id="btnApplyAll" name="btnApplyAll" value="Apply To All" onClick="applySelected();" style="color:#f20;"> -->
                    <!-- </td> -->
                <!-- </tr> -->
            </table>
        </fieldset>
        
        <fieldset>
        <legend>DISPLAY</legend>
            <table width="100%" border="0" cellspacing="0" cellpadding="0" class="details">
                <tr>
                    <td width="1%" height="25"><input type="checkbox" name="chAll" onClick="checkAllDtl();"  id="chAll"></td>
                    <td width="22%"><strong>Branch
              	        <input type="hidden" name="hdnEwt" id="hdnEwt" value="<?=$ewt?>" />
                        <input type="hidden" name="hdnVat" id="hdnVat" value="<?=$vat?>" />
				        <input type="hidden" name="hdDtl_Amt" value="<?=$arrSTS['stsAmt']?>" id="hdDtl_Amt" />
				        <input type="hidden" name="hdnEnhanceType" value="<?=$arrSTS['enhanceType']?>" id="hdnEnhanceType" />
                        <input type="hidden" name="hdDtl_refNo" value="<?=$_POST['refNo']?>" id="hdDtl_refNo" />
				        <input type="hidden" name="hdDtl_TotAmt" value="<?=(float)$DtlAmt?>" id="hdDtl_TotAmt" />
                        <input type="hidden" name="hdClipTxt" value="" id="hdClipTxt" />
                    </strong>
                    </td> 
                    <td align="center"><strong>Display Specs</strong></td>
                    <td align="center"><strong>Disp. Fee per Unit</strong></td>
                    <td align="center"><strong>No of Units</strong></td>
                    <td align="center"><strong>Total per Mo.</strong></td>
                     <td align="center"><strong>VAT Amount</strong></td>
                    <td align="center"><strong>Gross Amount</strong></td>
                </tr>
            </table> 
        </fieldset> 
    </div>
    </form>  
</body>
</html>
