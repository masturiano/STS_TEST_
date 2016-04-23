<?
	//created by: vincent c de torres
	session_start();
	require("../inventory/lbd_function.php");
	require_once "../../includes/config.php";
	require_once "../../functions/db_function.php";
	include("release_function.php");

	$db = new DB;
	$db->connect();

	$compCode = $_SESSION['comp_code'];
	$user = $_SESSION['userid'];
	
	if($_GET['action'] == 'doRelease'){
		foreach ((array)($_POST['chkbox']) as $index => $value){
			CostRelease($value,$compCode,$user);
		}	
	}	
	
	$qryCstEvntHdr = "SELECT * FROM tblCostEventHeader WHERE compCode = '{$compCode}' AND cstEventStatus = 'O' ";
	if($_GET['action'] == 'DoSearch'){
		if(!empty($_POST['txtSearch'])){
			if($_POST[cmbSearch] == 'EVENT NUMBER'){	
				$qryCstEvntHdr .= " AND CSTEVENTNUMBER LIKE '{$_POST['txtSearch']}%' ";
			}else{
				$qryCstEvntHdr .= " AND CSTCOSTDESC LIKE '{$_POST['txtSearch']}%' ";
			}
		}
	}
	$resCstEvntHdr = mssql_query($qryCstEvntHdr);


	

?>
<html>
	<head><title>Cost Event Release</title>
		<style type="text/css">
		<!--
			.style3 
			{
				font-family: Verdana, Arial, Helvetica, sans-serif;
				font-size: 11px;
				font-weight: bold;
			}
			.styleme 
			{
				font-family: Verdana, Arial, Helvetica, sans-serif;
				font-size: 11px;
			}
			.style6 
			{
				font-size: 11px; font-family: Verdana, Arial, Helvetica, sans-serif; 
			}
			.style7 
			{
				font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px;
			}
		-->
		</style>
	</head>
	<body>
		<form name="frmCstEvntEntry" id="frmCstEvntEntry" action="<?=$_SERVER['PHP_SELF']?>" method="POST">
			<div align="center"><input type="text" name="txtSearch" id="txtSearch" value="<? echo $_POST['txtSearch'] ?>">
			<select name="cmbSearch" id="cmbSearch">
				<option>EVENT NUMBER</option>
				<option>EVENT DESCRIPTION</option>
			</select>
			<img src="../images/search.gif" onclick="fSearch()" title="Search" />
			<font size="2"><b><a href="#" onclick="fSearch()" title="Search">Search</a></b></font>
			<font size="2"><b><a href='<? echo $_SERVER['PHP_SELF']; ?>'>Refresh</a></b></font></div>
			<table align="center" border="0">
				<tr nowrap="wrap" align="left" bgcolor="#6AB5FF">
					<th height="23" colspan="6" nowrap="nowrap" class="style6" align="center">
					<?php echo "<< COST EVENT RELEASE >>";?>        
			        </th>
				</tr>
				<tr nowrap="wrap" align="left" bgcolor="#6AB5FF">
					<th height="23" colspan="6" align="center" nowrap="nowrap" class="style6">
					    <a href="#" onClick="ViewReleaseCost();">View All Release Cost Events Report</a>					
			        </th>
				</tr>
				<tr bgcolor="#DEEDD1" >
					<td class="style3" align="center" width="1">
						#
					</td>
					<td class="style3" align="center" width="100">
						Cost Event Number
					</td>
					<td width="400" align="center" bgcolor="#DEEDD1" class="style3">
						Cost Event Description
					</td>
					<td class="style3" align="center" width="100">
						Start Date
					</td>
					<td class="style3" align="center" width="100">
						End Date
					</td>
					<td class="style3" align="center" width="100">
						Cost Event Status
					</td>
				</tr>
				<?
				while ($rowCstEvntHdr = mssql_fetch_array($resCstEvntHdr)) {
					$bgcolor = ($i++ % 2) ? "#EAEAEA" : "#F2FEFF";
					$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#97CBFF' . '\';"'
					. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';
				?>
				<tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?> style="cursor:pointer;" >
					<td align="center"><input type='checkbox' name='chkbox[<?=$i?>]' value='<?=$rowCstEvntHdr[1]?>' id='btncheck<?=$i?>' onclick="fChk()"></td>
					<td class="style3">
						<?=$rowCstEvntHdr[1]?>
					</td>
					<td class="style3">
						<?=strtoupper($rowCstEvntHdr[9])?>
					</td>
					<td class="style3">
						<?=date('F-d-Y', strtotime($rowCstEvntHdr[7]))?>
					</td>
					<td class="style3" align="center">
						<?if($rowCstEvntHdr[8] != ""){ echo date('F-d-Y', strtotime($rowCstEvntHdr[8])); }else { echo "---"; }?>
					</td>
					<td class="style3" align="center">
						<?echo ($rowCstEvntHdr[12] == 'O') ? "OPEN" : "---";?>
					</td>
				</tr>
				<?}?>
				<tr>
					<td colspan="6" align="center" class="style3">
						<a onClick="checkAll(); fChk();" ><font color="Blue" style="cursor: pointer;" >Select All</font></a> /
						<a onClick="clearSelection(); fChk();"><font color="Blue" style="cursor: pointer;" >Uncheck All</font></a>&nbsp;
						<input type="button" name="btnRelease" id="btnRelease" value="Release" onclick="fRelease()">
					</td>
				</tr>
			</table>
			<input name="txtCOUNT" type="hidden" id="txtCOUNT" value="<?php echo $i; ?>"> 
			<input type="hidden" name="hdnselect" id="hdnselect">
		</form>
	</body>
