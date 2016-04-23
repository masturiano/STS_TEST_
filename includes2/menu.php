<?php
$tmpVarServer = explode("/",$_SERVER['SCRIPT_FILENAME']);
?>
<div id="menu">
    <ul class="menu">
    	<li><a href="#" ><span></span></a></li>
        <li><a href="#" ><span>Account</span></a>
            <ul>
                <li><a href="#"><span>Change Password</span></a></li>
                <li><a href="#" onclick="logOut()"><span>Log Out</span></a></li>
            </ul>
        </li>
        <li><a href="#" ><span>Reference Table Maintenance</span></a>
            <ul>
                <li><a onclick="parent.location.href='http://<?php echo $_SERVER['HTTP_HOST'];?>/TAXI/modules/rtables/carList.php'"><span>Car</span></a></li>
                <li><a onclick="parent.location.href='http://<?php echo $_SERVER['HTTP_HOST'];?>/TAXI/modules/rtables/driverList.php'"><span>Driver</span></a></li>
                <li><a onclick="parent.location.href='http://<?php echo $_SERVER['HTTP_HOST'];?>/TAXI/modules/rtables/sparePartList.php'"><span>Spare Parts</span></a></li>
                <li><a onclick="parent.location.href='http://<?php echo $_SERVER['HTTP_HOST'];?>/TAXI/modules/rtables/typeOfServiceList.php'"><span>Type of Service</span></a></li>
            </ul>
        </li>
        <li><a href="#" ><span>Boundery</span></a>
			<ul>
        		<li><a onclick="parent.location.href='http://<?php echo $_SERVER['HTTP_HOST'];?>/TAXI/modules/boundery/boundery.php'"><span>Master List</span></a></li>
        		<li><a onclick="parent.location.href='http://<?php echo $_SERVER['HTTP_HOST'];?>/TAXI/modules/boundery/bounderyEntry.php'"><span>Entry</span></a></li>
        		<li><a onclick="parent.location.href='http://<?php echo $_SERVER['HTTP_HOST'];?>/TAXI/modules/boundery/bounderyReport.php'"><span>Report</span></a></li>
        	</ul>
        </li>
        <li><a  href="#" ><span>Inventory</span></a>
        	<ul>
        		<li><a onclick="parent.location.href='http://<?php echo $_SERVER['HTTP_HOST'];?>/TAXI/modules/inventory/inventory.php'"><span>Master List</span></a></li>
        		<li><a onclick="parent.location.href='http://<?php echo $_SERVER['HTTP_HOST'];?>/TAXI/modules/inventory/inventoryEntry.php'"><span>Entry</span></a></li>
        		<li><a onclick="parent.location.href='http://<?php echo $_SERVER['HTTP_HOST'];?>/TAXI/modules/inventory/inventoryReport.php'"><span>Report</span></a></li>
        	</ul>
        </li>
        <li><a href="#" ><span>Service</span></a>
        	<ul>
        		<li><a onclick="parent.location.href='http://<?php echo $_SERVER['HTTP_HOST'];?>/TAXI/modules/service/service.php'"><span>Master List</span></a></li>
        		<li><a onclick="parent.location.href='http://<?php echo $_SERVER['HTTP_HOST'];?>/TAXI/modules/service/serviceEntry.php'"><span>Entry</span></a></li>
        		<li><a onclick="parent.location.href='http://<?php echo $_SERVER['HTTP_HOST'];?>/TAXI/modules/service/serviceEntry.php'"><span>Report</span></a></li>
        	</ul>
        </li>
    </ul>
</div>
<SCRIPT>
	function logOut(){
		if(confirm("Do You want to Log out?") == true){
			parent.location.href='<?php echo "http://".$_SERVER['HTTP_HOST']."/TAXI/index.php?lo=1" ?>';
		}
	}
</SCRIPT>