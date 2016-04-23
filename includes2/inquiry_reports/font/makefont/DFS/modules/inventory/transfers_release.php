<?
	//created by: vincent c de torres
	session_start();
	require("../inventory/lbd_function.php");
	require_once "../../includes/config.php";
	require_once "../../functions/db_function.php";
	include("transfer_release_function.php");

	$db = new DB;
	$db->connect();

	$compCode = $_SESSION['comp_code'];
	$user = $_SESSION['userid'];
	
	if($_GET['action'] == 'doRelease'){
		foreach ((array)($_POST['chkbox']) as $index => $value){
			TransferRelease($value,$compCode,$user);
		}	
		echo "<script>alert('<<< Selected Transfer successfully released >>>')</script>";
	}	
	$search = $_POST['txtSearch'];
	$qryCstEvntHdr="SELECT AVG(tblTransferDtl.trfNumber) AS trfNumber, AVG(tblTransferDtl.compCode) AS compCode, SUM(tblTransferDtl.trfQtyIn) AS trfQtyin, 
                    tblTransferHeader.trfStatus, tblTransferHeader.trfDate
					FROM tblTransferDtl INNER JOIN
                    tblTransferHeader ON tblTransferDtl.trfNumber = tblTransferHeader.trfNumber
					WHERE (tblTransferDtl.trfQtyIn > 0) AND (tblTransferDtl.compCode = $compCode) AND (tblTransferHeader.trfStatus = 'O')";
	//$qryCstEvntHdr = "SELECT * FROM tblTransferHeader WHERE compCode = '{$compCode}' AND trfStatus = 'O' ";
	
	if($_GET['action'] == 'DoSearch'){
		if(!empty($_POST['txtSearch'])){
			if($_POST[cmbSearch] == 'TRANSFER NUMBER'){	
				//$qryCstEvntHdr .= " AND trfNumber LIKE '$search%'";
				$qryCstEvntHdr .= " AND (tblTransferDtl.trfNumber LIKE '$search%') ";
			}else{
				//$qryCstEvntHdr .= " AND (trfDate >= '$search' AND trfDate <= '$search')";
				$qryCstEvntHdr .= " AND (tblTransferHeader.trfDate >= '$search' AND tblTransferHeader.trfDate <= '$search') ";
			}
		}
	}
	//$qryCstEvntHdr .= " ORDER BY trfNumber DESC";
	$qryCstEvntHdr .= " GROUP BY tblTransferDtl.trfNumber, tblTransferHeader.trfStatus, tblTransferHeader.trfDate ORDER BY tblTransferDtl.trfNumber DESC";
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
				<option>TRANSFER NUMBER</option>
				<option>TRANSFER DATE</option>
			</select>
			<img src="../images/search.gif" onclick="fSearch()" title="Search" />
			<font size="2"><b><a href="#" onclick="fSearch()" title="Search">Search</a></b></font>
			<font size="2"><b><a href='<? echo $_SERVER['PHP_SELF']; ?>'>Refresh</a></b></font></div>
			
  <table align="center" border="0">
    <tr nowrap="wrap" align="left" bgcolor="#6AB5FF"> 
      <th height="23" colspan="6" nowrap="nowrap" class="style6" align="center"> 
        <?php echo "<< TRANSFER RELEASE >>";?> </th>
    </tr>
    <tr bgcolor="#DEEDD1" > 
      <td class="style3" align="center" width="20"> # </td>
      <td class="style3" align="center" width="120"> Transfer No </td>
      <td class="style3" align="center" width="183"> Transfer Date </td>
      <td class="style3" align="center" width="238"> From Location </td>
      <td class="style3" align="center" width="222"> To Location </td>
      <td class="style3" align="center" width="100"> Transfer Status </td>
    </tr>
    <?
				while ($rowCstEvntHdr = mssql_fetch_array($resCstEvntHdr)) {
					$trfNumber = $rowCstEvntHdr[0];
					$resHeader = mssql_query("SELECT * FROM tblTransferHeader WHERE trfNumber = $trfNumber"); 
					$loc1 = mssql_result($resHeader,0,2);
					$loc2 = mssql_result($resHeader,0,5);
					$bgcolor = ($i++ % 2) ? "#EAEAEA" : "#F2FEFF";
					$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#97CBFF' . '\';"'
					. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';
				?>
    <tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?> style="cursor:pointer;" > 
      <td align="center"><input type='checkbox' name='chkbox[<?=$i?>]' value='<?=mssql_result($resHeader,0,1)?>' id='btncheck<?=$i?>' onclick="fChk()"></td>
      <td class="style3"> 
        <?=mssql_result($resHeader,0,1)?>
      </td>
      <td class="style3"> 
        <?=date('F-d-Y', strtotime(mssql_result($resHeader,0,4)))?>
      </td>
      <td class="style3"> 
        <? 
		 	$result_loc1=mssql_query("SELECT * FROM tblLocation WHERE locCode = $loc1");	
			echo strtoupper(mssql_result($resHeader,0,2))." - ".mssql_result($result_loc1,0,"locName"); 
		 ?>
      </td>
      <td class="style3"> 
        <?
		 	$result_loc2=mssql_query("SELECT * FROM tblLocation WHERE locCode = $loc2");	
			echo strtoupper(mssql_result($resHeader,0,5))." - ".mssql_result($result_loc2,0,"locName"); 
		 ?>
      </td>
      <td class="style3" align="center"> <?echo (mssql_result($resHeader,0,11) == 'O') ? "OPEN" : "---";?> 
      </td>
    </tr>
    <?}?>
    <tr> 
      <td colspan="6" align="center" class="style3"> <a onClick="checkAll(); fChk();" ><font color="Blue" style="cursor: pointer;" >Select 
        All</font></a> / <a onClick="clearSelection(); fChk();"><font color="Blue" style="cursor: pointer;" >Uncheck 
        All</font></a>&nbsp; <input type="button" name="btnRelease" id="btnRelease" value="Release" onclick="fRelease()"> 
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
			alert("No Transfer Selected");
			return false;
		}
		else{
			var rel = confirm("<< Do You Want To Release All Selected Transfer Number >>");
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
	
		mywindow = window.open("transfer_released_list.php","Transfer Released",winattbr);	
		mywindow.opener = window;
		mywindow.focus();		
	}
</script>