</html>
<script>
	function fPassEvntCode(evntCde){
		opener.document.getElementById('txtEventNo').value=evntCde;
		opener.document.getElementById('txtEventNo').focus();
		window.close();
	}
	
	function fSearch(){
		var txtSearch = document.getElementById('txtSearch').value;
		if(txtSearch == ''){
			alert('Fill up text Search');
		}	
		else{
			document.frmCstEvntEntry.action="<?php $_SERVER['PHP_SELF']?>?action=DoSearch";
			document.frmCstEvntEntry.submit();
		}
	}
	
	function checkAll()
	{	
		var frm = document.frmCstEvntEntry;
		var cnt = parseInt(frm.txtCOUNT.value);
		for (i=1; i<=cnt; i++){
			eval("frm.btncheck" + i + ".checked=true;");
		}
	}
	
	function clearSelection()
	{
		var frm = document.frmCstEvntEntry;
		var cnt = parseInt(frm.txtCOUNT.value);
		for (i=1; i<=cnt; i++){
			eval("frm.btncheck" + i + ".checked=false;");
		}
	}
	
	function fChk(){
		var frm = document.frmCstEvntEntry;
		var cnt = parseInt(frm.txtCOUNT.value);
		for (i=1; i<=cnt; i++){
			var chk = eval("frm.btncheck" + i + ".checked;");
			//alert(chk);
			if(chk == true){
				document.getElementById('hdnselect').value='chck';
				return false;
			}
			else{
				document.getElementById('hdnselect').value='none';
				//return false;
			}
		}		
	}	
	
	function fRelease(){
		var hdnslct = document.getElementById('hdnselect').value;
		if(hdnslct == 'none' || hdnslct == ''){
			alert("No Event Selected");
			return false;
		}
		else{
			var rel = confirm("<< Do You Want To Release All Selected Event Number >>");
			if(rel == true){
				document.frmCstEvntEntry.action="<?php $_SERVER['PHP_SELF']?>?action=doRelease";
				document.frmCstEvntEntry.submit();			
			}	
		}		
	}
	
	function ViewReleaseCost()
	{
		var nwidth = screen.availWidth - 600;
		var nheight = screen.availHeight - 500;
		var nleft = parseInt((screen.availWidth/2) - (nwidth/2));
		var ntop = parseInt((screen.availHeight/2) - (nheight/2));
		
		var winattbr = "menubar=yes,toolbar=yes,location=yes,scrollbars=yes,status=yes,alwaysRaised=yes";
	
		mywindow = window.open("cost_viewrelease_cost.php","CostEvents",winattbr);	
		mywindow.opener = window;
		mywindow.focus();		
	}
</script>