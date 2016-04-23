<?
	//programmer: vincent c de torres
	//date created : dec 5, 2008
	//rock the code
	include("../../includes/config.php");
	include("../../functions/db_function.php");
	include("../inventory/lbd_function.php");
	include("../../functions/pager_function.php");

	$db = new DB;
	$db->connect();
	
//pager settings
$intLimit = 18;
$intOffset = $_GET['limit_start'];
$cUrl = "sample_pager.php";
if($intOffset == ""){
	$intOffset = 0;
}
//end of pager settings	
	
	switch ($_GET['action']){
		case 'delecomp':
				$qryDelComp   = "DELETE FROM tblCompany WHERE compCode = '{$_GET['compCode']}'";
				$resDelComp   = mssql_query($qryDelComp);
				
				$qryDelAdjNo  = "DELETE FROM tblAdjustmentNo WHERE compCode = '{$_POST['cCde']}'";
				$resDelAdjNo  = mssql_query($qryDelAdjNo);
				
				$qryDelPoNo   = "DELETE FROM tblPoNumber WHERE compCode = '{$_POST['cCde']}'";
				$resDelPoNo   = mssql_query($qryDelPoNo);
				
				$qryDelRcrNo  = "DELETE FROM tblRcrNumber WHERE compCode = '{$_POST['cCde']}'";
				$resDelRcrNo  = mssql_query($qryDelRcrNo);
				
				$qryDelEventNo = "DELETE FROM tblEventNo WHERE compCode = '{$_POST['cCde']}'";
				$resDelEventNo = mssql_query($qryDelEventNo);
				
				$qryDelInvNo   = "DELETE FROM tblInvNumber WHERE compCode = '{$_POST['cCde']}'";
				$resDelInvNo   = mssql_query($qryDelInvNo);			
				
				$qryDelRefNo   = "DELETE FROM tblProdMaintRefNo WHERE compCode = '{$_POST['cCde']}' ";
				$resDelRefNo   = mssql_query($qryDelRefNo);
		break;
	}
	
	$qryComp = "SELECT * FROM tblCompany ";
	$resComp = mssql_query($qryComp);
	$intMaxRec = mssql_num_rows($resComp);
	
	$qrySlctComp = "SELECT top $intLimit * FROM tblCompany WHERE compStat = 'A' AND compCode 
							 NOT IN (SELECT TOP $intOffset compCode FROM tblCompany ORDER BY compCode) ";
	if($_GET['action'] == 'DoSearch'){
		if(!empty($_POST['txtSearch'])){
			if($_POST[cmbSearch] == 'COMPANY CODE'){	
			echo	$qrySlctComp .= " AND compCode LIKE '{$_POST['txtSearch']}%' ";
			}else{
				$qrySlctComp .= " AND compName LIKE '{$_POST['txtSearch']}%' ";
			}
		}
	}	
	$qryAllowance .= "ORDER BY compCode ";		
	$resSlctComp = mssql_query($qrySlctComp);
?>
<html>
	<head><title>Company Table</title>
	<style type="text/css">
	.style6 {font-size: 11px; font-family: Verdana, Arial, Helvetica, sans-serif; }
	.style7 {font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px;}
	</style>
	</head>
	<body>
		<form name="frmCompTbl" id="frmCompTbl" method="POST" action="<?=$_SERVER['PHP_SELF']?>">
			<table width="500" border="0" align="center">
				<tr nowrap="wrap" align="left" bgcolor="#6AB5FF">
					<th height="23" colspan="14" nowrap="nowrap" class="style6">
						COMPANY TABLE
					</th>
				</tr>
				<tr nowrap="wrap" align="left" bgcolor="#DEEDD1">
					
      <th height="23" colspan="4" nowrap="nowrap" class="style6"> <img src="../images/s_b_insr.gif" /> 
        <a href="company_entry.php?action=new"> New </a> || <img src="../images/s_f_prnt.gif" /> 
        <a href="company_pdf.php?search_query=<?php echo $qrySlctComp; ?> &search_selection=all_record"  target="_blank"> Print</a> || <img src="../images/search.gif" onclick="fSearch()"/> 
        <a onclick="fSearch()" title="Search">Search</a> 
        <input type="text" name="txtSearch" id="txtSearch" value="<? echo $_POST['txtSearch'] ?>">
							<select name="cmbSearch" id="cmbSearch">
								<option>COMPANY CODE</option>
								<option>COMPANY NAME</option>
							</select>
							<a href='<? echo $_SERVER['PHP_SELF']; ?>'>Reload</a> 
					</th>
				</tr>
				<tr bgcolor="#DEEDD1">
				    <th width="106">Company Code</th>
				    <th width="315" align="center">Company Name</th>
				    <th colspan="2" align="center">Action</th>
				</tr>
				<?php 
					$i = 0;	
					while($rowComp = mssql_fetch_array($resSlctComp)){
					$bgcolor = ($i++ % 2) ? "#EAEAEA" : "#F2FEFF";
					$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#97CBFF' . '\';"'
					. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';	
				?>
			    <tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
			     	<td>
			     		<?=$rowComp[0]?>
			        </td>
					<td width="30" colspan="1">
			        	<?=strtoupper($rowComp[1]);?>
					<td align="center" width="1">
						<img src="../../Images/sap_wrtpn.jpg" alt="Edit" width="20" height="18" border="0" title="Edit Buyer Record"  align="middle" onclick="location.href='company_entry.php?action=upt&compCode=<?=$rowComp[0]?>'"/>
					</td>
					<td align="center" width="1">
						<img onclick="fDeleComp('<?=$rowComp[0]?>')" src="../../Images/mydelete.gif" alt="Print" width="18" height="18" border="0" title="Delete Buyer Record"  />
					</td>
				</tr>
				<?}?>
			</table>
			<?if($intMaxRec > 18){?>
				<table height="40" width="50%" border="0" align="center">
					</tr>
						<td align="center">
							<? echo fPageLinks($intOffset, $intMaxRec,$intLimit,'');?>
						</td>
					</tr>
				</table>
			<?}?>
		</form>
	</body>
</html>
<script>
	function fDeleComp(compCode){
		var delComp = confirm("<<Do You Want To Delete This Company>>");
		if(delComp == true){
			document.frmCompTbl.action="<?=$_SERVER['PHP_SELF']?>?action=delecomp&compCode="+compCode;
			document.frmCompTbl.submit();
		}
	}

	function fSearch(){
		var txtSearch = document.getElementById('txtSearch').value;
		if(txtSearch == ''){
			alert('Fill up text Search');
		}	
		else{
		document.frmCompTbl.action="<?php $_SERVER['PHP_SELF']?>?action=DoSearch";
		document.frmCompTbl.submit();
		}
	}
</script>