<?php
////////////////////////////////////////////////////////////////////////////////////
function dateToday()////////////////////////////////////////////////////////////////
	{
	$dateNow = date("n-j-Y");
	$do = $_GET['do']; $while=$_GET['while']; $ciDate=$_GET['ciDate']; 
	if($do=="newRecord"&&$ciDate=="")
		{ echo $dateNow;}
	else
		{ echo $ciDate;}
	}
////////////////////////////////////////////////////////////////////////////////////
function dateOneToday()////////////////////////////////////////////////////////
	{
	$dateNow = date("n-j-Y", time() + (0 * 24 * 60 * 60)) ;
	$do=$_GET['do']; $while=$_GET['while'];$strfDate=$_GET['strfDate']; 
	if($do=="newRecord"&&$strfDate=="")
		{ echo $dateNow;}
	else
		{ echo $strfDate;}
	}
////////////////////////////////////////////////////////////////////////////////////
function calendar()/////////////////////////////////////////////////////////////////
	{
	$do=$_GET['do']; $while=$_GET['while'];
	if($do=="newRecord"&&$while=="")
		{ echo  "onfocus='this.select();lcs(this)' onclick='event.cancelBubble=true;this.select();lcs(this)'"; }
	else if($do=="newRecord"&&$while=="newInvoice")
		{ }
	}
