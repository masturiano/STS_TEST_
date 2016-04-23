<?
	//created by: vincent c de torres
	session_start();
	require("../inventory/lbd_function.php");
	require_once "../../includes/config.php";
	require_once "../../functions/db_function.php";
	include("consess_release_function.php");

	$db = new DB;
	$db->connect();

	$compCode = $_SESSION['comp_code'];
	$user = $_SESSION['userid'];
	
	if($_GET['action'] == 'doRelease'){
		foreach ((array)($_POST['chkbox']) as $index => $value){
			ConsessRelease($value,$compCode,$user);
			echo "<script>location.href='{$_SERVER['PHP_SELF']}'</script>";
		}	
	}
		
	$qryprEvntHdr = "SELECT * FROM tblConsessHdr WHERE compCode = '{$compCode}' AND prEventStatus = 'O' ";
	if($_GET['action'] == 'DoSearch'){
		if(!empty($_POST['txtSearch'])){
			if($_POST[cmbSearch] == 'EVENT NUMBER'){	
				$qryprEvntHdr .= " AND PREVENTNUMBER LIKE '{$_POST['txtSearch']}%' ";
			}else{
				$qryprEvntHdr .= " AND PREVENTDESC LIKE '{$_POST['txtSearch']}%' ";
			}
		}
	}
	$resprEvntHdr = mssql_query($qryprEvntHdr);



?>
<html>
	<head><title>Price Event Release</title>
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
		<form name="frmprEvntEntry" id="frmprEvntEntry" action="<?=$_SERVER['PHP_SELF']?>" method="POST">
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
					<th height="23" colspan="6" align="center" nowrap="nowrap" class="style6">
					<?php echo "<< CONCESSIONAIRE EVENT RELEASE >>";?>        
			        </th>
				</tr>
				<tr nowrap="wrap" align="left" bgcolor="#6AB5FF">
					<th height="23" colspan="6" nowrap="nowrap" class="style6" align="center">
					    <a href="#" onClick="ViewReleasePrice();">View All Release Concessionaire Events Report</a>					
			        </th>
				</tr>
				<tr bgcolor="#DEEDD1" >
					<td class="style3" align="center" width="1">
						#
					</td>
					<td class="style3" align="center" width="100">
						Concessionaire Event Number
					</td>
					<td width="400" align="center" bgcolor="#DEEDD1" class="style3">
						Concessionaire Event Description
					</td>
					<td class="style3" align="center">
						Start Date
					</td>
					<td class="style3" align="center">
						End Date
					</td>
					<td class="style3" align="center" width="100">
						Price Event Status
					</td>
				</tr>
				<?
				while ($rowprEvntHdr = mssql_fetch_array($resprEvntHdr)) {
					$bgcolor = ($i++ % 2) ? "#EAEAEA" : "#F2FEFF";
					$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#97CBFF' . '\';"'
					. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';
				?>
				<tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?> style="cursor:pointer;" >
					<td align="center"><input type='checkbox' name='chkbox[<?=$i?>]' value='<?=$rowprEvntHdr[1]?>' id='btncheck<?=$i?>' onclick="fChk()"></td>
					<td class="style3">
						<?=$rowprEvntHdr[1]?>
					</td>
					<td class="style3">
						<?=strtoupper($rowprEvntHdr[8])?>
					</td>
					<td class="style3" align="center">
						<?if($rowprEvntHdr[5] != ""){ echo date('F-d-Y', strtotime($rowprEvntHdr[5])); }else { echo "---";}?>
					</td>
					<td class="style3" align="center">
						<?if($rowprEvntHdr[6] != ""){ echo date('F-d-Y', strtotime($rowprEvntHdr[6])); }else { echo "---";}?>
					</td>
					<td class="style3" align="center">
						<?echo ($rowprEvntHdr[11] == 'O') ? "OPEN" : "---";?>
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
			document.frmprEvntEntry.action="<?php $_SERVER['PHP_SELF']?>?action=DoSearch";
			document.frmprEvntEntry.submit();
		}
	}
	
	function checkAll()
	{	
		var frm = document.frmprEvntEntry;
		var cnt = parseInt(frm.txtCOUNT.value);
		for (i=1; i<=cnt; i++){
			eval("frm.btncheck" + i + ".checked=true;");
		}
	}
	
	function clearSelection()
	{
		var frm = document.frmprEvntEntry;
		var cnt = parseInt(frm.txtCOUNT.value);
		for (i=1; i<=cnt; i++){
			eval("frm.btncheck" + i + ".checked=false;");
		}
	}
	
	function fChk(){
		var frm = document.frmprEvntEntry;
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
				document.frmprEvntEntry.action="<?php $_SERVER['PHP_SELF']?>?action=doRelease";
				document.frmprEvntEntry.submit();			
			}	
		}		
	}
	
	function ViewReleasePrice()
	{
		var nwidth = screen.availWidth - 100;
		var nheight = screen.availHeight - 100;
		var nleft = parseInt((screen.availWidth/2) - (nwidth/2));
		var ntop = parseInt((screen.availHeight/2) - (nheight/2));
		
		var winattbr = "menubar=yes,toolbar=yes,location=yes,scrollbars=yes,status=yes,alwaysRaised=yes";
	
		mywindow = window.open("concess_viewrelease_concess.php","ConssesionairePriceEvents",winattbr);	
		mywindow.opener = window;
		mywindow.focus();		
	}
</script>