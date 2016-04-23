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
        <script type="text/javascript">
            // ON CHANGE LOCATION
            $(function(){  
                $('#cmb_location').change(function() {
                    var specs = $('#cmb_specs').val();
                    var location = $(this).val();   
                                           
                    var referenceNumber = $('#txtReferenceNo').val();    
                    var vatable = $('#txtVatable').val();
                    
                    if(specs == 0){
                        alert('Please select display specs first!');    
                    }
                    else{
                        $.ajax({           
                            url: "daDtl2.php",
                            type: "POST",
                            data: "action=displayRentables&display_specs_id="+specs+"&display_location="+location+'&reference_number='+referenceNumber+'&with_vat='+vatable,
                            success: function(data){   
                                $('#div_display_rentables').html(data);     
                            }         
                        });      
                    }
                      
                });
            });  
        </script>
        <? $daObj->DropDownMenu($daObj->makeArr($daObj->getLocation($_POST['display_specs_id']),'locId','description',''),'cmb_location','','class="selectBox3"'); ?>
        <?php
    exit();
    break; 
    
    case 'displaySpecsData':    
            $displaySpecsData = $daObj->getDiplaySpecs($_POST['display_specs_id']);
            echo $displaySpecsData['specsAmount'];
    exit();
    break; 
    
    case 'displayRentables':
        ?>
        <script type="text/javascript">   
            // CHECK UNCHECK CHECKBOX
            $(function(){
            // add multiple select / deselect functionality
                $("#chkAll").click(function () {
                    $('.case').attr('checked', this.checked);
                    $("#txtNoUnits").val($(".case:checked").length);
                      
                    var counter = $(".case:checked").length;   

                    var chkArray = [];
                    $(".case:checked").each(function(){
                        chkArray.push($(this).val());
                    })
                    
                    var vatable = $('#txtVatable').val();

                    if(counter > 0){
                        $(function(){
                            for(a=0;a<counter;a++) {
                                var displayFee = $("#txtDisplayFee_"+chkArray[a]).val();
                                var perMonth = displayFee;
                                
                                if(vatable == 'Y'){
                                    var vatAmount = displayFee * 0.12; 
                                    vatAmount = parseFloat(vatAmount).toFixed(2);
                                    var grossAmount = parseFloat(displayFee) + parseFloat(vatAmount);
                                    grossAmount = parseFloat(grossAmount).toFixed(2);
                                }
                                else{
                                    var vatAmount = 0;
                                    vatAmount = parseFloat(vatAmount).toFixed(2);
                                    var grossAmount = parseFloat(displayFee);
                                    grossAmount = parseFloat(grossAmount).toFixed(2);
                                }                               
                                
                                $("#txtPerMonth_"+chkArray[a]).val(perMonth);
                                $("#txtVatAmount_"+chkArray[a]).val(vatAmount);     
                                $("#txtGrossAmount_"+chkArray[a]).val(grossAmount);
                            }  
                        }); 
                        
                        var selected;
                        selected = chkArray.join(',') + ",";
                        selected = selected.slice(0,-1);  
                        
                        $("#txtDisplaySpecsIdSeriesStore").val(selected)       
                    } 
                });
         
            // if all checkbox are selected, check the selectall checkbox
            // and viceversa
                $(".case").click(function(){
             
                    if($(".case").length == $(".case:checked").length) {
                        $("#chkAll").attr("checked", "checked");
                    } else {
                        $("#chkAll").removeAttr("checked");
                    }
                    $("#txtNoUnits").val($(".case:checked").length); 
                                    
                    var counter = $(".case:checked").length;   
        
                    var chkArray = [];
                    $(".case:checked").each(function(){
                        chkArray.push($(this).val());
                    })
                    
                    var vatable = $('#txtVatable').val();
                    
                    if(counter > 0){
                        $(function(){
                            for(a=0;a<counter;a++) {
                                var displayFee = $("#txtDisplayFee_"+chkArray[a]).val();
                                var perMonth = displayFee;
                                
                                if(vatable == 'Y'){
                                    var vatAmount = displayFee * 0.12; 
                                    vatAmount = parseFloat(vatAmount).toFixed(2);
                                    var grossAmount = parseFloat(displayFee) + parseFloat(vatAmount);
                                    grossAmount = parseFloat(grossAmount).toFixed(2);
                                }
                                else{
                                    var vatAmount = 0;
                                    vatAmount = parseFloat(vatAmount).toFixed(2);
                                    var grossAmount = parseFloat(displayFee);
                                    grossAmount = parseFloat(grossAmount).toFixed(2);
                                } 
                                
                                $("#txtPerMonth_"+chkArray[a]).val(perMonth);
                                $("#txtVatAmount_"+chkArray[a]).val(vatAmount);     
                                $("#txtGrossAmount_"+chkArray[a]).val(grossAmount);
                            }  
                        });  
                        
                        var selected;
                        selected = chkArray.join(',') + ",";
                        selected = selected.slice(0,-1);  
                        
                        $("#txtDisplaySpecsIdSeriesStore").val(selected)   
                    }
                }); 
            });   
        </script>
        <?php 
        $displayAvailableRentables = $daObj->listAvailableRentables($_POST['display_specs_id'],$_POST['display_location']);
    ?>      
        <table width="100%" border="1" cellspacing="0" cellpadding="0" class="details"
            style="border-color: lightblue;">
            <tr>
                <td width="1%" height="25">
                    <input type="checkbox" name="chkAll" id="chkAll">
                </td>
                <td align="center"><strong>Branch</strong></td>
                <td align="center"><strong>Series</strong></td>
                <td align="center"><strong>Display Fee per Unit</strong></td>
                <td align="center"><strong>Per Month</strong></td>
                <td align="center"><strong>Vat Amount</strong></td>
                <td align="center"><strong>Gross Amount</strong></td>
            </tr>
            <?php
            foreach($displayAvailableRentables as $val){
            ?>
            <tr>
                <td width="1%" height="25">
                    <input type="checkbox" name="chk_<?=$val['displaySpecsIdSeriesStore'];?>" id="chk_<?=$val['displaySpecsIdSeriesStore'];?>"  value="<?=$val['displaySpecsIdSeriesStore'];?>" class="case">
                </td>
                <td align="center"><?=$val['strCode'];?></td>
                <td align="center"><?=$val['suffixSeries'];?></td>
                <td align="center">
                    <input type="text" name="txtDisplayFee_<?=$val['displaySpecsIdSeriesStore'];?>" id="txtDisplayFee_<?=$val['displaySpecsIdSeriesStore'];?>" class="textBoxTemp case" value="<?=$val['specsAmount'];?>">
                </td>
                <td align="center">
                    <input type="text" name="txtPerMonth_<?=$val['displaySpecsIdSeriesStore'];?>" id="txtPerMonth_<?=$val['displaySpecsIdSeriesStore'];?>" class="textBoxTemp case" value="" readonly>
                </td>
                <?php
                if($_POST['with_vat'] == 'Y'){
                ?>  
                    <td align="center">
                        <input type="text" name="txtVatAmount_<?=$val['displaySpecsIdSeriesStore'];?>" id="txtVatAmount_<?=$val['displaySpecsIdSeriesStore'];?>" class="textBoxTemp case" value="" readonly>    
                    </td>    
                <?php
                }
                else{
                    $vat_amount = 0;
                ?>
                    <td align="center">
                        <input type="text" name="txtVatAmount_<?=$val['displaySpecsIdSeriesStore'];?>" id="txtVatAmount_<?=$val['displaySpecsIdSeriesStore'];?>" class="textBoxTemp case"  value="<?=$vat_amount;?>" readonly>    
                    </td>    
                <?php       
                }
                ?>   
                <td align="center">
                    <input type="text" name="txtGrossAmount_<?=$val['displaySpecsIdSeriesStore'];?>" id="txtGrossAmount_<?=$val['displaySpecsIdSeriesStore'];?>" class="textBoxTemp case" readonly>    
                </td>
            </tr>
            <?php
            }
            ?>
             
        </table>
        <input type="hidden" name="txtDisplaySpecsIdSeriesStore" id="txtDisplaySpecsIdSeriesStore" value=""/>
    <?php            
    exit();
    break; 
    
    case "AddSTSDtl":
        
        $idSeriesStore = array($_POST['txtDisplaySpecsIdSeriesStore']);
        
        foreach($idSeriesStore as $val){
            echo $val[0];    
        }        
        
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
        value = $('#cmb_specs').val();  

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

            if(specs == 0)
            {     
                $('#div_display_rentables').html(''); 
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
            }
            else
            {   
                $('#div_display_rentables').html('');   
                       
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
        <input type="hidden" name="txtReferenceNo" id="txtReferenceNo" class="textBox" value="<?=$_POST['refNo'];?>">
        <input type="hidden" name="txtVatable" id="txtVatable" class="textBox" value="<?=$_POST['vatTag'];?>">
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
            <table width="100%" border="0" cellspacing="0" cellpadding="2" align="center">
                <tr> 
                    <td class="hd2">No. of Units</td>
                    <td>
                        <input type="text" name="txtNoUnits" id="txtNoUnits" class="textBoxTemp" onChange="computeMonthly();" onKeyPress='return valNumInputDec(event); ' style="text-align:right" disabled/>
                    </td>    
                    <td class="hd2">Display Fee per Unit</td>
                    <td>
                        <input type="text" name="txtUnitAmt" id="txtUnitAmt" class="textBoxTemp" onKeyPress='return valNumInputDec(event);' style="text-align:right" onChange="computeMonthly();" disabled/>
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
            <div style="width: 100%;" id="div_display_rentables"></div>   
            <?php 
            /* 
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
            */
            ?>
        </fieldset> 
    </div>
    </form>  
</body>
</html>
