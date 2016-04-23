<?php
session_start();
include("../../includes/db.inc.php");
include("../../includes/common.php");
include("maintenanceObj2.php");
$maintenanceObj = new maintenanceObj2;

$now = date('Y-m-d H:i:s');

if ($_SESSION['sts-username'] == "") {
	echo "<SCRIPT>window.parent.location.href='../../index.php';</SCRIPT>";
}    

$userName = $maintenanceObj->getBranches($_SESSION['sts-username']); 
$storeCode = $userName['strCode'];


switch($_POST['action']) {
    
	case 'saveRentables':
		$result = $maintenanceObj->updateRentables($storeCode,$_POST['displaySpecsIdSeries'],$_POST['group'],$_POST['availability'],$_POST['location'],$_POST['supplier']);
		if($result!=0) {
			echo "alert('STS rentables successfully added.');\n";
		} else {
			echo "alert('Error/No Rentables Added.');";
		}
	exit();
	break;
	
	case 'untagRentables':
		$maintenanceObj->untagRentables($_POST['refNo'],$_POST['strCode']);
		echo "dialogAlert('Rentables Deleted');";
	exit();
	break;
    
    case 'searchSupplier':
        $arrResult = array();
            $arrSupp = $maintenanceObj->findSupplier($_GET['term']);
                foreach($arrSupp as $val){
                    $arrResult[] = array(
                        "id"=>$val['suppCode']."|".$val['suppName'],
                        "label"=>$val['suppCode']." - ".str_replace("-",'-',$val['suppName']),
                        "value" => strip_tags($val['suppName']));    
                }
        echo json_encode($arrResult);
    exit();    
    
    case 'displayRentables':
        ?>  
        <table class="rentableList" id="rentableList" width="100%" border="0" align="center">
            <thead>
                <tr>
                    <td align="center"><b>RENTABLE SPACE</b></td>
                    <td align="center"><b>SERIES</b></td>
                    <td align="center"><b>GROUP</b></td>
                    <td align="center"><b>LOCATION</b></td>
                    <td align="center"><b>SUPPLIER</b></td>
                    <td align="center"><b>STORE AVAILABILITY</b></td>
                    <td align="center"><b>ACTION</b></td>
                </tr>
            </thead>    
            <tbody>
                <?php 
                $arrDisplaySpecsSeries = $maintenanceObj->listDiplaySpecsSeries($storeCode,$_POST['display_specs_id'],$_POST['display_group'],$_POST['display_series']);  
                    foreach ($arrDisplaySpecsSeries as $val) {
                ?>  
                <tr>
                    <td align="left" width="40%"><?=$val['displaySpecsDesc'];?></td>
                    <td align="left" width="40%"><?=$val['suffix'].$val['series'];?>
                        <input type=hidden id='txt_identity_<?=$val['displaySpecsId'];?><?=$val['series'];?>' name='txt_identity_<?=$val['displaySpecsId'];?><?=$val['series'];?>' value='<?=$val['displaySpecsId'];?><?=$val['series'];?>'>
                    </td>    
                    <?php 
                    $assDisableStoreAvailability = $maintenanceObj->checkStoreAvailability($storeCode,$val['displaySpecsId'].$val['series']);
                    if(strlen($assDisableStoreAvailability['endDate']) > 0){
                        $disabled = "disabled=\"disabled\"";    
                    }
                    else{
                        $disabled = "";       
                    }
                    ?>
                    <td width="10%">
                        <? $assDisplaySelectedGroup= $maintenanceObj->getSelectedGrpList($storeCode,$val['displaySpecsId'].$val['series']); ?>
                        <? $maintenanceObj->DropDownMenu($maintenanceObj->makeArr($maintenanceObj->getGrpList(),'minCode','description',$assDisplaySelectedGroup['description']),'cmb_group_'.$val['displaySpecsId'].$val['series'],$assDisplaySelectedGroup['grpCode'],'class="selectBox"'." ".$disabled); ?>
                    </td>  
                    <td width="10%">
                        <? $arrDisplayLocation= $maintenanceObj->getLocation($val['displaySpecsId']); ?>
                        <? $assDisplaySelectedLocation= $maintenanceObj->getSelectedLocation($val['displaySpecsId'].$val['locId']); ?>
                        <? $maintenanceObj->DropDownMenu($maintenanceObj->makeArr($maintenanceObj->getLocation($val['displaySpecsId']),'locId','description',$assDisplaySelectedLocation['description']),'cmb_location_'.$val['displaySpecsId'].$val['series'],$assDisplaySelectedLocation['locId'],'class="selectBox"'." ".$disabled); ?>
                    </td>
                    <?php /* 
                    <td align="left">
                        <input type="text" name="txtSupplier" id="txtSupplier" class="selectBox textBox txtSupplier" maxlength="7" style="width: 200px;">
                        <input type="hidden" name="txtHiddenSupplier" id="txtHiddenSupplier">
                    </td> 
                    */ ?>
                    <td width="10%">
                        <? $arrDisplaySupplier= $maintenanceObj->findSupplier(); ?>
                        <? $assDisplaySelectedSupplier= $maintenanceObj->findSelectedSupplier($storeCode,$val['displaySpecsId'].$val['series']); ?>
                        <? $maintenanceObj->DropDownMenu($maintenanceObj->makeArr($maintenanceObj->findSupplier(),'suppCode','suppCodeName',$assDisplaySelectedSupplier['description']),'cmb_supplier_'.$val['displaySpecsId'].$val['series'],$assDisplaySelectedSupplier['suppCode'],'class="selectBox"'." ".$disabled); ?>
                    </td>
                    <td align="center" width="10%">  
                        <? $availability = $maintenanceObj->displayAvailability($val['displaySpecsId'],$val['series']);?>
                        <? $option = array($availability['availabilityTag']=>$availability['availabilityTagName'],'Y'=>'YES','N'=>'NO'); ?>    
                        <? $maintenanceObj->DropDownMenu($option,'cmb_availability_'.$val['displaySpecsId'].$val['series'],'','class="selectBox"'." ".$disabled); ?>
                    </td>
                    
   
                    <td align="center" colspan="3" style="padding-top: 0px;"> 
                        <button id="save_specs-<?=$val['displaySpecsId'];?><?=$val['series'];?>" class="btn" onClick="saveSpecs(<?=$val['displaySpecsId'];?><?=$val['series'];?>);" <?=$disabled;?>>SAVE</button>
                    </td>
                </tr>
                <?php }?>
            </tbody>
            <tfoot></tfoot>
        </table>  
        <?
    exit();
    break;
    
    case 'checkSeriesPositive':
        echo $maintenanceObj->checkAddSeries($storeCode,$_POST['display_specs_id']);
    exit();
    break;
    
    case 'addSeriesPositive':
        echo $maintenanceObj->addSeries($storeCode,$_POST['display_specs_id']);
    exit();
    break;
    
    case 'checkSeriesNegative':
        $row = $maintenanceObj->checkMinusSeries($storeCode,$_POST['display_specs_id']);
        if(strlen($row['endDate'])>0){
            echo "1";    
        }
        else{
            echo "0";       
        }
    exit();
    break;
    
    case 'minusSeriesPositive':
        echo $maintenanceObj->minusSeries($storeCode,$_POST['display_specs_id']);
    exit();
    break;
    
    /*
    case 'displayLocation':
        ?>   
            <? $maintenanceObj->DropDownMenu($maintenanceObj->makeArr($maintenanceObj->getLocation($_POST['display_specs_id']),'locId','series',$arrDisplayLocation['series']),'cmb_location2_'.$val['displaySpecsId'].$val['series'],'','class="selectBox"'." ".$disabled); ?>
        <?php
    exit();
    break;
    */
}  
?>    
 
