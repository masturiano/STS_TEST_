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
		$result = $maintenanceObj->updateRentables($_POST['displaySpecsIdSeries'],$_POST['group'],$_POST['availability']);
		if($result!=0) {
			echo "alert('STS rentables successfully added.');\n";
		} else {
			echo "alert('Error/No Rentables Added.');";
		}
	exit();
	break;
    
    case 'displayRentables':
        ?>        
        <table class="rentableList" id="rentableList" width="100%" border="0" align="center">
            <thead>
                <tr>
                    <td align="center"><b>RENTABLE SPACE</b></td>
                    <td align="center"><b>SERIES</b></td>
                    <td align="center"><b>SIZE SPECS</b></td>
                    <td align="center"><b>CREATED BY</b></td>
                </tr>
            </thead>
            <tbody>
                <?php $arrDisplaySpecsSeries = $maintenanceObj->listDiplaySpecsSeriesNumber($_POST['display_specs_id']);
                    foreach ($arrDisplaySpecsSeries as $val) {
                ?>
                <tr>
                    <td align="left" width="30%"><?=$val['displaySpecsDesc'];?></td>
                    <td align="left" width="30%"><?=$val['series'];?>
                        <input type=hidden id='txt_identity_<?=$val['displaySpecsId'];?><?=$val['series'];?>' name='txt_identity_<?=$val['displaySpecsId'];?><?=$val['series'];?>' value='<?=$val['displaySpecsId'];?><?=$val['series'];?>'>
                    </td>    
                    <td align="left" width="20%"><?=$val['sizeSpecsDesc'];?></td> 
                    <td align="left" width="20%"><?=$val['fullName'];?></td>
                </tr>
                <?php }?>
            </tbody>
            <tfoot></tfoot>
        </table>  
        <?
    exit();
    break;
    
    case 'addSeriesPositive':
        echo $maintenanceObj->addMainSeries($_POST['display_specs_id'],$_POST['display_size_specs_id'],$_SESSION['sts-userId']);
    exit();
    break;
    
    case 'minusSeriesPositive':
        echo $maintenanceObj->minusMainSeries($_POST['display_specs_id']);
    exit();
    break;
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
                url: "display_series.php",
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
                value = $(this).val();
                $.ajax({           
                    url: "display_series.php",
                    type: "POST",
                    data: "action=displayRentables&display_specs_id="+value,
                    success: function(data){   
                        $('#div_display_rentables').html(data);        
                    }         
                });    
            });
        });   
        
        function addSeries(){
            
            specs = $('#cmb_specs').val();
            size_specs = $('#cmb_size_specs').val();
            
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
            else if(size_specs == 0){
                $("#dialogAlert").dialog("destroy");
                $("#dialogAlert").html('Please select display size specs!').dialog({
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
                $('#cmb_size_specs').css("border","red solid 1px"); 
                return false;     
            }
            else{
                $.ajax({           
                    url: "display_series.php",
                    type: "POST",
                    data: "action=addSeriesPositive&display_specs_id="+specs+'&display_size_specs_id='+size_specs,
                    success: function(data){  
                        // ON ADD SERIES  
                        $.ajax({           
                            url: "display_series.php",
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
                return false;     
            }
            else{
                $.ajax({           
                    url: "display_series.php",
                    type: "POST",
                    data: "action=minusSeriesPositive&display_specs_id="+specs,
                    success: function(data){  
                        // ON MINUS SERIES    
                        $.ajax({           
                            url: "display_series.php",
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
        <h2 class="ui-widget-header ui-corner-all" style="padding:5px;">DISPLACE SPECIFICATIONS SERIES</h2>
        
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
                    <td align="left" width="20%">
                        <?php $maintenanceObj->DropDownMenu($maintenanceObj->makeArr($maintenanceObj->listDiplaySpecs(),'displaySpecsId','displaySpecsDesc','0'),'cmb_specs','','class="selectBox"'); ?>
                    </td>
                    <td align="left" width="7%">SIZE SPECS</td>
                    <td align="left" width="1%">:</td>
                    <td align="left">
                        <?php $maintenanceObj->DropDownMenu($maintenanceObj->makeArr($maintenanceObj->listDiplaySizeSpecs(),'sizeSpecsId','sizeSpecsDesc','0'),'cmb_size_specs','','class="selectBox"'); ?>
                    </td>
                </tr>
            </table>
            <br/>
        </div>
        <br/>  
        <div style="background: white; border-radius: 4px; border: black; padding-left: 5px; padding-right: 5px;">
            <br/> 
            <div style="width: 100%;" id="div_display_rentables"> 
                        
            </div>
            <br/>
        </div>
         
        <div id='dialogAlert' title='STS' style="display:none">
        </div>
         
    </body>   
</html>