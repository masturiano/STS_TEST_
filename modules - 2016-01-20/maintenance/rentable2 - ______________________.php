<?php
session_start();
include("../../includes/db.inc.php");
include("../../includes/common.php");
include("maintenanceObj2.php");
$maintenanceObj = new maintenanceObj;

$now = date('Y-m-d H:i:s');
if ($_SESSION['sts-username'] == "") {
	echo "<SCRIPT>window.parent.location.href='../../index.php';</SCRIPT>";
}    
switch($_POST['action']) {
	case 'saveRentables':
		$result = $maintObj->updateRentables($_POST);
		if($result!=0) {
			echo "dialogAlert('STS rentables successfully added.');\n";
		} else {
			echo "dialogAlert('Error/No Rentables Added.');";
		}
	exit();
	break;
	
	case 'untagRentables':
		$daObj->untagRentables($_POST['refNo'],$_POST['strCode']);
		echo "dialogAlert('Rentables Deleted');";
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
        </style>
    </head>  
    <title>Rentables</title>    
    <body>  
        <h2 class="ui-widget-header ui-corner-all" style="padding:5px;">DISPLACE SPECIFICATIONS</h2>
        
        <div style="background: white; border-radius: 4px; border: black; height: 65px;">
            <br/>
            <table width="99%" border="0" align="right">
                <tr>
                    <td align="left" width="7%">DISPLAY SPECS</td>
                    <td align="left" width="1%">:</td>
                    <td align="left">
                        <?php $maintenanceObj->DropDownMenu($maintenanceObj->makeArr($maintenanceObj->listDiplaySpecs(),'displaySpecsId','displaySpecsDesc',''),'cmbSpecs','','class="selectBox" onChange="emptyDiv();"'); ?>
                    </td>
                </tr>
            </table>
            <br/>
        </div>
        <br/>
        <div style="background: white; border-radius: 4px; border: black; padding-left: 5px; padding-right: 5px;">
            <br/> 
            <div style="width: 100%;">    
                <table id="rentableList" width="100%" border="0" align="center">
                    <thead>
                        <tr>
                            <td align="center"><b>RENTABLE SPACE</b></td>
                            <td align="center"><b>SERIES</b></td>
                            <td align="center"><b>GROUP</b></td>
                            <td align="center"><b>ACTION</b></td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $arrDisplaySpecsSeries = $maintenanceObj->listDiplaySpecsSeries();
                            foreach ($arrDisplaySpecsSeries as $val) {
                        ?>
                        <tr>
                            <td align="left" width="20%"><?=$val['displaySpecsDesc'];?></td>
                            <td align="left" width="20%"><?=$val['series'];?></td>
                            <td align="left" width="20%"><?=$val['displaySpecsDesc'];?></td>
                            <td align="center" width="5%"><input type="checkbox" id="checkBox" class="case" name="case" value=""/></td>
                        </tr>
                        <?php }?>
                    </tbody>
                    <tfoot></tfoot>
                </table>
            </div>
            <br/>
        </div>
        
    </body>   
</html>