<html>
    <head>
        
            
        <link type="text/css" href="../../includes/jquery/css/redmond/jquery-ui-1.8.16.custom.css" rel="stylesheet" />
        <link type="text/css" href="../../includes/jquery/development-bundle/demos/demos.css" rel="stylesheet" />
        <link type="text/css" href="../../includes/jqGrid/css/ui.jqgrid.css"rel="stylesheet" />
        <script type="text/javascript" src="../../includes/jquery/js/jquery-1.6.2.min.js"></script>
        <script type="text/javascript" src="../../includes/jquery/js/jquery-ui-1.8.16.custom.min.js"></script>
        <script type="text/javascript" src="../../includes/jqGrid/js/i18n/grid.locale-en.js"></script>
        <script type="text/javascript" src="../../includes/jqGrid/js/jquery.jqGrid.min.js"></script>
        <script type="text/javascript" src="../../includes/js/jquery.dataTables.min.js"></script>   
        
        <link href="../../includes/showLoading/css/showLoading.css" rel="stylesheet" media="screen" />
        <script type="text/javascript" src="../../includes/showLoading/js/jquery.showLoading.js"></script>
               
        <script type="text/javascript">      
             
        // DATA TABLE
        $('document').ready(function(){
            $('#rentableList').dataTable({
                "sPaginationType": "full_numbers"
            })  
        });
        
         
        // ON FIRST LOAD
        $(function(){      
            value = $('#cmb_specs').val()
            $.ajax({           
                url: "rentable2.php",
                type: "POST",
                data: "action=displayRentables&display_specs_id="+value,
                success: function(data){   
                    $('#div_display_rentables').html(data);        
                }         
            });  
        });   
        
        // ON CHANGE SPECS
        $(function(){  
            $('#cmb_specs').change(function() {
                var group = $('#cmb_group').val();
                var specs = $(this).val();
                var series = $("#txtSeries").val();
                $.ajax({           
                    url: "rentable2.php",
                    type: "POST",
                    data: "action=displayRentables&display_specs_id="+specs+"&display_group="+group+"&display_series="+series,
                    success: function(data){   
                        $('#div_display_rentables').html(data);        
                    }         
                });    
            });
        });   
        
        // ON CHANGE GROUP
        $(function(){  
            $('#cmb_group').change(function() {
                var specs = $('#cmb_specs').val();
                var group = $(this).val();
                var series = $("#txtSeries").val();
                $.ajax({           
                    url: "rentable2.php",
                    type: "POST",
                    data: "action=displayRentables&display_specs_id="+specs+"&display_group="+group+"&display_series="+series,
                    success: function(data){   
                        $('#div_display_rentables').html(data);        
                    }         
                });    
            });
        });  
        
        // ON INPUT SERIES
        $(function(){ 
            $("#txtSeries").keyup(function()
            {    
                var series = $("#txtSeries").val();
                var specs = $('#cmb_specs').val();
                var group = $('#cmb_group').val();
                $.ajax({           
                    url: "rentable2.php",
                    type: "POST",
                    data: "action=displayRentables&display_specs_id="+specs+"&display_group="+group+"&display_series="+series,
                    success: function(data){   
                        $('#div_display_rentables').html(data);        
                    }         
                }); 
            }); 
        });   
        
        // AUTO COMPLETE 
        $(function() {
            $( ".txtSupplier" ).autocomplete({
                source: function(request, response) {
                    $.getJSON('rentable2.php?action=searchSupplier', {
                        term: request.term
                    }, response);
                },
                
                select: function(event, ui) {
                    var tagId = ui.item.id;
                    
                    $('#txthiddenSupplier').val(tagId);
                    $('#txtSupplier').val(tagId);
                }
            })
        });
        
        function saveSpecs(displaySpecsIdSeries)
        {
            var identity = $('#txt_identity_'+displaySpecsIdSeries).val();
            var specsIdSeries = displaySpecsIdSeries; 
            var group = $('#cmb_group_'+displaySpecsIdSeries).val();      
            var location = $('#cmb_location_'+displaySpecsIdSeries).val();          
            var supplier = $('#cmb_supplier_'+displaySpecsIdSeries).val();          
            var availability = $('#cmb_availability_'+displaySpecsIdSeries).val();
            
            specs = $('#cmb_specs').val();
            
            if(specs == 0){
                $("#dialogAlert").dialog("destroy");
                $("#dialogAlert").html('Please select display specs!').dialog({
                title: 'STS',
                height: 150,
                modal: true,
                closeOnEscape: false,
                modal: true,
                buttons: {
                        Ok: function() {
                            $(this).dialog('close');
                        }
                    } 
                });
                $('#cmb_specs').css("border","red solid 1px");   
                return false;   
            }
                              
            if(group == 0){
                $("#dialogAlert").dialog("destroy");
                $("#dialogAlert").html('Please select group!').dialog({
                title: 'STS',
                height: 150,
                modal: true,
                closeOnEscape: false,
                modal: true,
                buttons: {
                        Ok: function() {
                            $(this).dialog('close');
                        }
                    } 
                }); 
                $('#cmb_group_'+displaySpecsIdSeries).css("border","red solid 1px");
             return false;
            }
            else{
                $('#cmb_group_'+displaySpecsIdSeries).css("border","transparent solid 0px");    
            } 
            
            if(location == 0){
                $("#dialogAlert").dialog("destroy");
                $("#dialogAlert").html('Please select location!').dialog({
                title: 'STS',
                height: 150,
                modal: true,
                closeOnEscape: false,
                modal: true,
                buttons: {
                        Ok: function() {
                            $(this).dialog('close');
                        }
                    } 
                }); 
                $('#cmb_location_'+displaySpecsIdSeries).css("border","red solid 1px");
             return false;
            }
            else{
                $('#cmb_location_'+displaySpecsIdSeries).css("border","transparent solid 0px");    
            } 
            
            if(supplier == 0){
                $("#dialogAlert").dialog("destroy");
                $("#dialogAlert").html('Please select supplier!').dialog({
                title: 'STS',
                height: 150,
                modal: true,
                closeOnEscape: false,
                modal: true,
                buttons: {
                        Ok: function() {
                            $(this).dialog('close');
                        }
                    } 
                }); 
                $('#cmb_supplier_'+displaySpecsIdSeries).css("border","red solid 1px");
             return false;
            }
            else{
                $('#cmb_supplier_'+displaySpecsIdSeries).css("border","transparent solid 0px");    
            } 
            
            $.ajax({
                url: 'rentable2.php',
                type: "POST",
                data: 'action=saveRentables'+'&displaySpecsIdSeries='+specsIdSeries+'&group='+group+'&availability='+availability+'&location='+location+'&supplier='+supplier,
                beforeSend: function()
                    {
                        jQuery('#activity_pane').showLoading();
                    },
                success: function(data){   
                    // AFTER SAVING SPECS
                    $('#cmb_specs').val();
                    specs = $('#cmb_specs').val();       
                    $.ajax({           
                        url: "rentable2.php",
                        type: "POST",
                        data: "action=displayRentables&display_specs_id="+specs,
                        success: function(data){   
                            $('#div_display_rentables').html(data); 
                            jQuery('#activity_pane').hideLoading();       
                        }         
                    });  
                    
                }                
            });   
        }
        
        function addSeries(){
            
            specs = $('#cmb_specs').val();
            
            if(specs == 0){
                $("#dialogAlert").dialog("destroy");
                $("#dialogAlert").html('Please select display specs!').dialog({
                title: 'STS',
                height: 150,
                modal: true,
                closeOnEscape: false,
                modal: true,
                buttons: {
                        Ok: function() {
                            $(this).dialog('close');
                        }
                    } 
                });
                $('#cmb_specs').css("border","red solid 1px");      
            }
            else{
                $.ajax({           
                    url: "rentable2.php",
                    type: "POST",
                    data: "action=checkSeriesPositive&display_specs_id="+specs,
                    success: function(data){   
                        if(data == 1){
                            $.ajax({           
                                url: "rentable2.php",
                                type: "POST",
                                data: "action=addSeriesPositive&display_specs_id="+specs,
                                success: function(data){  
                                    // ON ADD SERIES  
                                    $.ajax({           
                                        url: "rentable2.php",
                                        type: "POST",
                                        data: "action=displayRentables&display_specs_id="+specs,
                                        success: function(data){   
                                            $('#div_display_rentables').html(data);        
                                        }         
                                    });  
                                }         
                            }); 
                        }
                        else{
                            $("#dialogAlert").dialog("destroy");
                            $("#dialogAlert").html('Please coordinate to merchandising!').dialog({
                            title: 'STS',
                            height: 150,
                            modal: true,
                            closeOnEscape: false,
                            modal: true,
                            buttons: {
                                    Ok: function() {
                                        $(this).dialog('close');
                                    }
                                } 
                            });  
                        }
                    }         
                });    
            }    
        }
        
        function minusSeries(){
            
            specs = $('#cmb_specs').val();
            
            if(specs == 0){
                $("#dialogAlert").dialog("destroy");
                $("#dialogAlert").html('Please select display specs!').dialog({
                title: 'STS',
                height: 150,
                modal: true,
                closeOnEscape: false,
                modal: true,
                buttons: {
                        Ok: function() {
                            $(this).dialog('close');
                        }
                    } 
                });    
                $('#cmb_specs').css("border","red solid 1px");        
            }
            else{
                $.ajax({           
                    url: "rentable2.php",
                    type: "POST",
                    data: "action=checkSeriesNegative&display_specs_id="+specs,
                    success: function(data){   
                        if(data == 1){
                            $("#dialogAlert").dialog("destroy");
                            $("#dialogAlert").html('Series still occupied!').dialog({
                            title: 'STS',
                            height: 150,
                            modal: true,
                            closeOnEscape: false,
                            modal: true,
                            buttons: {
                                    Ok: function() {
                                        $(this).dialog('close');
                                    }
                                } 
                            });   
                        }
                        else{
                            $.ajax({           
                                url: "rentable2.php",
                                type: "POST",
                                data: "action=minusSeriesPositive&display_specs_id="+specs,
                                success: function(data){  
                                    // ON MINUS SERIES    
                                    $.ajax({           
                                        url: "rentable2.php",
                                        type: "POST",
                                        data: "action=displayRentables&display_specs_id="+specs,
                                        success: function(data){   
                                            $('#div_display_rentables').html(data);        
                                        }         
                                    });  
                                }         
                            });        
                        }
                    }         
                });    
            }   
        }
        
        function isNumberKey(evt) {
            var charCode = (evt.which) ? evt.which : event.keyCode
            if (charCode > 31 && (charCode < 48 || charCode > 57))
             return false;
            else {
                var len = document.getElementById("txtSeries").value.length;
                var index = document.getElementById("txtSeries").value.indexOf('.');

                if (index > 0 && charCode == 46) {
                    return false;
                }
                if (index > 0){
                    var CharAfterdot = (len + 1) - index;
                    if (CharAfterdot > 3) {
                        if(charCode == 8){
                            return true;
                        }
                        else{
                            return false;
                        }
                    }
                }   
            }
            return true;
        }  
        </script>

        <style type="text/css" title="currentStyle">
            @import "../../includes/css/demo_page.css";
            @import "../../includes/css/demo_table_jui.css";   
            
            .selectBox {
                background: #99D6FF;
                width: 230px;
                padding: 5px;
                font-size: 12px;
                line-height: 1;
                border: 0;
                border-radius: 0;
                height: 25px;
                -webkit-appearnace: none;
            }
            
            <!-- DATA TABLE
            table#rentableList {
                border: blue;
            }
            
            table#rentableList tr.even td {
                background-color: white;
            }

            table#rentableList tr.odd td {
                background-color: #99D6FF;
            }
            
            table#rentableList tr.even:hover td {
                background-color: lightgray;
            }

            table#rentableList tr.odd:hover td {
                background-color: lightgray;
            }
            
            .btn {
              background: #3498db;
              background-image: -webkit-linear-gradient(top, #3498db, #2980b9);
              background-image: -moz-linear-gradient(top, #3498db, #2980b9);
              background-image: -ms-linear-gradient(top, #3498db, #2980b9);
              background-image: -o-linear-gradient(top, #3498db, #2980b9);
              background-image: linear-gradient(to bottom, #3498db, #2980b9);
              -webkit-border-radius: 5;
              -moz-border-radius: 5;
              border-radius: 5px;
              -webkit-box-shadow: 0px 1px 3px #666666;
              -moz-box-shadow: 0px 1px 3px #666666;
              box-shadow: 0px 1px 3px #666666;
              font-family: Arial;
              color: #ffffff;
              font-size: 12px;
              padding: 0px 0px 0px 0px;
              border: solid #1f628d 1px;
              text-decoration: none;
            }

            .btn:hover {
              background: #3cb0fd;
              text-decoration: none;
            }
        </style>
    </head>  
    <title>Rentables</title>    
    <body>  
        <h2 class="ui-widget-header ui-corner-all" style="padding:5px;">DISPLACE SPECIFICATIONS</h2>
        
        <div style="background: white; border-radius: 4px; border: black; height: 65px;">
            <br/>
            <table width="99%" border="0" align="right">
                <tr>
                    <td align="left" width="4%">SERIES</td>
                    <td align="left" width="1%">:</td>
                    <td align="left">
                        <button id="" class="btn" onClick="addSeries();">+ PLUS</button> 
                        <button id="" class="btn" onClick="minusSeries();">- MINUS</button>   
                    </td>
                </tr>
            </table>
            <br/>
        </div>
        <br/>
        <div style="background: white; border-radius: 4px; border: black; height: 65px;">
            <br/>
            <table width="99%" border="0" align="right">
                <tr>
                    <td align="left" width="7%">DISPLAY SPECS</td>
                    <td align="left" width="1%">:</td>
                    <td align="left" width="10%">
                        <?php $maintenanceObj->DropDownMenu($maintenanceObj->makeArr($maintenanceObj->listDiplaySpecs(),'displaySpecsId','displaySpecsDesc','0'),'cmb_specs','','class="selectBox"'); ?>
                    </td>       
                    <td align="left" width="7%">GROUP</td>
                    <td align="left" width="1%">:</td>
                    <td align="left" width="10%">
                        <?php $maintenanceObj->DropDownMenu($maintenanceObj->makeArr($maintenanceObj->getGrpList(),'minCode','description','0'),'cmb_group','','class="selectBox"'); ?>
                    </td>
                    <td align="left" width="7%">SERIES</td>
                    <td align="left" width="1%">:</td>
                    <td align="left">
                        <input type="text" name="txtSeries" id="txtSeries" class="selectBox" maxlength="7" style="width: 100px;">
                        <!-- onkeypress="return isNumberKey(event) -->
                    </td>
                </tr>
            </table>
            <br/>
        </div>
        <br/>  
        <div style="background: white; border-radius: 4px; border: black; padding-left: 5px; padding-right: 5px;">
            <br/> 
            <div id="activity_pane" align="center">
               <div style="width: 100%;" id="div_display_rentables">     
               </div>
            </div> 
            <br/>
        </div>
         
        <div id='dialogAlert' title='STS' style="display:none">
        </div>   
         
    </body>   
</html>