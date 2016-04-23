<?php
	require("../../includes/config.php");
	require("../../functions/db_function.php");

	$db = new DB;
	$db->connect();


	function allowType()
	{
		$strAllow = "SELECT UPPER(ALLWTYPECODE) AS ALLWTYPECODE, UPPER(ALLWDESC) AS ALLWDESC FROM TBLALLOWTYPE WHERE ALLWSTAT = 'A'";
		$qryAllow = mssql_query($strAllow);

		$ArrAllowType = array();
		while ($rstAllow = mssql_fetch_array($qryAllow))
		{
			$ArrAllowType[] = $rstAllow[0] . 'xOx' . $rstAllow[1];
		}
		return $ArrAllowType;
	}
	
	
?>