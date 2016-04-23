<?php
date_default_timezone_set('Asia/Singapore');
////////////////////////////////////////////////////////////////////////////////////-----
function dateTodayMonth()///////////////////////////////////////////////////////////
	{
	$do = $_GET['do']; $while=$_GET['while']; $signal=$_GET['signal'];
	$trfNo = $_GET['trfNo']; $docDate = $_GET['docDate']; 
	$trfLoc1 = $_GET['trfLoc1']; $trfLoc2 = $_GET['trfLoc2']; $trfClass = $_GET['trfClass'];
	$responsible = $_GET['responsible']; $trfRemarks = $_GET['trfRemarks'];
	$hashIte = $_GET['hashIte']; $hashQty = $_GET['hashQty'];
	$prodIte=$_GET['prodIte']; $prodQty=$_GET['prodQty']; $prodPrice=$_GET['prodPrice']; $prodCost=$_GET['prodCost'];
	
	$compcode='1'; $monthNow = date("n");
	$conserver = mssql_connect("192.168.200.170","sa","sa") or die("Cannot Connect to server");
	$selectdb = mssql_select_db("DFS") or die("Cannot select database");
	$month = mssql_query("SELECT pdDesc, pdCode
	FROM tblPeriod
	WHERE pdStat='O' 
	AND pdCode='$monthNow'
	AND compCode='$compcode'");
	
	if($do=="receiveRecord"||$while=="displayItems")
		{ echo "<select name='thisMonth' class='readonly_textbox_month' id='thisMonth'>"; }
	while ($monthRow = mssql_fetch_assoc($month))
		{
		echo "<option value='".$monthRow['pdCode']."'>".strtoupper($monthRow['pdCode'])." - ".strtoupper($monthRow['pdDesc'])."</option>";
		}
	echo "</select>";
	mssql_close();
	}
////////////////////////////////////////////////////////////////////////////////////-----
function dateTodayYear()///////////////////////////////////////////////////////////
	{
	$do = $_GET['do']; $while=$_GET['while']; $signal=$_GET['signal'];
	$trfNo = $_GET['trfNo']; $docDate = $_GET['docDate']; 
	$trfLoc1 = $_GET['trfLoc1']; $trfLoc2 = $_GET['trfLoc2']; $trfClass = $_GET['trfClass'];
	$responsible = $_GET['responsible']; $trfRemarks = $_GET['trfRemarks'];
	$hashIte = $_GET['hashIte']; $hashQty = $_GET['hashQty'];
	$prodIte=$_GET['prodIte']; $prodQty=$_GET['prodQty']; $prodPrice=$_GET['prodPrice']; $prodCost=$_GET['prodCost'];
	
	$compcode='1'; $yearNow = date("Y"); $monthNow = date("n");
	$conserver = mssql_connect("192.168.200.170","sa","sa") or die("Cannot Connect to server");
	$selectdb = mssql_select_db("DFS") or die("Cannot select database");
	$year = mssql_query("SELECT pdDesc, pdCode, pdYear
	FROM tblPeriod
	WHERE pdStat='O' 
	AND pdYear='$yearNow'
	AND pdCode='$monthNow'
	AND compCode='$compcode'");
	
	if($do=="receiveRecord"||$while=="displayItems")
		{ echo "<select name='thisYear' class='readonly_textbox_month' id='thisYear'>"; }
	while ($yearRow = mssql_fetch_assoc($year))
		{
		echo "<option value='".$yearRow['pdYear']."'>".strtoupper($yearRow['pdYear'])."</option>";
		}
	echo "</select>";
	mssql_close();
	}
////////////////////////////////////////////////////////////////////////////////////-----
function dateOneTodayStart()////////////////////////////////////////////////////////
	{
	//$dateNow = date("n-j-Y", time() + (1 * 24 * 60 * 60)) ;
	$dateNow = date("n-j-Y"); $ciDate = $_GET['ciDate']; 
	$do=$_GET['do']; $while=$_GET['while']; 
	if($do=="release"&&$while=="displayItems")
		{ echo "value='$ciDate' disabled='disabled'"; }
	else
		{ echo "value='$ciDate' disabled='disabled'"; }
	}
////////////////////////////////////////////////////////////////////////////////////-----
function dateTodayStart()////////////////////////////////////////////////////////
	{
	//$dateNow = date("n-j-Y", time() + (1 * 24 * 60 * 60)) ;
	$dateNow = date("n-j-Y"); $ciDate = $_GET['ciDate']; 
	$do=$_GET['do']; $while=$_GET['while']; 
	if($do=="release"&&$while=="displayItems")
		{ echo "value='$dateNow' disabled='disabled'"; }
	else
		{ echo "disabled='disabled'"; }
	}
////////////////////////////////////////////////////////////////////////////////////-----
function calendar()/////////////////////////////////////////////////////////////////
	{
	$do=$_GET['do']; $while=$_GET['while'];
	if($do=="newRecord"||$do=="updateRecord")
		{ echo  "onfocus='this.select();lcs(this)' onclick='event.cancelBubble=true;this.select();lcs(this)'"; }
	}
////////////////////////////////////////////////////////////////////////////////////-----
function signal()///////////////////////////////////////////////////////////////////
	{
	$do = $_GET['do'];
	echo "value='$do' readonly='readonly'";
	}
////////////////////////////////////////////////////////////////////////////////////-----
function prdFrcTag()///////////////////////////////////////////////////////////////////
	{
	$do = $_GET['do']; $prdFrcTag = $_GET['prdFrcTag'];
	echo "value='$prdFrcTag' readonly='readonly'";
	}
////////////////////////////////////////////////////////////////////////////////////-----
function opsName()//////////////////////////////////////////////////////////////////
	{
	$do=$_GET['do']; $while=$_GET['while'];
	$username = "1234";
	if($do==""&&$while=="")
		{	echo "disabled='disabled'";	}
	else if($do=="release"&&$while=="displayItems")
		{	echo "value='$username' disabled='disabled'";	}
	}
////////////////////////////////////////////////////////////////////////////////////-----
function releaseButton()/////////////////////////////////////////////////////////////
	{
	$do = $_GET['do']; $while=$_GET['while'];
	if($do=="")
		{	echo "<input type='submit' class='queryButton' name='saveRecordbutt' id='saveRecordbutt' value='Release Now' title='Release Now' disabled='disabled'/>";	}
	else if($do=="release"&&$while=="displayItems")
		{
		echo "
		<input type='submit' class='queryButton' name='saveRecordbutt' id='saveRecordbutt' value='Release Now' title='Release Now'/>";
		
		}
	}
////////////////////////////////////////////////////////////////////////////////////-----
function cancelButton()/////////////////////////////////////////////////////////////
	{
	$do = $_GET['do']; $while=$_GET['while'];
	if($do=="")
		{	echo "<input type='button' class='queryButton' name='cancelButt' id='cancelButt' value='Cancel' title='Cancel' 
		disabled='disabled'/>"; }
	else if($do=="newRecord")
		{	echo "<input type='button' class='queryButton' name='cancelButt' id='cancelButt' value='Cancel' title='Cancel' onclick='canc()'/>"; }
	else if($do=="updateRecord")
		{	echo "<input type='button' class='queryButton' name='cancelButt' id='cancelButt' value='Cancel' title='Cancel' onclick='var confirmCancel = confirm(\"Are you sure you want to cancel updating this Record?\"); if(confirmCancel) { window.location=\"adjustment.php\"}'/>"; }
	else if($do=="release")
		{	echo "<input type='button' class='queryButton' name='cancelButt' id='cancelButt' value='Cancel' title='Cancel' onclick='var confirmCancel = confirm(\"Are you sure you want to cancel Releasing this Record?\"); if(confirmCancel) { window.location=\"ci_release.php\"}'/>"; }
	}
///////////////////////////////////////////////////////////////////////////////////
function ciNumber()////////////////////////////////////////////////////////////////
	{
	$do = $_GET['do']; $while=$_GET['while']; $signal=$_GET['signal'];
	$ciNo = $_GET['ciNo'];
	
	$compcode = '1';
	$conserver = mssql_connect("192.168.200.170","sa","sa") or die("Cannot Connect to server");
	$selectdb = mssql_select_db("DFS") or die("Cannot select database");
	$ciHeader = mssql_query("SELECT * 
	FROM tblCiHeader
	WHERE ciStat='O' 
	AND compCode='$compcode'
	ORDER BY ciRlsdDte ASC");
	
	echo "<select name='ciNo' class='textbox' tabindex='1' id='ciNo'>";
	echo "<option value=\"\"></option>";
	while ($ciHeaderRow = mssql_fetch_assoc($ciHeader))
		{
		echo "<option value='".$ciHeaderRow['ciNumber']."'"; 
		if(($ciHeaderRow['ciNumber'])==($_GET['ciNo'])) 
			{ 
			echo "selected='selected'"; 
			} 
		echo "' onclick='displayItems()'>".strtoupper($ciHeaderRow['ciNumber'])."</option>";
		}
	echo "</select>";
	mssql_close();
	}
////////////////////////////////////////////////////////////////////////////////////-----
function whiles()///////////////////////////////////////////////////////////////////
	{
	$while = $_GET['while'];
	echo "value='$while' readonly='readonly'";
	}

////////////////////////////////////////////////////////////////////////////////////-----
function includeItem()/////////////////////////////////////////////////////////////
	{
	$do = $_GET['do']; $while=$_GET['while']; $signal=$_GET['signal'];
	$adjNo = $_GET['adjNo']; $adjType = $_GET['adjType']; $docDate = $_GET['docDate']; 
	$whseStore = $_GET['whseStore']; $adjReason = $_GET['adjReason']; $adjRemarks = $_GET['adjRemarks'];
	$responsible = $_GET['responsible']; $adjTag = $_GET['adjTag'];
	$hashIte = $_GET['hashIte']; $hashQty = $_GET['hashQty'];
	$prodIte=$_GET['prodIte']; $prodQty=$_GET['prodQty']; $prodPrice=$_GET['prodPrice']; $prodCost=$_GET['prodCost'];
	
	if($do=="newRecord"||$do=="updateRecord")
		{	echo "
			<tr bgcolor='#E9F8E8'>
			<td><center>--</center></td>
			<td><center>
			<input name='prodIte' type='text' class='pricePriceInput' id='prodIte' tabindex='1' maxlength='6'  value='$prodIte'"; includeIte(); 
			 echo ">
				</center></td>
			<td><input name='prodDesc' type='text' class='pricePriceDesc' id='prodDesc'"; includeDesc(); 
				echo "readonly='readonly' />
				</td>
			<td><center>
				<input name='prodUm' type='text' class='pricePriceUm' id='prodUm' maxlength='10' "; includeUm(); 
				echo "readonly='readonly' />
				</center></th>
			<td><center>
				<input name='prodQty' type='text' class='pricePriceInput' id='prodQty' maxlength='8'"; includeQty(); 
				echo">
				</center></td>
			<td><center>
				<input name='prodCost' type='text' id='prodCost' maxlength='10'"; includeCost(); 
				echo ">
				</center></td>
			<td><center>
				<input name='prodPrice' type='text' class='pricePricePrice' id='prodPrice' maxlength='10' "; includePrice(); 
				echo "readonly='readonly'>
				</center></td>
			<td><center><center/></td>
			<td><center><center/></td>";
		}
	}
////////////////////////////////////////////////////////////////////////////////////-----
function includeTypeCode()//////////////////////////////////////////////////////////
		{
	$do = $_GET['do']; $while=$_GET['while']; $signal=$_GET['signal'];
	$adjNo = $_GET['adjNo']; $adjType = $_GET['adjType']; $docDate = $_GET['docDate']; 
	$whseStore = $_GET['whseStore']; $adjReason = $_GET['adjReason']; $adjRemarks = $_GET['adjRemarks'];
	$responsible = $_GET['responsible']; $adjTag = $_GET['adjTag'];
	$hashIte = $_GET['hashIte']; $hashQty = $_GET['hashQty'];
	$prodIte=$_GET['prodIte']; $prodQty=$_GET['prodQty']; $prodPrice=$_GET['prodPrice']; $prodCost=$_GET['prodCost'];
	
	$conserver = mssql_connect("192.168.200.170","sa","sa") or die("Cannot Connect to server");
	$selectdb = mssql_select_db("DFS") or die("Cannot select database");
	$adjustmentType = mssql_query("SELECT * 
	FROM tblAdjustmentType
	WHERE adjTypeStat='A' 
	ORDER BY adjTypeCode ASC");
	
	if($do==""||$while=="")
		{ echo "<select name='adjType' class='textbox' tabindex='1' id='adjType'>"; }
	else if($do=="newRecord"&&$while=="findIte")
		{ echo "<select name='adjType' class='textbox' tabindex='1' id='adjType'>"; }
	else if(($do=="newRecord"||$do=="updateRecord")&&($while=="newIte"||$while=="editItem"))
		{ echo "<select name='adjType' class='textbox' tabindex='1' id='adjType' disabled='disabled'>"; }
	else if($do=="receiveRecord"&&$while=="displayItems")
		{ echo "<select name='adjType' class='textbox' tabindex='1' id='adjType' disabled='disabled'>"; }

	echo "<option value=\"\"></option>";
	while ($adjustmentTypeRow = mssql_fetch_assoc($adjustmentType))
		{
		echo "<option value='".$adjustmentTypeRow['adjTypeCode']."' onclick='typeCodeToUse()'"; 
		if(($adjustmentTypeRow['adjTypeCode'])==($_GET['adjType'])) 
			{ 
			echo "selected='selected'"; 
			} 
				
		echo "'>".strtoupper($adjustmentTypeRow['adjTypeCode'])." - ".strtoupper($adjustmentTypeRow['adjTypeDesc'])."</option>";
		}
	echo "</select>";
	mssql_close();
	}
////////////////////////////////////////////////////////////////////////////////////-----
function includeLocation()//////////////////////////////////////////////////////////
		{
	$do = $_GET['do']; $while=$_GET['while']; $signal=$_GET['signal']; $ciNo = $_GET['ciNo'];
	
	$conserver = mssql_connect("192.168.200.170","sa","sa") or die("Cannot Connect to server");
	$selectdb = mssql_select_db("DFS") or die("Cannot select database");
	$location = mssql_query("SELECT * 
	FROM tblLocation
	WHERE locStat='A' 
	ORDER BY locName ASC");
	
	if($do==""||$while=="")
		{ echo "<select name='location' class='textbox' tabindex='1' id='location' disabled='disabled'>"; }
	else if($do=="release"&&$while=="displayItems")
		{ echo "<select name='location' class='textbox' tabindex='1' id='location' disabled='disabled'>"; }

	echo "<option value=\"\"></option>";
	while ($locationRow = mssql_fetch_assoc($location))
		{
		echo "<option value='".$locationRow['locCode']."'"; 
		if(($locationRow['locCode'])==($_GET['location'])) 
			{ 
			echo "selected='selected'"; 
			} 
				
		echo "'>".strtoupper($locationRow['locCode'])." - ".strtoupper($locationRow['locName'])."</option>";
		}
	echo "</select>";
	mssql_close();
	}
////////////////////////////////////////////////////////////////////////////////////-----
function includeDesc()//////////////////////////////////////////////////////////////
	{
	$do = $_GET['do']; $while=$_GET['while']; $signal=$_GET['signal'];
	$adjNo = $_GET['adjNo']; $adjType = $_GET['adjType']; $docDate = $_GET['docDate']; 
	$whseStore = $_GET['whseStore']; $adjReason = $_GET['adjReason']; $adjRemarks = $_GET['adjRemarks'];
	$responsible = $_GET['responsible']; $adjTag = $_GET['adjTag'];
	$hashIte = $_GET['hashIte']; $hashQty = $_GET['hashQty'];
	$prodIte=$_GET['prodIte']; $prodQty=$_GET['prodQty']; $prodPrice=$_GET['prodPrice']; $prodCost=$_GET['prodCost'];
	if(($do=="newRecord"||$do=="updateRecord")&&($prodIte!=""||$while=="editItem"))
		{
		$conserver = mssql_connect("192.168.200.170","sa","sa") or die("Cannot Connect to server");
		$selectdb = mssql_select_db("DFS") or die("Cannot select database");
		$findDesc = mssql_query("SELECT prdDesc
		FROM tblProdMast 
		WHERE prdNumber='$prodIte'");
		while($findDescRow=mssql_fetch_assoc($findDesc))
			{	$prdDesc = strtoupper($findDescRow['prdDesc']); }
		echo "value=\"".$prdDesc."\"";
		mssql_close();
		}
	}
////////////////////////////////////////////////////////////////////////////////////-----
function includeUm()////////////////////////////////////////////////////////////////
	{
	$do = $_GET['do']; $while=$_GET['while']; $signal=$_GET['signal'];
	$adjNo = $_GET['adjNo']; $adjType = $_GET['adjType']; $docDate = $_GET['docDate']; 
	$whseStore = $_GET['whseStore']; $adjReason = $_GET['adjReason']; $adjRemarks = $_GET['adjRemarks'];
	$responsible = $_GET['responsible']; $adjTag = $_GET['adjTag'];
	$hashIte = $_GET['hashIte']; $hashQty = $_GET['hashQty'];
	$prodIte=$_GET['prodIte']; $prodQty=$_GET['prodQty']; $prodPrice=$_GET['prodPrice']; $prodCost=$_GET['prodCost'];
	if(($do=="newRecord"||$do=="updateRecord")&&($prodIte!=""||$while=="editItem"))
		{
		$conserver = mssql_connect("192.168.200.170","sa","sa") or die("Cannot Connect to server");
		$selectdb = mssql_select_db("DFS") or die("Cannot select database");
		$findSellUnit = mssql_query("SELECT prdSellUnit
		FROM tblProdMast 
		WHERE prdNumber='$prodIte'");
		while($findSellUnitRow=mssql_fetch_assoc($findSellUnit))
			{	$prdSellUnit = strtoupper($findSellUnitRow['prdSellUnit']); }
		echo "value='".$prdSellUnit."'";
		mssql_close();
		}
	}
////////////////////////////////////////////////////////////////////////////////////
function includeQty()///////////////////////////////////////////////////////////////
	{
	$do = $_GET['do']; $while=$_GET['while']; $signal=$_GET['signal'];
	$adjNo = $_GET['adjNo']; $adjType = $_GET['adjType']; $docDate = $_GET['docDate']; 
	$whseStore = $_GET['whseStore']; $adjReason = $_GET['adjReason']; $adjRemarks = $_GET['adjRemarks'];
	$responsible = $_GET['responsible']; $adjTag = $_GET['adjTag'];
	$hashIte = $_GET['hashIte']; $hashQty = $_GET['hashQty'];
	$prodIte=$_GET['prodIte']; $prodQty=$_GET['prodQty']; $prodPrice=$_GET['prodPrice']; $prodCost=$_GET['prodCost'];
	
	if($do=="newRecord"&&$prodIte!="")
		{
		$conserver = mssql_connect("192.168.200.170","sa","sa") or die("Cannot Connect to server");
		$selectdb = mssql_select_db("DFS") or die("Cannot select database");
		$findFrcTag = mssql_query("SELECT prdFrcTag
		FROM tblProdMast 
		WHERE prdNumber='$prodIte'");
		while($findFrcTagRow=mssql_fetch_assoc($findFrcTag))
			{	$prdFrcTag = $findFrcTagRow['prdFrcTag'];}
		if($prdFrcTag=='Y') { echo "value='0.0000'";}
		else { echo "value='0'";}
		mssql_close();
		}
	else if(($do=="newRecord"||$do=="updateRecord")&&($prodIte!=""||$while=="editItem"))
		{
		echo "value='$prodQty'";
		}
	}
////////////////////////////////////////////////////////////////////////////////////-----
function includeCost()/////////////////////////////////////////////////////////////
	{
	$do = $_GET['do']; $while=$_GET['while']; $signal=$_GET['signal'];
	$adjNo = $_GET['adjNo']; $adjType = $_GET['adjType']; $docDate = $_GET['docDate']; 
	$whseStore = $_GET['whseStore']; $adjReason = $_GET['adjReason']; $adjRemarks = $_GET['adjRemarks'];
	$responsible = $_GET['responsible']; $adjTag = $_GET['adjTag'];
	$hashIte = $_GET['hashIte']; $hashQty = $_GET['hashQty'];
	$prodIte=$_GET['prodIte']; $prodQty=$_GET['prodQty']; $prodPrice=$_GET['prodPrice']; $prodCost=$_GET['prodCost'];
	
	if(($do=="newRecord"||$do=="updateRecord")&&$prodIte!=""||$while=="editItem")
		{
		$conserver = mssql_connect("192.168.200.170","sa","sa") or die("Cannot   Connect to server");
		$selectdb = mssql_select_db("DFS") or die("Cannot select database");		
		
		if(($do=="newRecord"||$do=="updateRecord")&&$adjType!="CA")
			{	
			$findCost = mssql_query("SELECT aveUnitCost
			FROM tblAveCost 
			WHERE prdNumber='$prodIte'");
			while($findCostRow=mssql_fetch_assoc($findCost))
				{	$aveUnitCost = strtoupper($findCostRow['aveUnitCost']); }
			echo "value='".$aveUnitCost."' readonly='readonly' class='pricePricePrice'";	}
		else if(($do=="newRecord"||$do=="updateRecord")&&$adjType=="CA")
			{	
			$findCostDtl = mssql_query("SELECT adjCost
			FROM tblAdjustmentDtl 
			WHERE prdNumber='$prodIte'");
			while($findCostDtlRow=mssql_fetch_assoc($findCostDtl))
				{	$adjCost = strtoupper($findCostDtlRow['adjCost']); }
			echo "value='".$adjCost."' class='pricePriceInput'";	}
		else
			{	echo "value='".number_format(0, 4)."' class='pricePriceInput'";	}
		mssql_close();
		}
	else
		{ echo "class='pricePricePrice'";}
	}
////////////////////////////////////////////////////////////////////////////////////-----
function includePrice()/////////////////////////////////////////////////////////////
	{
	$do = $_GET['do']; $while=$_GET['while']; $signal=$_GET['signal'];
	$adjNo = $_GET['adjNo']; $adjType = $_GET['adjType']; $docDate = $_GET['docDate']; 
	$whseStore = $_GET['whseStore']; $adjReason = $_GET['adjReason']; $adjRemarks = $_GET['adjRemarks'];
	$responsible = $_GET['responsible']; $adjTag = $_GET['adjTag'];
	$hashIte = $_GET['hashIte']; $hashQty = $_GET['hashQty'];
	$prodIte=$_GET['prodIte']; $prodQty=$_GET['prodQty']; $prodPrice=$_GET['prodPrice']; $prodCost=$_GET['prodCost'];
	
	if(($do=="newRecord"||$do=="updateRecord")&&$prodIte!=""||$while=="editItem")
		{
		$conserver = mssql_connect("192.168.200.170","sa","sa") or die("Cannot Connect to server");
		$selectdb = mssql_select_db("DFS") or die("Cannot select database");
		$findPrice = mssql_query("SELECT regUnitPrice
		FROM tblProdPrice 
		WHERE prdNumber='$prodIte'");
		while($findPriceRow=mssql_fetch_assoc($findPrice))
			{	$regUnitPrice = strtoupper($findPriceRow['regUnitPrice']); }
		echo "value='".$regUnitPrice."'";
		mssql_close();
		}
	}
////////////////////////////////////////////////////////////////////////////////////
function includedItems()//////////////////////////////////////////////////////////////
	{
	$do = $_GET['do']; $while=$_GET['while']; $signal=$_GET['signal']; $ciNo = $_GET['ciNo']; 
	
	if($do="release"&&$while="displayItems")
		{
		$compcode='1';	
		$conserver = mssql_connect("192.168.200.170","sa","sa") or die("Cannot Connect to server");
		$selectdb = mssql_select_db("DFS") or die("Cannot select database");
		$findInvoices = mssql_query("SELECT *
		FROM tblCiItemDtl 
		WHERE compcode='$compcode' 
		AND ciNumber='$ciNo'
		AND ciStatus='O'");
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
        <td><center>$prdNumber</center><div align='center'><input name='prodNumber[]' type='hidden' class='pricePricePrice' 
				id='prodNumber[]' value='$prdNumber' readonly='readonly'></div></td>
        <td>$prdDesc</td>
        <td><center>$prdBC</center><div align='center'><input name='prdConv[]' type='hidden' class='pricePricePrice' 
				id='prdConv[]' value='$prdConv' readonly='readonly'></div></td>
        <td><center>$qtyRegPk<div align='center'><input name='qtyRegPk[]' type='hidden' class='pricePricePrice' 
				id='qtyRegPk[]' value='$qtyRegPk' readonly='readonly'></div></center></td>
        <td><center>$qtyFreePk<div align='center'><input name='qtyFreePk[]' type='hidden' class='pricePricePrice' 
				id='qtyFreePk[]' value='$qtyFreePk' readonly='readonly'></div></center></td>
        <td><center>$qtyRegPc<div align='center'><input name='qtyRegPc[]' type='hidden' class='pricePricePrice' 
				id='qtyRegPc[]' value='$qtyRegPc' readonly='readonly'></div></center></td>
        <td><center>$qtyFreePc<div align='center'><input name='qtyFreePc[]' type='hidden' class='pricePricePrice' 
				id='qtyFreePc[]' value='$qtyFreePc' readonly='readonly'></div></center></td>
        <td><center>$ciDiscPcent</center><div align='center'><input name='ciDiscPcent[]' type='hidden' class='pricePricePrice' 
				id='ciDiscPcent[]' value='$ciDiscPcent' readonly='readonly'></div></td>
        <td><div align='right'>$ciUnitPrice</div><div align='center'><input name='ciUnitPrice[]' type='hidden' class='pricePricePrice' id='ciUnitPrice[]' value='$ciUnitPrice' readonly='readonly'></div></td>
		<td><div align='center'>";
		/* if(mssql_num_rows($findInvoices)<=1)
			{ 	echo "---"; }
		else if(mssql_num_rows($findInvoices)>=2)
			{	echo "
				<a href='ci_transaction.php?do=newRecord&while=deleteItemNow&ciNumber=$ciNumber&ciStatus=$ciStatus&ciDate=$ciDate&strfNumber=$strfNumber&strfDate=$strfDate&custCode=$custCode&locationCode=$locationCode&pricingToUse=$pricingToUse&pricingSpecial=$pricingSpecial&remarks=$remarks&prodIte=$prdNumber'
				onclick='var deleteChild = confirm(\"This Item Product will be Deleted. Are you sure?\"); 
				if(deleteChild) { return true; } else { return false; }'><u>del</u></a>"; } */
		echo "</div></td>
      </tr>"; }
	  	}
	}
////////////////////////////////////////////////////////////////////////////////////-----
function includeIte()///////////////////////////////////////////////////////////////
	{
	$do=$_GET['do']; $while=$_GET['while'];
	if($do=="newRecord"&&$while==""||$while=="findIte")
		{ echo "onchange='lookProduct()'"; }
	else if(($do=="newRecord"||$do=="updateRecord")&&$while=="newIte")
		{ echo "onchange='lookNewProduct()'"; }
	else if(($do=="newRecord"||$do=="updateRecord")&&$while=="editItem")
		{ echo "disabled='disabled'"; }
	}
	
////////////////////////////////////////////////////////////////////////////////////-----
function toRelease()////////////////////////////////////////////////////////////////
	{
	$do=$_GET['do']; $while=$_GET['while'];
	if($do=="release"&&$while=="displayItems")
		{	echo "
<table align='center' bordercolor='#E9F8E8' bgcolor='#E9F8E8'>
              <tr>
                <td width='50%'><b>
                  <center>
                    <div align='center'><b>Month</b></div>
                  </center>
                </b></td>
                <td width='50%'><b>
                  <center>
                    <div align='center'>Year</div>
                  </center>
                  </b><b></b></td>
              </tr>
              <tr>
                <td><b>
                  <center>
                    <div align='center'><b>"; dateTodayMonth();
                    echo "</b></div>
                  </center>
                </b></td>
                <td><b>
                  <center>
                    <div align='center'><b><b>"; dateTodayYear();
                    echo "</b></b></div>
                  </center>
                  </b><b>
                  <center>
                  </center>
                  </b></td>
              </tr>
            </table>";
		}
	}
////////////////////////////////////////////////////////////////////////////////////
?>
<script src="calendar.js"></script>
<!--THIS IS THE CSS-->
<link rel='stylesheet' type='text/css' href='../../includes/style.css'></link><div id='frame_body'>
<!--THIS IS THE HEADER-->
<hr>
    <form name="form" id="form" method="post" onsubmit="return statusR(this)">
<div class='header'>
  <div class='details'>
    <div class='header'>
      <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="50%">&nbsp;</td>
          <td colspan="2"><div align='right'><?php releaseButton(); ?>
            <?php cancelButton(); ?>
          </div></td>
        </tr>
        <tr>
          <td width="55%" rowspan="2" align="center" valign="top"><table width="85%" border="0">
              <tr>
              <td width="30%"><b>C.I. Number</b></td>
              <td width="20%"><b><b>
                <?php ciNumber(); ?>
              </b></b></td>
              <td width="30%"><b><b>
                <div align="right">Date</div>
              </b></b></td>
              <td colspan="2"><b><b><b><b><b>
                <input type="text" class="textbox"  name="dateToday" id="dateToday" maxlength="11"
                <?php dateTodayStart(); ?>/>
              </b></b></b></b></b></td>
              </tr>
              <tr>
              <td><strong>C.I. Date</strong></td>
              <td><b><b><b><b><b>
                <input type="text" class="textbox"  name="ciDate" id="ciDate" maxlength="11"
                <?php calendar(); ?><?php dateOneTodayStart(); ?>/>
              </b></b></b></b></b></td>
              <td>&nbsp;</td>
              <td colspan="2">&nbsp;</td>
              </tr>
              <tr>
              <td><b><b>Operator's Name</b></b></td>
              <td><b><b><b><b><b>
                <input type="text" class="textbox"  name="docDate" id="docDate" maxlength="11"
                <?php opsName(); ?>/>
              </b></b></b></b></b></td>
              <td>&nbsp;</td>
              <td colspan="2">&nbsp;</td>
              </tr>
            <tr>
              <td><b>Location</b></td>
              <td colspan="2"><b><b>
                <b>
                <?php includeLocation(); ?>
                </b></b></b></td>
              <td width="20%">&nbsp;</td>
              <td width="20%">&nbsp;</td>
            </tr>
            <tr>
              <th colspan='5'></th>
            </tr>
          </table></td>
          <td width="4%">&nbsp;</td>
          <td width="53%"><b><b>
            <input  name="signal" type="hidden" class="textbox" id="signal" tabindex='1' <?php signal(); ?>/>
            <b>
            <input  name="prdFrcTag" type="hidden" class="textbox" id="prdFrcTag" tabindex='1' <?php prdFrcTag(); ?>/>
            <b><b><b><b>
            <input  name="whiles" type="hidden" class="textbox" id="whiles" <?php whiles(); ?>/>
            </b></b></b></b></b></b></b></td>
        </tr>
        <tr>
          <td align="center" valign="top">&nbsp;</td>
          <td align="center" valign="bottom"><?php toRelease(); ?><br /></td>
        </tr>
      </table>
    </div>
    <br />
    <table width="100%" align="center" bordercolor="#E9F8E8">
      <tr>
        <th>--</th>
        <th>Item Code</th>
        <th>Product Description</th>
        <th>U/M-Conv</th>
        <th colspan="2">Qty Ordered <br />
          Pack(s)/Box<br />
          Regular | Free</th>
        <th colspan="2">Qty Ordered <br />
          Loose/Piece(s)<br />
          Regular | Free</th>
        <th>Discount</th>
        <th>Unit Price</th>
        <th>&nbsp;</th>
      </tr>
      <?php //includedInvoice(); ?>
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
      <?php includedItems(); ?>
    </table>
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

<label>

</label>
<script type="text/javascript">
var signal = document.getElementById('signal').value;

////////////////////////////////////////////////////////////////////////////////////-----
function displayItems()/////////////////////////////////////////////////////////////
	{
	var ciNo = document.getElementById('ciNo').value;
	window.location="ci_transaction.php?do=release&while=displayItems&ciNo="+ciNo;
	return true;
	}
////////////////////////////////////////////////////////////////////////////////////-----
function statusR()//////////////////////////////////////////////////////////////////
	{
	//var signal = document.getElementById('signal').value;
	var ciNo = document.getElementById('ciNo').value;
	var location = document.getElementById('location').value;
	var ciDate = document.getElementById('ciDate').value;
	var thisMonth = document.getElementById('thisMonth').value;
	var thisYear = document.getElementById('thisYear').value;
	if(ciNo=="")
		{
		alert("C.I. Number is missing");
		document.getElementById('ciNo').focus();
		return false;
		}

	var released = confirm("You are going to release this Commercial Invoice Document! \nClick OK to continue.");
	if(released)
		{
		form.action = "ci_transaction.php?do=release&while=saveReleaseNow&ciNo="+ciNo+"&location="+location+"&strfNumber=<?php echo $_GET['strfNumber']; ?>&ciDate="+ciDate+"&thisMonth="+thisMonth+"&thisYear="+thisYear;
		return true;
		}
	else
		{
		document.getElementById('ciNo').focus();
		return false;
		}
	}
////////////////////////////////////////////////////////////////////////////////////
</script>
