<?
$uri = explode("/",$_SERVER['REQUEST_URI']);

?>
<TABLE border="0" cellpadding="0" cellspacing="0">
<TR>
<TD>
<ul>
		
    <li><a href="#" <?php echo ($uri['4'] == 'supplierVIP.php') ? 'class="current"' : '';?>  onclick="parent.location.href='http://<?php echo $_SERVER['HTTP_HOST'];?>/<?php echo SYS_NAME;?>/modules/registration/supplierVIP.php'"><span></span>SUPPLIER</a></li>
    <li><a href="#" <?php echo ($uri['4'] == 'employees.php') ? 'class="current"' : '';?> onclick="parent.location.href='http://<?php echo $_SERVER['HTTP_HOST'];?>/<?php echo SYS_NAME;?>/modules/registration/employees.php'" ><span></span>EMPLOYEES</a></li>
    <li><a href="# "<?php echo ($uri['4'] == 'bus.php' || $uri['4'] == 'busPerExclusive.php' || $uri['4'] == 'uploadBusReg.php') ? 'class="current"' : '';?>  onclick="parent.location.href='http://<?php echo $_SERVER['HTTP_HOST'];?>/<?php echo SYS_NAME;?>/modules/registration/bus.php'"><span></span>BUS</a></li>
    <li><a href="#" <?php echo ($uri['4'] == 'walkinExclusive.php') ? 'class="current"' : '';?>onclick="parent.location.href='http://<?php echo $_SERVER['HTTP_HOST'];?>/<?php echo SYS_NAME;?>/modules/registration/walkinExclusive.php'"><span></span>MEMBER</a></li>
    <li><a href="#" <?php echo ($uri['4'] == 'guestExtra.php') ? 'class="current"' : '';?> onclick="parent.location.href='http://<?php echo $_SERVER['HTTP_HOST'];?>/<?php echo SYS_NAME;?>/modules/registration/guestExtra.php'"><span></span>GUEST</a></li>
    
</ul>
</TD>
</TR>   
<TR>
	<TD>
		<DIV id="tabMenu" style="width:100%;display:none;background:#191919;border:1px outset gold;-moz-border-radius:10px 10px 10px 10px;"></DIV>
	</TD>
</TR>
</TABLE>