////////////////////////////////////////////////////////////////////////////////////
function ciNumber()/////////////////////////////////////////////////////////////////
	{
	$do=$_GET['do']; $while=$_GET['while']; 
	if($do=="newRecord")
		{	
		$compcode='1';	
		$conserver = mssql_connect("192.168.200.170","sa","sa") or die("Cannot Connect to server");
		$selectdb = mssql_select_db("DFS") or die("Cannot select database");
		$findlastCiNumber = mssql_query("SELECT lastInvNo
		FROM tblInvNumber 
		WHERE compcode='$compcode'");
		while($findlastCiNumberRow=mssql_fetch_assoc($findlastCiNumber))
			{	$lastInvNo = $findlastCiNumberRow['lastInvNo']; }
		echo "value='".$lastInvNo."' readonly='readonly'";
		}
	}
////////////////////////////////////////////////////////////////////////////////////
function includestrfNumber()////////////////////////////////////////////////////////
	{
	$do=$_GET['do']; $while=$_GET['while'];  
	$strfNumber=$_GET['strfNumber'];
	if($do=="newRecrod"&&$strfNumber=="")
		{ }
	else if($do=="newRecord"&&$while=="newInvoice")
		{ echo "value='".$strfNumber."' disabled='disabled'"; }
	else
		{ echo "value='".$strfNumber."'";}
	}
////////////////////////////////////////////////////////////////////////////////////
function customerCode()/////////////////////////////////////////////////////////////
	{
	$do=$_GET['do']; $while=$_GET['while'];  
	$custCode=$_GET['custCode'];
	if($do=="newRecord"&&$custCode=="")
		{ }
	else if($do=="newRecord"&&$while=="newInvoice")
		{ echo "disabled='disabled' value='".$custCode."'";}
	else
		{ echo "value='".$custCode."'";}
	}
////////////////////////////////////////////////////////////////////////////////////
function customerName()/////////////////////////////////////////////////////////////
	{
	$do=$_GET['do']; $while=$_GET['while'];  
	$custCode=$_GET['custCode'];
	if($custCode!="")
		{
		$compcode='1';	
		$conserver = mssql_connect("192.168.200.170","sa","sa") or die("Cannot Connect to server");
		$selectdb = mssql_select_db("DFS") or die("Cannot select database");
		$findCustomerName = mssql_query("SELECT custName
		FROM tblCustMast 
		WHERE compcode='$compcode' 
		AND custCode='$custCode'");
		while($findCustomerNameRow=mssql_fetch_assoc($findCustomerName))
			{	$custName = strtoupper($findCustomerNameRow['custName']); }
		mssql_close();
		echo "value='".$custName."'";
		}
	}
////////////////////////////////////////////////////////////////////////////////////
function customerTerms()////////////////////////////////////////////////////////////
	{
	$do=$_GET['do']; $while=$_GET['while'];  
	$custCode=$_GET['custCode'];
	if($custCode!="")
		{
		$compcode='1';	
		$conserver = mssql_connect("192.168.200.170","sa","sa") or die("Cannot Connect to server");
		$selectdb = mssql_select_db("DFS") or die("Cannot select database");
		$findCustomerName = mssql_query("SELECT custTerms
		FROM tblCustMast 
		WHERE compcode='$compcode' 
		AND custCode='$custCode'");
		while($findCustomerNameRow=mssql_fetch_assoc($findCustomerName))
			{	$custTerms = strtoupper($findCustomerNameRow['custTerms']); }
		mssql_close();
		echo "value='".$custTerms."'";
		}
	}
////////////////////////////////////////////////////////////////////////////////////
function locationCode()/////////////////////////////////////////////////////////////
	{
	$do=$_GET['do']; $while=$_GET['while'];  
	$locationCode=$_GET['locationCode'];
	if($do=="newRecord"&&$locationCode=="")
		{ }
	else if($do=="newRecord"&&$while=="newInvoice")
		{ echo "disabled='disabled' value='".$locationCode."'";}
	else
		{ echo "value='".$locationCode."'";}
	}
////////////////////////////////////////////////////////////////////////////////////
function locationName()/////////////////////////////////////////////////////////////
	{
	$do=$_GET['do']; $while=$_GET['while'];  
	$locationCode=$_GET['locationCode'];
	if($locationCode!="")
		{
		$compcode='1';	
		$conserver = mssql_connect("192.168.200.170","sa","sa") or die("Cannot Connect to server");
		$selectdb = mssql_select_db("DFS") or die("Cannot select database");
		$findLocationName = mssql_query("SELECT locName
		FROM tblLocation 
		WHERE compcode='$compcode' 
		AND locCode='$locationCode'");
		while($findLocationNameRow=mssql_fetch_assoc($findLocationName))
			{	$locName = strtoupper($findLocationNameRow['locName']); }
		mssql_close();
		echo "value='".$locName."'";
		}
	}
////////////////////////////////////////////////////////////////////////////////////
function pricingR()
	{
	$do=$_GET['do']; $while=$_GET['while'];  
	$pricingToUse=$_GET['pricingToUse'];
	if($pricingToUse=="R")
		{ echo "selected='selected'"; }
	}
function pricingC()
	{
	$do=$_GET['do']; $while=$_GET['while'];  
	$pricingToUse=$_GET['pricingToUse'];
	if($pricingToUse=="C")
		{ echo "selected='selected'"; }
	}
function pricingS()
	{
	$do=$_GET['do']; $while=$_GET['while'];  
	$pricingToUse=$_GET['pricingToUse'];
	if($pricingToUse=="S")
		{ echo "selected='selected'"; }
	}
function pricingP()
	{
	$do=$_GET['do']; $while=$_GET['while'];  
	$pricingToUse=$_GET['pricingToUse'];
	if($pricingToUse=="P")
		{ echo "selected='selected'"; }
	}
function pricingPVal()
	{
	$do=$_GET['do']; $while=$_GET['while'];  
	$pricingToUse=$_GET['pricingToUse']; $pricingSpecial=$_GET['pricingSpecial'];
	if($pricingToUse=="P")
		{ echo "value='".$pricingSpecial."'"; }
	}
////////////////////////////////////////////////////////////////////////////////////
function remarks()/////////////////////////////////////////////////////////////
	{
	$do=$_GET['do']; $while=$_GET['while'];  
	$remarks=$_GET['remarks'];
	if($do=="newRecord"&&$remarks=="")
		{ }
	else if($do=="newRecord"&&$while=="newInvoice")
		{ echo "value='".$remarks."' disabled='disabled'";}
	else
		{ echo "value='".$remarks."'";}
	}
////////////////////////////////////////////////////////////////////////////////////
function prodIte()/////////////////////////////////////////////////////////////
	{
	$do=$_GET['do']; $while=$_GET['while'];  
	$prodIte=$_GET['prodIte'];
	if($do=="newRecord"&&$prodIte=="")
		{ }
	else
		{ echo "value='".$prodIte."'";}
	}
////////////////////////////////////////////////////////////////////////////////////
function prodDesc()/////////////////////////////////////////////////////////////
	{
	$do=$_GET['do']; $while=$_GET['while'];  
	$prodIte=$_GET['prodIte'];
	if($do=="newRecord"&&$prodIte=="")
		{ }
	else
		{	$compcode='1';	
			$conserver = mssql_connect("192.168.200.170","sa","sa") or die("Cannot Connect to server");
			$selectdb = mssql_select_db("DFS") or die("Cannot select database");
			$findDesc = mssql_query("SELECT prdDesc
			FROM tblProdMast 
			WHERE prdNumber='$prodIte'");
			while($findDescRow=mssql_fetch_assoc($findDesc))
				{	$prdDesc = ucwords(strtolower($findDescRow['prdDesc']));}		
			mssql_close();
		echo "value='".$prdDesc."'";}
	}
////////////////////////////////////////////////////////////////////////////////////
function prodUm()/////////////////////////////////////////////////////////////
	{
	$do=$_GET['do']; $while=$_GET['while'];  
	$prodIte=$_GET['prodIte'];
	if($do=="newRecord"&&$prodIte=="")
		{ }
	else
		{	$compcode='1';	
			$conserver = mssql_connect("192.168.200.170","sa","sa") or die("Cannot Connect to server");
			$selectdb = mssql_select_db("DFS") or die("Cannot select database");
			$findUm = mssql_query("SELECT prdBuyUnit,prdConv
			FROM tblProdMast 
			WHERE prdNumber='$prodIte'");
			while($findUmRow=mssql_fetch_assoc($findUm))
				{	$prdBuyUnit = strtoupper($findUmRow['prdBuyUnit']);
					$prdConv = $findUmRow['prdConv'];}		
			mssql_close();
		echo "value='".$prdBuyUnit."-".$prdConv."'";}
	}
function unitPrice()
	{
	$do=$_GET['do']; $while=$_GET['while'];  
	$prodIte=$_GET['prodIte']; $pricingToUse=$_GET['pricingToUse']; $pricingSpecial=$_GET['pricingSpecial'];
	if($do=="newRecord"&&$prodIte=="")
		{ }
	else if($do=="newRecord"&&$prodIte!=""&&$pricingToUse=="R")
		{	$compcode='1';	
			$conserver = mssql_connect("192.168.200.170","sa","sa") or die("Cannot Connect to server");
			$selectdb = mssql_select_db("DFS") or die("Cannot select database");
			$findRUP = mssql_query("SELECT regUnitPrice
			FROM tblProdPrice 
			WHERE prdNumber='$prodIte'");
			while($findRUPRow=mssql_fetch_assoc($findRUP))
				{	$regUnitPrice = $findRUPRow['regUnitPrice'];}		
			mssql_close();
		echo "value='".$regUnitPrice."'  readonly='readonly'";}
	else if($do=="newRecord"&&$prodIte!=""&&$pricingToUse=="C")
		{	$compcode='1';	
			$conserver = mssql_connect("192.168.200.170","sa","sa") or die("Cannot Connect to server");
			$selectdb = mssql_select_db("DFS") or die("Cannot select database");
			$findAUC = mssql_query("SELECT aveUnitCost
			FROM tblAveCost 
			WHERE prdNumber='$prodIte'");
			while($findAUCRow=mssql_fetch_assoc($findAUC))
				{	$aveUnitCost = $findAUCRow['aveUnitCost'];}		
			mssql_close();
		echo "value='".$aveUnitCost."'  readonly='readonly'";}
	else if($do=="newRecord"&&$prodIte!=""&&$pricingToUse=="P")
		{	$compcode='1';	
			$conserver = mssql_connect("192.168.200.170","sa","sa") or die("Cannot Connect to server");
			$selectdb = mssql_select_db("DFS") or die("Cannot select database");
			$findAUC = mssql_query("SELECT aveUnitCost
			FROM tblAveCost 
			WHERE prdNumber='$prodIte'");
			while($findAUCRow=mssql_fetch_assoc($findAUC))
				{	$aveUnitCost = $findAUCRow['aveUnitCost'];}	
				$aveUnitCost = $aveUnitCost*(1+($pricingSpecial/100));	
			mssql_close();
		echo "value='".$aveUnitCost."' readonly='readonly'";}
	else if($do=="newRecord"&&$prodIte!=""&&$pricingToUse=="S")
		{	
		echo "value='".$aveUnitCost."'";}
	}
////////////////////////////////////////////////////////////////////////////////////
function newRecord()////////////////////////////////////////////////////////////////
	{
	$do = $_GET['do'];
	if($do=="")
		{ echo "<input type='button' class='queryButton' name='addButt' id='addButt' value='New' title='New' 
		onclick='newRecord()'/>"; }
	else if($do=="newRecord")
		{ echo "<input type='button' class='queryButton' name='addButt' id='addButt' value='New' title='New' 
		disabled='disabled'/>"; }
	}	
////////////////////////////////////////////////////////////////////////////////////
function updateRecord()////////////////////////////////////////////////////////////////
	{
	echo "<input type='button' class='queryButton' name='addButt' id='addButt' value='Update' title='Update' onclick='updateRecord()'/>";
	}
////////////////////////////////////////////////////////////////////////////////////
function deleteRecord()////////////////////////////////////////////////////////////////
	{
	echo "<input type='button' class='queryButton' name='addButt' id='addButt' value='Delete' title='New' onclick='deleteRecordNow()'/>";
	}
////////////////////////////////////////////////////////////////////////////////////
function saveRecord()///////////////////////////////////////////////////////////////
	{
	$do = $_GET['do']; $while=$_GET['while'];
	if($do=="newRecord"&&$while!="newInvoice")
		{
		echo "
		<input type='button' class='queryButton' name='saveRecordbutt' id='saveRecordbutt' value='Save Record' title='Save Record' onclick='saveRecordNow()'/>"; }
	else if(($do=="newRecord"&&$while=="newInvoice")||($do=="newRecord"&&$while=="findIte"))
		{
		echo "
		<input type='button' class='queryButton' name='saveRecordbutt' id='saveRecordbutt' value='Insert Record' title='Insert Record' onclick='saveNewRecordNow()'/>";
		}
	}
////////////////////////////////////////////////////////////////////////////////////
function cancelButton()/////////////////////////////////////////////////////////////
	{
	$do = $_GET['do']; $while=$_GET['while'];
	if($do=="")
		{	echo "<input type='button' class='queryButton' name='cancelButt' id='cancelButt' value='Cancel' title='Cancel' 
		disabled='disabled'/>"; }
	else if($do=="newRecord")
		{	echo "<input type='button' class='queryButton' name='cancelButt' id='cancelButt' value='Cancel' title='Cancel' onclick='canc()'/>"; }
	else if($do=="deleteRecordList"||$do=="updateSet")
		{	echo "<input type='button' class='queryButton' name='cancelButt' id='cancelButt' value='Cancel' title='Cancel' onclick='canc()'/>"; }
	}
////////////////////////////////////////////////////////////////////////////////////
function includedInvoice()//////////////////////////////////////////////////////////
	{
	$do = $_GET['do']; $while=$_GET['while'];
	if($do=="newRecord")
		{ echo "
			<tr bgcolor='#E9F8E8'>
	        <td><center>-</center></td>
    	    <td><center>
				<input name='prodIte' type='text' class='pricePriceInput' id='prodIte' tabindex='1' maxlength='6' onfocus='checkHeader()'"; includeLookProduct(); prodIte(); echo ">
				</center></td>
			<td>
				<input name='prodDesc' type='text' class='pricePriceDesc' id='prodDesc' ";  prodDesc(); echo " readonly='readonly' />
				</td>
			<td><center>
				<input name='prodUm' type='text' class='pricePriceUmUm' id='prodUm' maxlength='14'"; prodUm();  echo "readonly='readonly' />
				</center></td>
			<td><center>
				<input name='prodQty1' type='text' class='pricePriceInput' id='prodQty1' tabindex='1' maxlength='6' onfocus='checkHeader()' ";  echo ">
				</center></td>
			<td><center>
				<input name='prodQty2' type='text' class='pricePriceInput' id='prodQty2' tabindex='1' maxlength='6' onfocus='checkHeader()' ";  echo ">
				</center></td>
			<td><center>
				<input name='prodQty3' type='text' class='pricePriceInput' id='prodQty3' tabindex='1' maxlength='6' onfocus='checkHeader()' ";  echo ">
				</center></td>
			<td><center>
				<input name='prodQty4' type='text' class='pricePriceInput' id='prodQty4' tabindex='1' maxlength='6' onfocus='checkHeader()' ";  echo ">
				</center></td>
			<td><center>
				<input name='prodDisc' type='text' class='pricePriceInput' id='prodDisc' tabindex='1' maxlength='6' onfocus='checkHeader()' ";  echo ">
				</center></td>
			<td><center>
				<input name='prodUniPri' type='text' class='pricePrice' id='prodUniPri' maxlength='6' onfocus='checkHeader()' ";  unitPrice(); echo ">
				</center></td>
			<td><center></center></td>
			</tr>"; }
	}
function includedInvoices()	
	{
	$do=$_GET['do']; $while=$_GET['while'];
	$ciNumber=$_GET['ciNumber']; $ciStatus=$_GET['ciStatus']; $ciDate=$_GET['ciDate']; $strfNumber=$_GET['strfNumber'];
	$strfDate=$_GET['strfDate']; $custCode=$_GET['custCode']; $locationCode = $_GET['locationCode'];
	$pricingToUse=$_GET['pricingToUse']; $pricingSpecial=$_GET['pricingSpecial']; $remarks=$_GET['remarks'];
	$prodIte=$_GET['prodIte'];$prodDisc=$_GET['prodDisc'];$prodUniPri=$_GET['prodUniPri'];
	$prodQty1=$_GET['prodQty1'];$prodQty2=$_GET['prodQty2'];$prodQty3=$_GET['prodQty3'];$prodQty4=$_GET['prodQty4'];
	
	if($do="newRecord"&&$while="newInvoice")
		{
		$compcode='1';	
		$conserver = mssql_connect("192.168.200.170","sa","sa") or die("Cannot Connect to server");
		$selectdb = mssql_select_db("DFS") or die("Cannot select database");
		$findInvoices = mssql_query("SELECT *
		FROM tblCiItemDtl 
		WHERE compcode='$compcode' 
		AND ciNumber='$ciNumber'");
		$counter = 0;
		while($findInvoicesRow=mssql_fetch_assoc($findInvoices))
			{	$counter = $counter + 1;
				$prdNumber = $findInvoicesRow['prdNumber']; 
				$prdBuyUnit = $findInvoicesRow['prdBuyUnit']; 
				$prdConv = $findInvoicesRow['prdConv']; 
				$qtyRegPk = $findInvoicesRow['qtyRegPk']; 
				$qtyFreePk = $findInvoicesRow['qtyFreePk']; 
				$qtyRegPc = $findInvoicesRow['qtyRegPc']; 
				$qtyFreePc = $findInvoicesRow['qtyFreePc']; 
				$ciDiscPcent = $findInvoicesRow['ciDiscPcent']; 
				$ciUnitPrice = $findInvoicesRow['ciUnitPrice']; 
				$prdBC =  $prdBuyUnit."-".$prdConv;
				
			$findInvoicesDesc = mssql_query("SELECT prdDesc
			FROM tblProdMast 
			WHERE prdNumber='$prdNumber'");
			
			while($findInvoicesDescRow=mssql_fetch_assoc($findInvoicesDesc))
				{
				$prdDesc = ucwords(strtolower($findInvoicesDescRow['prdDesc']));
				}
	echo "<tr>
        <td><center>$counter</center></td>
        <td><center>$prdNumber</center></td>
        <td>$prdDesc</td>
        <td><center>$prdBC</center></td>
        <td><center>$qtyRegPk</center></td>
        <td><center>$qtyFreePk</center></td>
        <td><center>$qtyRegPc</center></td>
        <td><center>$qtyFreePc</center></td>
        <td><center>$ciDiscPcent</center></td>
        <td><div align='right'>$ciUnitPrice</div></td>
		<td><div align='center'>";
		if(mssql_num_rows($findInvoices)<=1)
			{ 	echo "---"; }
		else if(mssql_num_rows($findInvoices)>=2)
			{	echo "
				<a href='ci_transaction.php?do=newRecord&while=deleteItemNow&ciNumber=$ciNumber&ciStatus=$ciStatus&ciDate=$ciDate&strfNumber=$strfNumber&strfDate=$strfDate&custCode=$custCode&locationCode=$locationCode&pricingToUse=$pricingToUse&pricingSpecial=$pricingSpecial&remarks=$remarks&prodIte=$prdNumber'
				onclick='var deleteChild = confirm(\"This Item Product will be Deleted. Are you sure?\"); 
				if(deleteChild) { return true; } else { return false; }'><u>del</u></a>"; }
		echo "</div></td>
      </tr>"; }
	  	}
	}
////////////////////////////////////////////////////////////////////////////////////		
function includeLookProduct()
	{
	$do=$_GET['do']; $while=$_GET['while'];
	if($do=="newRecord"&&$while=="findIte"||$while=="findLocation")
		{ echo "onchange='lookProduct()'"; }
	else if($do=="newRecord"&&$while=="newInvoice")
		{ echo "onchange='lookNewProduct()'"; }
	}
////////////////////////////////////////////////////////////////////////////////////
?>
<script src="calendar.js"></script>
<!--THIS IS THE CSS-->
<link rel='stylesheet' type='text/css' href='../../includes/style.css'></link><div id='frame_body'>
<!--THIS IS THE HEADER-->
<hr>
    <form method="post">
<div class='header'>
  <div class='details'>
    <div class='header'>
      <table width="100%" border="0" cellpadding="0" cellspacing="0">
      <tr>
          <td width="50%">&nbsp;</td>
          <td colspan="2"><div align='right'>
                <?php newRecord(); ?>
                <?php updateRecord(); ?>
                <?php deleteRecord(); ?>
                <?php saveRecord(); ?>
                <?php cancelButton(); ?>
                    </div></td>
          </tr>
        <tr>
          <td width="55%" rowspan="2" align="center" valign="top"><table width="85%" border="0" align="center">
          <tr>
              <td width="25%"><b>CI Number:</b></td>
              <td><b><b>
                <input type="text" class="readonly_textbox"  name="ciNumber" id="ciNumber" tabindex='1' readonly="readonly" maxlength="11" <?php ciNumber(); ?>/>
              </b></b></td>
              <td width="25%"><b>
                <div align="right">CI Status:</div>
              </b></td>
              <td width="25%" colspan="2"><select name="ciStatus" class="textbox" tabindex='1' id="ciStatus">
                <option value="O">Open</option>
              </select></td>
            </tr>
            <tr>
              <td width="25%"><b>CI Date</b>:</td>
              <td><b><b>
                <input type="text" class="readonly_textbox"  name="ciDate" id="ciDate" maxlength="11" 
          <?php calendar(); ?> value='<?php dateToday(); ?>' readonly="readonly"/>
              </b></b></td>
              <td width="25%">&nbsp;</td>
              <td width="25%" colspan="2">&nbsp;</td>
            </tr>
            <tr>
              <td colspan="5"></td>
              </tr>
            <tr>
              <td width="25%"><b>STRF Number:</b></td>
              <td><input name="strfNumber" type='text' class='pricePriceInput' id="strfNumber" tabindex='1'  maxlength="9"
             <?php includestrfNumber(); ?>/></td>
              <td width="25%">&nbsp;</td>
              <td width="25%" colspan="2">&nbsp;</td>
            </tr>
            <tr>
              <td><b>Date:</b></td>
              <td><b><b>
                <input type="text" class="readonly_textbox"  name="strfDate" id="strfDate" maxlength="11" 
           <?php calendar(); ?>value='<?php dateOneToday(); ?>' readonly="readonly"/>
              </b></b></td>
              <td><strong>
                <div align="right">Terms:</div>
              </strong></td>
              <td colspan="2"><b><b>
                <input type="text" class="readonly_textbox"  name="strfTerms" id="strfTerms" maxlength="11" 
          readonly="readonly" <?php customerTerms(); ?>/>
              </b></b></td>
            </tr>

            <tr>
              <td><b>Customer:</b></td>
              <td width="20%"><b><b>
                  <input name="custCode" type='text' class='pricePriceInput' id="custCode" tabindex='1'  maxlength="6"
                  onchange="lookCustomer()" <?php customerCode(); ?>/></td>
              <td colspan="3"><input type="text" class="readonly_textbox"  name="custName" id="custName" readonly="readonly"
              <?php customerName(); ?>/>              </td>
              </tr>
            <tr>
              <td><b>Location:</b></td>
              <td><b><b>
                <input name="locationCode" type='text' class='pricePriceInput' id="locationCode" tabindex='1'  maxlength="6"
                onchange="lookLocation()" <?php locationcode(); ?>/>
              </b></b></td>
              <td colspan="3"><b><b>
                <input type="text" class="readonly_textbox"  name="locationName" id="locationName" readonly="readonly"
                <?php locationName(); ?>/>
              </b></b></td>
              </tr>
            <tr>
              <td><b>Pricing to Use:</b></td>
              <td colspan="2"><select name="pricingToUse" class="textbox" tabindex='1' id="pricingToUse">
                  <option value="R" onclick="pricingToUseNULL()" <?php pricingR(); ?>>Regular Price</option>
                  <option value="C" onclick="pricingToUseNULL()" <?php pricingC(); ?>>Cost</option>
                  <option value="S" onclick="pricingToUseNULL()" <?php pricingS(); ?>>Special</option>
                  <option value="P" onclick="pricingToUse()" <?php pricingP(); ?>>Cost Plus</option>
              </select></td>
              <td width="12%"><b>Percentage:</b></td>
              <td width="13%"><b><b>
                <input type="text" class="readonly_textbox"  name="pricingSpecial" id="pricingSpecial" maxlength="3"
                <?php pricingPVal(); ?> onchange="pricingCostPlus()"/>
              </b></b></td>
            </tr>
            <tr>
              <td><b>Remarks:</b></td>
              <td colspan="4"><b><b>
                <input name="remarks" type='text' class='readonly_textbox' id="remarks" tabindex='1' maxlength="50"
				<?php remarks(); ?>/>
              </b></b></td>
            </tr>
             <tr>
              <th colspan="5"></th>
              </tr>
          </table></td>
          <td width="4%">&nbsp;</td>
          <td width="53%">&nbsp;</td>
        </tr>
        <tr>
          <td align="center" valign="top">&nbsp;</td>
          <td align="center" valign="bottom"><table width="85%" border="0" align="center" bgcolor="#E9F8E8">
              <tr>
                <th><b>
                  <div align="center">Control Totals</div>
                </b></th>
              </tr>
              <tr>
                <td><table width="50%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#E9F8E8">
                    <tr>
                      <td><div align="center"><b>Items</b></div></td>
                      <td><div align="center"><b>Hash</b></div></td>
                      <td><div align="center"><b>Entered</b></div></td>
                      <td><div align="center"><b>Difference</b></div></td>
                    </tr>
                    <tr>
                      <td>Qty Reg (Pk/Bx)</td>
                      <td><input name="hashReg1" type='text' class='pricePrice' id="hashReg1" value="0" readonly='readonly'/></td>
                      <td>
                        <input name="enter1" type='text' class='pricePrice' id="enter1" value="0" readonly='readonly'/>
                      </td>
                      <td>
                        <input name="diff1" type='text' class='pricePrice' id="innerPack3" value="0" readonly='readonly'/>
                      </td>
                    </tr>
                    <tr>
                      <td>Qty Free (Pk/Bx)</td>
                      <td><input name="hashReg2" type='text' class='pricePrice' id="hashReg2" value="0" readonly='readonly'/></td>
                      <td>
                        <input name="enter2" type='text' class='pricePrice' id="enter2" value="0" readonly='readonly'/>
                      </td>
                      <td>
                        <input name="diff2" type='text' class='pricePrice' id="innerPack4" value="0" readonly='readonly'/>
                      </td>
                    </tr>
                    <tr>
                      <td>Qty Reg (Lse/Pc)</td>
                      <td><input name="hashReg3" type='text' class='pricePrice' id="hashReg3" value="0" readonly='readonly'/></td>
                      <td>
                        <input name="enter3" type='text' class='pricePrice' id="enter3" value="0" readonly='readonly'/>
                     </td>
                      <td>
                        <input name="diff3" type='text' class='pricePrice' id="innerPack5" value="0" readonly='readonly'/>                        </td>
                    </tr>
                    <tr>
                      <td>Qty Free (Lse/Pc)</td>
                      <td><input name="hashReg4" type='text' class='pricePrice' id="hashReg4" value="0" readonly='readonly'/></td>
                      <td>
                        <input name="enter4" type='text' class='pricePrice' id="enter4" value="0" readonly='readonly'/>
                     </td>
                      <td>
                        <input name="diff4" type='text' class='pricePrice' id="diff4" value="0" readonly='readonly'/>
                      </td>
                    </tr>
                    
                </table></td>
              </tr>
              <tr>
                <th></th>
              </tr>
          </table></td>
        </tr>
      </table>
    </div>
    <br /><table width="100%" align="center" bordercolor="#E9F8E8">
      <tr>
        <th>--</th>
        <th>Item Code</th>
        <th>Product Description</th>
        <th>U/M-Conv</th>
        <th colspan="2">Qty Ordered <br />
          Pack(s)/Box<br />
          Regular | Free</th>
        <th colspan="2">Qty Ordered Loose/Piece(s)<br />
          Regular | Free</th>
        <th>Discount</th>
        <th>Unit Price</th>
        <th>&nbsp;</th>
      </tr>
      <?php includedInvoice(); ?>
      <tr>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
      </tr>
      <?php includedInvoices(); ?>
    </table>
    <br />
  </div>
</div>

<!--Standard Info-->
<div class='details'></div>
<!--SAMPLE FROM-->
</form>
</div>

<!-- footer - status/message -->
<div id='footer'> 
  <? echo $message;?></div>
<!--setfocus on reference search box-->
<script type="text/javascript">
////////////////////////////////////////////////////////////////////////////////////
var ciNumber = document.getElementById('ciNumber').value;
document.getElementById('ciStatus').disabled = true;
document.getElementById('strfNumber').readOnly = true;
document.getElementById('custCode').readOnly = true;
document.getElementById('locationCode').readOnly = true;
document.getElementById('pricingToUse').disabled = true;
var pricingSpecial = document.getElementById('pricingSpecial').value;
if(pricingSpecial=="")
	{
	document.getElementById('pricingSpecial').readOnly = true;
	}
document.getElementById('remarks').readOnly = true;
if(ciNumber!="")
	{
	var strfNumber = document.getElementById('strfNumber').value;
	var custCode = document.getElementById('custCode').value;
	var locationCode = document.getElementById('locationCode').value;
	var remarks = document.getElementById('remarks').value;
	var prodIte = document.getElementById('prodIte').value;
	var prodQty1 = document.getElementById('prodQty1').value;
	var prodUniPri = document.getElementById('prodUniPri').value;
	document.getElementById('ciStatus').disabled = false;
	document.getElementById('strfNumber').readOnly = false;
	if(strfNumber=="")
		{
		document.getElementById('strfNumber').focus();
		}
	else if(custCode=="")
		{
		document.getElementById('custCode').focus();
		}
	else if(locationCode=="")
		{
		document.getElementById('locationCode').focus();
		}
	else if(remarks=="")
		{
		document.getElementById('remarks').focus();
		}
	else if(prodIte=="")
		{
		document.getElementById('prodIte').focus();
		}
	else if(prodQty1=="")
		{
		document.getElementById('prodQty1').focus();
		}
	else if(prodUniPri=="")
		{
		}	
	document.getElementById('custCode').readOnly = false;
	document.getElementById('locationCode').readOnly = false;
	document.getElementById('pricingToUse').disabled = false;
	document.getElementById('remarks').readOnly = false;
	}
////////////////////////////////////////////////////////////////////////////////////
function newRecord()////////////////////////////////////////////////////////////////
	{
	var addbutt = document.getElementById('addButt').value;
	window.location="ci_transaction.php?do=newRecord";
	}
////////////////////////////////////////////////////////////////////////////////////
function canc()/////////////////////////////////////////////////////////////////////
	{
	var cancelnow = confirm("Are you sure you want to cancel?");
	if(cancelnow)
		{
		window.location="commercial_invoice.php";
		return true;	
		}
	}
////////////////////////////////////////////////////////////////////////////////////
function lookCustomer()/////////////////////////////////////////////////////////////
	{
	var numericExpression = /^[0-9]+$/;
	var ciNumber = document.getElementById('ciNumber').value;
	var ciStatus = document.getElementById('ciStatus').value;
	var ciDate = document.getElementById('ciDate').value;
	var strfNumber=document.getElementById('strfNumber').value;
	var strfDate = document.getElementById('strfDate').value;
	var custCode = document.getElementById('custCode').value;
	var locationCode = document.getElementById('locationCode').value;
	var pricingToUse = document.getElementById('pricingToUse').value;
	var pricingSpecial = document.getElementById('pricingSpecial').value;
	var remarks = document.getElementById('remarks').value;
	if(!custCode.match(numericExpression))
		{
		alert("Customer Code: Numbers only");
		document.getElementById('custCode').focus();
		return false;
		}
	else
		{
		window.location = "ci_transaction.php?do=newRecord&while=findCustomer&ciNumber="+ciNumber+"&ciStatus="+ciStatus+"&ciDate="+ciDate+"&strfNumber="+strfNumber+"&strfDate="+strfDate+"&custCode="+custCode+"&locationCode="+locationCode+"&pricingToUse="+pricingToUse+"&pricingSpecial="+pricingSpecial+"&remarks="+remarks;
		return true;
		}
	}
////////////////////////////////////////////////////////////////////////////////////
function lookLocation()/////////////////////////////////////////////////////////////
	{
	var numericExpression = /^[0-9]+$/;
	var ciNumber = document.getElementById('ciNumber').value;
	var ciStatus = document.getElementById('ciStatus').value;
	var ciDate = document.getElementById('ciDate').value;
	var strfNumber=document.getElementById('strfNumber').value;
	var strfDate = document.getElementById('strfDate').value;
	var custCode = document.getElementById('custCode').value;
	var locationCode = document.getElementById('locationCode').value;
	var pricingToUse = document.getElementById('pricingToUse').value;
	var pricingSpecial = document.getElementById('pricingSpecial').value;
	var remarks = document.getElementById('remarks').value;
	if(!locationCode.match(numericExpression))
		{
		alert("location Code: Numbers only");
		document.getElementById('locationCode').focus();
		return false;
		}
	else
		{
		window.location = "ci_transaction.php?do=newRecord&while=findLocation&ciNumber="+ciNumber+"&ciStatus="+ciStatus+"&ciDate="+ciDate+"&strfNumber="+strfNumber+"&strfDate="+strfDate+"&custCode="+custCode+"&locationCode="+locationCode+"&pricingToUse="+pricingToUse+"&pricingSpecial="+pricingSpecial+"&remarks="+remarks;
		return true;
		}
	}
////////////////////////////////////////////////////////////////////////////////////
function pricingToUseNULL()/////////////////////////////////////////////////////////
	{
	var numericExpression = /^[0-9]+$/;
	var ciNumber = document.getElementById('ciNumber').value;
	var ciStatus = document.getElementById('ciStatus').value;
	var ciDate = document.getElementById('ciDate').value;
	var strfNumber=document.getElementById('strfNumber').value;
	var strfDate = document.getElementById('strfDate').value;
	var custCode = document.getElementById('custCode').value;
	var locationCode = document.getElementById('locationCode').value;
	var pricingToUse = document.getElementById('pricingToUse').value;
	var pricingSpecial = document.getElementById('pricingSpecial').value;
	var remarks = document.getElementById('remarks').value;
	var prodIte = document.getElementById('prodIte').value;
	document.getElementById('pricingSpecial').value="";
	document.getElementById('pricingSpecial').readOnly=true;
	window.location = "commercial_invoice.php?do=newRecord&ciNumber="+ciNumber+"&ciStatus="+ciStatus+"&ciDate="+ciDate+"&strfNumber="+strfNumber+"&strfDate="+strfDate+"&custCode="+custCode+"&locationCode="+locationCode+"&pricingToUse="+pricingToUse+"&pricingSpecial="+pricingSpecial+"&remarks="+remarks+"&prodIte="+prodIte;
	}
////////////////////////////////////////////////////////////////////////////////////
function pricingToUse()/////////////////////////////////////////////////////////////
	{
	var pricingToUse = document.getElementById('pricingToUse').value;
	var pricingSpecial = document.getElementById('pricingSpecial').value;
	document.getElementById('pricingSpecial').readOnly=false;
	document.getElementById('pricingSpecial').focus();
	}
////////////////////////////////////////////////////////////////////////////////////
function pricingCostPlus()//////////////////////////////////////////////////////////
	{
	var numericExpression = /^[0-9]+$/;
	var ciNumber = document.getElementById('ciNumber').value;
	var ciStatus = document.getElementById('ciStatus').value;
	var ciDate = document.getElementById('ciDate').value;
	var strfNumber=document.getElementById('strfNumber').value;
	var strfDate = document.getElementById('strfDate').value;
	var custCode = document.getElementById('custCode').value;
	var locationCode = document.getElementById('locationCode').value;
	var pricingToUse = document.getElementById('pricingToUse').value;
	var pricingSpecial = document.getElementById('pricingSpecial').value;
	var remarks = document.getElementById('remarks').value;
	var prodIte = document.getElementById('prodIte').value;
	document.getElementById('pricingSpecial').value="";
	document.getElementById('pricingSpecial').readOnly=true;
	window.location = "commercial_invoice.php?do=newRecord&ciNumber="+ciNumber+"&ciStatus="+ciStatus+"&ciDate="+ciDate+"&strfNumber="+strfNumber+"&strfDate="+strfDate+"&custCode="+custCode+"&locationCode="+locationCode+"&pricingToUse="+pricingToUse+"&pricingSpecial="+pricingSpecial+"&remarks="+remarks+"&prodIte="+prodIte;
	}
////////////////////////////////////////////////////////////////////////////////////
function checkHeader()//////////////////////////////////////////////////////////////
	{
	var numericExpression = /^[0-9]+$/;
	var ciNumber = document.getElementById('ciNumber').value;
	var ciStatus = document.getElementById('ciStatus').value;
	var ciDate = document.getElementById('ciDate').value;
	var strfNumber=document.getElementById('strfNumber').value;
	var strfDate = document.getElementById('strfDate').value;
	var custCode = document.getElementById('custCode').value;
	var locationCode = document.getElementById('locationCode').value;
	var pricingToUse = document.getElementById('pricingToUse').value;
	var pricingSpecial = document.getElementById('pricingSpecial').value;
	var remarks = document.getElementById('remarks').value;
	if(strfNumber=="")
		{
		alert("STRF Number: Required");
		document.getElementById('strfNumber').focus();
		}
	else if(!strfNumber.match(numericExpression))
		{
		alert("STRF Number: Numbers Only");
		document.getElementById('strfNumber').focus();
		}
	else if(custCode=="")
		{
		alert("Customer Code: Required");
		document.getElementById('custCode').focus();
		}
	else if(!custCode.match(numericExpression))
		{
		alert("Customer Code: Numbers Only");
		document.getElementById('custCode').focus();
		}
	else if(locationCode=="")
		{
		alert("Location Code: Required");
		document.getElementById('locationCode').focus();
		}
	else if(!locationCode.match(numericExpression))
		{
		alert("Location Code: Numbers Only");
		document.getElementById('locationCode').focus();
		}
	else if(pricingToUse=="P"&&pricingSpecial=="")
		{
		var numericExpression = /^[0-9]+$/;
		var custCode = document.getElementById('custCode').value;
		var pricingSpecial = document.getElementById('pricingSpecial').value;
		if(!pricingSpecial.match(numericExpression))
			{
			alert("Cost Plus Percentage: Numbers Only (1-100)");
			document.getElementById('pricingSpecial').focus();
			}
		}
	else if(remarks=="")
		{
		alert("Remarks: Required");
		document.getElementById('remarks').focus();
		}
	}
////////////////////////////////////////////////////////////////////////////////////
function lookProduct()//////////////////////////////////////////////////////////////
	{
	var numericExpression = /^[0-9]+$/;
	var ciNumber = document.getElementById('ciNumber').value;
	var ciStatus = document.getElementById('ciStatus').value;
	var ciDate = document.getElementById('ciDate').value;
	var strfNumber=document.getElementById('strfNumber').value;
	var strfDate = document.getElementById('strfDate').value;
	var custCode = document.getElementById('custCode').value;
	var locationCode = document.getElementById('locationCode').value;
	var pricingToUse = document.getElementById('pricingToUse').value;
	var pricingSpecial = document.getElementById('pricingSpecial').value;
	var remarks = document.getElementById('remarks').value;
	var prodIte = document.getElementById('prodIte').value;
	if(!prodIte.match(numericExpression))
		{
		alert("Item Code: Numbers only");
		document.getElementById('prodIte').focus();
		return false;
		}
	else
		{
		window.location = "ci_transaction.php?do=newRecord&while=findIte&ciNumber="+ciNumber+"&ciStatus="+ciStatus+"&ciDate="+ciDate+"&strfNumber="+strfNumber+"&strfDate="+strfDate+"&custCode="+custCode+"&locationCode="+locationCode+"&pricingToUse="+pricingToUse+"&pricingSpecial="+pricingSpecial+"&remarks="+remarks+"&prodIte="+prodIte;
		return true;
		}
	}
////////////////////////////////////////////////////////////////////////////////////
function lookNewProduct()//////////////////////////////////////////////////////////////
	{
	var numericExpression = /^[0-9]+$/;
	var ciNumber = document.getElementById('ciNumber').value;
	var ciStatus = document.getElementById('ciStatus').value;
	var ciDate = document.getElementById('ciDate').value;
	var strfNumber=document.getElementById('strfNumber').value;
	var strfDate = document.getElementById('strfDate').value;
	var custCode = document.getElementById('custCode').value;
	var locationCode = document.getElementById('locationCode').value;
	var pricingToUse = document.getElementById('pricingToUse').value;
	var pricingSpecial = document.getElementById('pricingSpecial').value;
	var remarks = document.getElementById('remarks').value;
	var prodIte = document.getElementById('prodIte').value;
	if(!prodIte.match(numericExpression))
		{
		alert("Item Code: Numbers only");
		document.getElementById('prodIte').focus();
		return false;
		}
	else
		{
		window.location = "ci_transaction.php?do=newRecord&while=newInvoice&ciNumber="+ciNumber+"&ciStatus="+ciStatus+"&ciDate="+ciDate+"&strfNumber="+strfNumber+"&strfDate="+strfDate+"&custCode="+custCode+"&locationCode="+locationCode+"&pricingToUse="+pricingToUse+"&pricingSpecial="+pricingSpecial+"&remarks="+remarks+"&prodIte="+prodIte;
		return true;
		}
	}
////////////////////////////////////////////////////////////////////////////////////
function saveRecordNow()////////////////////////////////////////////////////////////
	{
	var numericExpression = /^[0-9]+$/;
	var ciNumber = document.getElementById('ciNumber').value;
	var ciStatus = document.getElementById('ciStatus').value;
	var ciDate = document.getElementById('ciDate').value;
	var strfNumber=document.getElementById('strfNumber').value;
	var strfDate = document.getElementById('strfDate').value;
	var custCode = document.getElementById('custCode').value;
	var locationCode = document.getElementById('locationCode').value;
	var pricingToUse = document.getElementById('pricingToUse').value;
	var pricingSpecial = document.getElementById('pricingSpecial').value;
	var remarks = document.getElementById('remarks').value;
	var prodIte = document.getElementById('prodIte').value;
	var prodQty1 = document.getElementById('prodQty1').value;
	var prodQty2 = document.getElementById('prodQty2').value;
	var prodQty3 = document.getElementById('prodQty3').value;
	var prodQty4 = document.getElementById('prodQty4').value;
	var prodDisc = document.getElementById('prodDisc').value;
	var prodUniPri = document.getElementById('prodUniPri').value;
	if(!strfNumber.match(numericExpression))
		{
		alert("STRF Number: Numbers only");
		document.getElementById('strfNumber').focus();
		return false;
		}
	else if(strfNumber=="")
		{
		alert("STRF Number: Required");
		document.getElementById('strfNumber').focus();
		return false;
		}
	/* else if(ciDate>strfDate)
		{
		alert("STRF Date must not earlier than Current Date");
		document.getElementById('strfDate').focus();
		return false;
		} */
	else if(custCode=="")
		{
		alert("Customer Code: Required");
		document.getElementById('custCode').focus();
		return false;
		}
	else if(!custCode.match(numericExpression))
		{
		alert("Customer Code: Numbers only");
		document.getElementById('ustCode').focus();
		return false;
		}
	else if(locationCode=="")
		{
		alert("Location Code: Required");
		document.getElementById('locationCode').focus();
		return false;
		}
	else if(pricingToUse=="P"&&pricingSpecial=="")
		{
		alert("Percentage: Required");
		document.getElementById('pricingSpecial').focus();
		return false;
		}
	else if(pricingToUse=="P"&&pricingSpecial>100)
		{
		alert("Percentage: Maximum of 100% only");
		document.getElementById('pricingSpecial').focus();
		return false;
		}
	else if(prodQty1==""&&prodQty2==""&&prodQty3==""&&prodQty4=="")
		{
		alert("Quantity Ordered: At least 1 quantity required");
		document.getElementById('prodQty1').focus();
		return false;
		}
	else if(prodQty1==0&&prodQty2==0&&prodQty3==0&&prodQty4==0)
		{
		alert("Quantity Ordered: At least 1 quantity required");
		document.getElementById('prodQty1').focus();
		return false;
		}
	else if(prodQty1!=""&&!prodQty1.match(numericExpression))
		{
		alert("Quantity Ordered: Numbers Only");
		document.getElementById('prodQty1').focus();
		return false;
		}
	else if(prodQty2!=""&&!prodQty2.match(numericExpression))
		{
		alert("Quantity Ordered: Numbers Only");
		document.getElementById('prodQty2').focus();
		return false;
		}
	else if(prodQty3!=""&&!prodQty3.match(numericExpression))
		{
		alert("Quantity Ordered: Numbers Only");
		document.getElementById('prodQty3').focus();
		return false;
		}
	else if(prodQty4!=""&&!prodQty4.match(numericExpression))
		{
		alert("Quantity Ordered: Numbers Only");
		document.getElementById('prodQty4').focus();
		return false;
		}
	else if(prodDisc!=""&&!prodDisc.match(numericExpression))
		{
		alert("Discount: Numbers Only");
		document.getElementById('prodDisc').focus();
		return false;
		}
	else if(prodDisc>100)
		{
		alert("Discount: Maximum of 100% only");
		document.getElementById('prodDisc').focus();
		return false;
		}
	else if(pricingToUse=="S"&&prodUniPri=="")
		{
		alert("Unit Price: Required");
		document.getElementById('prodUniPri').focus();
		return false;
		}
	else
		{
		window.location = "ci_transaction.php?do=newRecord&while=saveRecordNow&ciNumber="+ciNumber+"&ciStatus="+ciStatus+"&ciDate="+ciDate+"&strfNumber="+strfNumber+"&strfDate="+strfDate+"&custCode="+custCode+"&locationCode="+locationCode+"&pricingToUse="+pricingToUse+"&pricingSpecial="+pricingSpecial+"&remarks="+remarks+"&prodIte="+prodIte+"&prodQty1="+prodQty1+"&prodQty2="+prodQty2+"&prodQty3="+prodQty3+"&prodQty4="+prodQty4+"&prodDisc="+prodDisc+"&prodUniPri="+prodUniPri;
		}
	}
////////////////////////////////////////////////////////////////////////////////////
function saveNewRecordNow()/////////////////////////////////////////////////////////
	{
	var numericExpression = /^[0-9]+$/;
	var ciNumber = document.getElementById('ciNumber').value;
	var ciStatus = document.getElementById('ciStatus').value;
	var ciDate = document.getElementById('ciDate').value;
	var strfNumber=document.getElementById('strfNumber').value;
	var strfDate = document.getElementById('strfDate').value;
	var custCode = document.getElementById('custCode').value;
	var locationCode = document.getElementById('locationCode').value;
	var pricingToUse = document.getElementById('pricingToUse').value;
	var pricingSpecial = document.getElementById('pricingSpecial').value;
	var remarks = document.getElementById('remarks').value;
	var prodIte = document.getElementById('prodIte').value;
	var prodQty1 = document.getElementById('prodQty1').value;
	var prodQty2 = document.getElementById('prodQty2').value;
	var prodQty3 = document.getElementById('prodQty3').value;
	var prodQty4 = document.getElementById('prodQty4').value;
	var prodDisc = document.getElementById('prodDisc').value;
	var prodUniPri = document.getElementById('prodUniPri').value;
	if(!strfNumber.match(numericExpression))
		{
		alert("STRF Number: Numbers only");
		document.getElementById('strfNumber').focus();
		return false;
		}
	else if(strfNumber=="")
		{
		alert("STRF Number: Required");
		document.getElementById('strfNumber').focus();
		return false;
		}
	/* else if(ciDate>strfDate)
		{
		alert("STRF Date must not earlier than Current Date");
		document.getElementById('strfDate').focus();
		return false;
		} */
	else if(custCode=="")
		{
		alert("Customer Code: Required");
		document.getElementById('custCode').focus();
		return false;
		}
	else if(!custCode.match(numericExpression))
		{
		alert("Customer Code: Numbers only");
		document.getElementById('ustCode').focus();
		return false;
		}
	else if(locationCode=="")
		{
		alert("Location Code: Required");
		document.getElementById('locationCode').focus();
		return false;
		}
	else if(pricingToUse=="P"&&pricingSpecial=="")
		{
		alert("Percentage: Required");
		document.getElementById('pricingSpecial').focus();
		return false;
		}
	else if(pricingToUse=="P"&&pricingSpecial>100)
		{
		alert("Percentage: Maximum of 100% only");
		document.getElementById('pricingSpecial').focus();
		return false;
		}
	else if(prodQty1==""&&prodQty2==""&&prodQty3==""&&prodQty4=="")
		{
		alert("Quantity Ordered: At least 1 quantity required");
		document.getElementById('prodQty1').focus();
		return false;
		}
	else if(prodQty1==0&&prodQty2==0&&prodQty3==0&&prodQty4==0)
		{
		alert("Quantity Ordered: At least 1 quantity required");
		document.getElementById('prodQty1').focus();
		return false;
		}
	else if(prodQty1!=""&&!prodQty1.match(numericExpression))
		{
		alert("Quantity Ordered: Numbers Only");
		document.getElementById('prodQty1').focus();
		return false;
		}
	else if(prodQty2!=""&&!prodQty2.match(numericExpression))
		{
		alert("Quantity Ordered: Numbers Only");
		document.getElementById('prodQty2').focus();
		return false;
		}
	else if(prodQty3!=""&&!prodQty3.match(numericExpression))
		{
		alert("Quantity Ordered: Numbers Only");
		document.getElementById('prodQty3').focus();
		return false;
		}
	else if(prodQty4!=""&&!prodQty4.match(numericExpression))
		{
		alert("Quantity Ordered: Numbers Only");
		document.getElementById('prodQty4').focus();
		return false;
		}
	else if(prodDisc!=""&&!prodDisc.match(numericExpression))
		{
		alert("Discount: Numbers Only");
		document.getElementById('prodDisc').focus();
		return false;
		}
	else if(prodDisc>100)
		{
		alert("Discount: Maximum of 100% only");
		document.getElementById('prodDisc').focus();
		return false;
		}
	else if(pricingToUse=="S"&&prodUniPri=="")
		{
		alert("Unit Price: Required");
		document.getElementById('prodUniPri').focus();
		return false;
		}
	else
		{
		window.location = "ci_transaction.php?do=newRecord&while=saveNewRecordNow&ciNumber="+ciNumber+"&ciStatus="+ciStatus+"&ciDate="+ciDate+"&strfNumber="+strfNumber+"&strfDate="+strfDate+"&custCode="+custCode+"&locationCode="+locationCode+"&pricingToUse="+pricingToUse+"&pricingSpecial="+pricingSpecial+"&remarks="+remarks+"&prodIte="+prodIte+"&prodQty1="+prodQty1+"&prodQty2="+prodQty2+"&prodQty3="+prodQty3+"&prodQty4="+prodQty4+"&prodDisc="+prodDisc+"&prodUniPri="+prodUniPri;
		}
	}
////////////////////////////////////////////////////////////////////////////////////
function deleteRecordNow()//////////////////////////////////////////////////////////
	{
	var numericExpression = /^[0-9]+$/;
	var ciNumber = document.getElementById('ciNumber').value;
	var ciStatus = document.getElementById('ciStatus').value;
	var ciDate = document.getElementById('ciDate').value;
	var strfNumber=document.getElementById('strfNumber').value;
	var strfDate = document.getElementById('strfDate').value;
	var custCode = document.getElementById('custCode').value;
	var locationCode = document.getElementById('locationCode').value;
	var pricingToUse = document.getElementById('pricingToUse').value;
	var pricingSpecial = document.getElementById('pricingSpecial').value;
	var remarks = document.getElementById('remarks').value;
	var prodIte = document.getElementById('prodIte').value;
	var prodQty1 = document.getElementById('prodQty1').value;
	var prodQty2 = document.getElementById('prodQty2').value;
	var prodQty3 = document.getElementById('prodQty3').value;
	var prodQty4 = document.getElementById('prodQty4').value;
	var prodDisc = document.getElementById('prodDisc').value;
	var prodUniPri = document.getElementById('prodUniPri').value;
	var deleteConfirm = confirm("This Commercial Invoice Record will be deleted. Are you sure?");
	if(deleteConfirm)
		{
		window.location = "ci_transaction.php?do=newRecord&while=deleteRecordNow&ciNumber="+ciNumber+"&ciStatus="+ciStatus+"&ciDate="+ciDate+"&strfNumber="+strfNumber+"&strfDate="+strfDate+"&custCode="+custCode+"&locationCode="+locationCode+"&pricingToUse="+pricingToUse+"&pricingSpecial="+pricingSpecial+"&remarks="+remarks+"&prodIte="+prodIte+"&prodQty1="+prodQty1+"&prodQty2="+prodQty2+"&prodQty3="+prodQty3+"&prodQty4="+prodQty4+"&prodDisc="+prodDisc+"&prodUniPri="+prodUniPri;
		}
	}
</script>
