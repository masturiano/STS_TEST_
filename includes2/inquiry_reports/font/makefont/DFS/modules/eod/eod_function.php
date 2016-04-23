<?php
//*************************************************************************
//**	Program Name			:	EOD
//**	Program Description 	:	End of Day Functions
//**	Author					:	Louie B. Datuin
//*************************************************************************		

	class EODFunc
	{
		function CostEOD($DateToProcess, $CompCode, $User)
		{
			//*************************************************************************
			//**	Clear Product Cost
			//**	11/07/2008																		
			//*************************************************************************		
			$ClearProduct = mssql_query("SELECT * FROM CLEARPRODUCTCOST WHERE (COMPCODE = '$CompCode') AND  (PROMOUNITCOST > 0) AND
				(PROMOCOSTSTART IS NOT NULL) AND (PROMOCOSTEND IS NOT NULL) AND (PROMOCOSTEND < '$DateToProcess')");
			
			while ($ClearProdCost = mssql_fetch_array($ClearProduct))
			{
				$Update = "UPDATE TBLPRODCOST SET PROMOCOSTEVENT = 0, CSTTYPECODE = 0, PROMOCOSTSTART = NULL, PROMOCOSTEND = NULL, 
					PROMOUNITCOST = 0 WHERE (COMPCODE = '$CompCode') AND (PROMOCOSTEND < '$DateToProcess')";
				mssql_query($Update);
			}
	
			//*************************************************************************
			//**	New Regular Cost Procedure
			//**	11/07/2008																		
			//*************************************************************************		
			$ExtractRegular = mssql_query("SELECT * FROM EXTRACTREGULARCOST WHERE (COMPCODE = '$CompCode') AND  (CSTTYPECODE = 1) AND
				(CSTENDDAYTAG IS NULL) AND (CSTPLANSTATUS IS NULL) AND (CSTSTARTDATE < '$DateToProcess')");
			
			$Supp =  "";
			$Prod =  "";
			$Event = "";
			$Start = "";
			$Cost =  "";
			$Prec =  "";
								
			do 
			{
				if ($RegularCost[0] == "")
				{
				
				}
				else
				{
					$Supp = $RegularCost[1];
					$Prod = $RegularCost[2];
					$Code = $RegularCost[6];
					$Conv = $RegularCost[7];
					$Event = $RegularCost[9];
					$Start = $RegularCost[3];
					$Cost = $RegularCost[11];
					$Type = $RegularCost[10];
					$Prec = $RegularCost[4];
					
					$CheckProduct = mssql_query("SELECT * FROM VIEWPRODCOST WHERE (COMPCODE = '$CompCode') AND (SUPPCODE = '$Supp') 
						AND (PRDNUMBER = '$Prod')");
					
					//If First Record	
					if (mssql_num_rows($CheckProduct) == 1)
					{
						$Update = "UPDATE TBLPRODCOST SET REGCOSTEVENT = '$Event', REGCOSTSTART = '$Start', REGUNITCOST = '$Cost', 
							CSTTYPECODE = '$Type' WHERE (COMPCODE = '$CompCode') AND (SUPPCODE = '$Supp') AND (PRDNUMBER = '$Prod')";
						mssql_query($Update);
						
						$Update = "UPDATE TBLCOSTPLAN SET CSTCOSTTAG = 1, CSTENDDAYTAG = 'Y' WHERE (COMPCODE = '$CompCode') AND 
						(SUPPCODE = '$Supp') AND (PRDNUMBER = '$Prod') AND (CSTSTARTDATE = '$Start') AND (CSTPRECEDENCE = '$Prec')";
						mssql_query($Update);
					}
					//If Record Exists
					else
					{
						$Insert = "INSERT INTO TBLPRODCOST (COMPCODE, SUPPCODE, PRDNUMBER, UMCODE, PRDCONV, REGCOSTEVENT, REGCOSTSTART, 
							REGUNITCOST, CSTTYPECODE) VALUES ('$CompCode', '$Supp', '$Prod', '$Code', '$Conv', '$Event', 
							'$Start', '$Cost', '$Type')";
						mssql_query($Insert);
						
						$Update = "UPDATE TBLCOSTPLAN SET CSTCOSTTAG = 1, CSTENDDAYTAG = 'Y' WHERE (COMPCODE = '$CompCode') AND 
						(SUPPCODE = '$Supp') AND (PRDNUMBER = '$Prod') AND (CSTSTARTDATE = '$Start') AND (CSTPRECEDENCE = '$Prec')";
						mssql_query($Update);
					}
				}
			}
			while ($RegularCost = mssql_fetch_array($ExtractRegular));
			
			//*************************************************************************
			//**	New Promo Cost Procedure
			//**	11/07/2008																		
			//*************************************************************************		
						
			$ExtractPromo = mssql_query("SELECT * FROM EXTRACTPROMOCOST WHERE (COMPCODE = '$CompCode') AND (CSTTYPECODE > 1) 
				AND (CSTENDDAYTAG IS NULL) AND (CSTPLANSTATUS IS NULL) AND (CSTSTARTDATE < '$DateToProcess')");
	
			$Supp = "";
			$Prod = "";
			$Code = "";
			$Conv = "";					
			$Cost = "";
			$Start = "";
			$End = "";
			$Event = "";
			$Type = "";
								
			do 
			{
				if ($PromoCost[0] == "")
				{
				}
				else
				{
					$Supp = $PromoCost[1];
					$Prod = $PromoCost[2];
					$Code = $PromoCost[6];
					$Conv = $PromoCost[7];
					$Cost = $PromoCost[11];
					$Start = $PromoCost[3];
					$End = $PromoCost[8];
					$Event = $PromoCost[9];
					$Type = $PromoCost[10];
					
					$CheckProduct = mssql_query("SELECT * FROM VIEWPRODCOST WHERE (COMPCODE = '$CompCode') AND (SUPPCODE = '$Supp') 
						AND (PRDNUMBER = '$Prod')");
					
					//If Record Exists
					if (mssql_num_rows($CheckProduct) == 1)
					{
						$GetProduct = mssql_fetch_array($CheckProduct);
						//If Supplier/Product(Variable) = Supplier/Product(Product Cost)
						if (($Supp == $GetProduct[1]) and ($Prod == $GetProduct[2]))
						{
							//If End Date < Next Process Date
							if ($GetProduct[11] < $DateToProcess)
							{
								$Update = "UPDATE TBLPRODCOST SET PROMOUNITCOST = '$Cost', PROMOCOSTSTART = '$Start', PROMOCOSTEND = '$End',
									PROMOCOSTEVENT = '$Event', CSTTYPECODE = '$Type' WHERE (COMPCODE = '$CompCode') AND (SUPPCODE = '$Supp')
									AND (PRDNUMBER = '$Prod')";
								mssql_query($Update);
	
								$Update = "UPDATE TBLCOSTPLAN SET CSTCOSTTAG = 1, CSTENDDAYTAG = 'Y' WHERE (COMPCODE = '$CompCode') 
									AND (SUPPCODE = '$Supp') AND (PRDNUMBER = '$Prod') AND (CSTEVENTNO = '$Event')";
								mssql_query($Update);
							}
							//If End Date (Saved) < Ending Date (Product Table)
							if ($Date < $GetProduct[11])
							{
								$Update = "UPDATE TBLCOSTPLAN SET CSTCOSTTAG = 1, CSTENDDAYTAG = 'Y' WHERE (COMPCODE = '$CompCode') 
									AND (SUPPCODE = '$Supp') AND (PRDNUMBER = '$Prod') AND (CSTEVENTNO = '$Event')";
								mssql_query($Update);
							}
						}
					}
					else
					{
						$Insert = "INSERT INTO TBLPRODCOST (COMPCODE, SUPPCODE, PRDNUMBER, UMCODE, PRDCONV, PROMOCOSTEVENT, PROMOCOSTSTART, 
							PROMOCOSTEND, PROMOUNITCOST, CSTTYPECODE) VALUES ('$CompCode', '$Supp', '$Prod', '$Code', '$Conv', '$Event', 
							'$Start', '$End', '$Cost', '$Type')";
						mssql_query($Insert);
	
						$Update = "UPDATE TBLCOSTPLAN SET CSTENDDAYTAG = 'Y' WHERE (COMPCODE = '$CompCode') 
							AND (SUPPCODE = '$Supp') AND (PRDNUMBER = '$Prod') AND (CSTEVENTNO = '$Event')";
						mssql_query($Update);
					}
				}
			}
			while ($PromoCost = mssql_fetch_array($ExtractPromo));
			
			//*************************************************************************
			//**	Close Promo Cost
			//**	11/07/2008																		
			//*************************************************************************		
			$ClosePromo = mssql_query("SELECT * FROM CLOSEPROMOCOST WHERE (COMPCODE = '$CompCode') AND  (CSTPLANSTATUS IS NULL) AND
				(CSTTYPECODE > 1) AND (CSTENDDATE < '$DateToProcess')");
				
			while ($ClosePromoCost = mssql_fetch_array($ClosePromo))
			{
				$Update = "UPDATE TBLCOSTPLAN SET CSTPLANSTATUS = 'C' WHERE (COMPCODE = '$CompCode') AND (CSTENDDATE < '$DateToProcess')";
				mssql_query($Update);
			}
		}
		
		function PriceEOD($DateToProcess, $CompCode, $User)
		{
			
			//*************************************************************************
			//**	Clear Product Price
			//**	11/08/2008																		
			//*************************************************************************		
			
			$now = date("m/d/Y");
			$ClearProduct = mssql_query("SELECT * FROM CLEARPRODUCTPRICE WHERE (COMPCODE = '$CompCode') AND  (PROMOUNITPRICE > 0) AND
				(PROMOPRICESTART IS NOT NULL) AND (PROMOPRICEEND IS NOT NULL) AND (PROMOPRICEEND < '$DateToProcess')");
			
			while ($ClearProdCost = mssql_fetch_array($ClearProduct))
			{
				$Update = "UPDATE TBLPRODCOST SET PROMOCOSTEVENT = 0, CSTTYPECODE = 0, PROMOCOSTSTART = NULL, PROMOCOSTEND = NULL, 
					PROMOUNITCOST = 0 WHERE (COMPCODE = '$CompCode') AND (PROMOCOSTEND < '$DateToProcess')";
				mssql_query($Update);
			}

			//*************************************************************************
			//**	New Regular Price Procedure
			//**	11/08/2008																		
			//*************************************************************************		
			$ExtractRegular = mssql_query("SELECT * FROM EXTRACTREGULARPRICE WHERE (COMPCODE = '$CompCode') AND  (PRTYPECODE = 1) AND
				(PRENDDAYTAG IS NULL) AND (PRPLANSTATUS IS NULL) AND (PRSTARTDATE < '$DateToProcess')");
			
			$Prod = "";
			$Start = "";
			$Prec = "";
			$End = "";
			$Code = "";					
			$Event = "";
			$Type = "";
								
			do 
			{
				if ($RegularPrice[0] == "")
				{
				
				}
				else
				{
					$Prod  = $RegularPrice[1];
					$Start = $RegularPrice[2];
					$Prec  = $RegularPrice[3];
					$End   = $RegularPrice[4];
					$Code  = $RegularPrice[5];					
					$Event = $RegularPrice[6];
					$Type  = $RegularPrice[7];						
					$Price = $RegularPrice[8];
					
					$CheckProduct = mssql_query("SELECT * FROM VIEWPRODPRICE WHERE (COMPCODE = '$CompCode') AND (PRDNUMBER = '$Prod')");
					
					//If First Record	
					if (mssql_num_rows($CheckProduct) == 1)
					{
						$Update = "UPDATE TBLPRODPRICE SET REGPRICEEVENT = '$Event', REGPRICESTART = '$Start', REGUNITPRICE = '$Price', DATEUPDATED = '{$now}', LASTUPDATEDBY = '{$User}'
							WHERE (COMPCODE = '$CompCode') AND (PRDNUMBER = '$Prod')";
						mssql_query($Update);
						
						$Update = "UPDATE TBLPRICEPLAN SET PRPRICETAG = 1, PRENDDAYTAG = 'Y' WHERE (COMPCODE = '$CompCode') AND 
							(PRDNUMBER = '$Prod') AND (PRSTARTDATE = '$Start') AND (PRPRECEDENCE = '$Prec')";
						mssql_query($Update);
					}
					//If Record Exists
					else
					{
						$gmt = time() + (8 * 60 * 60);
						$date = date("m-d-Y", $gmt);	
												
						$Insert = "INSERT INTO TBLPRODPRICE (COMPCODE, PRDNUMBER, UMCODE, REGPRICEEVENT, REGPRICESTART, REGUNITPRICE, 
							DATEUPDATED, LASTUPDATEDBY) 
							VALUES ('$CompCode', '$Prod', '$Code', '$Event', '$Start', '$Price', '$date', '$User')";
						mssql_query($Insert);
						
						$Update = "UPDATE TBLPRICEPLAN SET PRPRICETAG = 1, PRENDDAYTAG = 'Y' WHERE (COMPCODE = '$CompCode') AND 
							(PRDNUMBER = '$Prod') AND (PRSTARTDATE = '$Start') AND (PRPRECEDENCE = '$Prec')";
						mssql_query($Update);
					}
				}
			}
			while ($RegularPrice = mssql_fetch_array($ExtractRegular));
			
			//*************************************************************************
			//**	New Promo Price Procedure
			//**	11/07/2008																		
			//*************************************************************************		
						
			$ExtractPromo = mssql_query("SELECT * FROM EXTRACTPROMOPRICE WHERE (COMPCODE = '$CompCode') AND (PRTYPECODE > 1) 
				AND (PRENDDAYTAG IS NULL) AND (PRPLANSTATUS IS NULL) AND (PRSTARTDATE < '$DateToProcess')");

			$Prod = "";
			$Start = "";
			$Prec = "";
			$End = "";
			$Code = "";					
			$Event = "";
			$Type = "";
								
			do 
			{
				if ($PromoPrice[0] == "")
				{
				}
				else
				{
					$Prod = $PromoPrice[1];
					$Start = $PromoPrice[2];
					$Prec = $PromoPrice[3];
					$End = $PromoPrice[4];
					$Code = $PromoPrice[5];					
					$Event = $PromoPrice[6];
					$Type = $PromoPrice[7];						
					$Price = $PromoPrice[8];
					
					$CheckProduct = mssql_query("SELECT * FROM VIEWPRODPRICE WHERE (COMPCODE = '$CompCode') AND (PRDNUMBER = '$Prod')");
					
					//If Record Exists
					if (mssql_num_rows($CheckProduct) == 1)
					{
						$GetProduct = mssql_fetch_array($CheckProduct);
						//If Product(Variable) = Product(Product Cost)
						if ($Prod == $GetProduct[1])
						{
							//If End Date < Next Process Date
							if ($GetProduct[9] < $DateToProcess)
							{
								$Update = "UPDATE TBLPRODPRICE SET PROMOUNITPRICE = '$Price', PROMOPRICESTART = '$Start', PROMOPRICEEND = '$End',
									PROMOPRICEEVENT = '$Event', DATEUPDATED = '{$now}', LASTUPDATEDBY = '{$User}' WHERE (COMPCODE = '$CompCode') AND (PRDNUMBER = '$Prod') ";
								mssql_query($Update);

								$Update = "UPDATE TBLPRICEPLAN SET PRPRICETAG = '1', PRENDDAYTAG = 'Y' WHERE (COMPCODE = '$CompCode') 
									AND (PRDNUMBER = '$Prod') AND (PRSTARTDATE = '$Start') AND (PRPRECEDENCE = '$Prec')";
								mssql_query($Update);
							}
						}
					}
					else
					{
						 
						$Insert = "INSERT INTO TBLPRODPRICE (COMPCODE, PRDNUMBER, UMCODE, PROMOPRICEEVENT, PROMOPRICESTART, 
							PROMOPRICEEND, PROMOUNITPRICE, PRTYPECODE,DATEUPDATED,LASTUPDATEDBY) VALUES ('$CompCode', '$Prod', '$Code', '$Event', '$Start', '$End', 
							'$Price', '1','{$now}','{$User}'";
						mssql_query($Insert);

						$Update = "UPDATE TBLPRICEPLAN SET PRENDDAYTAG = 'Y' WHERE (COMPCODE = '$CompCode') 
							AND (PRDNUMBER = '$Prod') AND (PRSTARTDATE = '$Start') AND (PRPRECEDENCE = '$Prec')";
						mssql_query($Update);
					}
				}
			}
			while ($PromoPrice = mssql_fetch_array($ExtractPromo));
			
			//*************************************************************************
			//**	Close Promo Price
			//**	11/07/2008																		
			//*************************************************************************		
			$ClosePromo = mssql_query("SELECT * FROM CLOSEPROMOPRICE WHERE (COMPCODE = '$CompCode') AND (PRPLANSTATUS IS NULL) AND
				(PRTYPECODE > 1) AND (PRENDDATE < '$DateToProcess')");
				
			while ($ClosePromoCost = mssql_fetch_array($ClosePromo))
			{
				$Update = "UPDATE TBLPRICEPLAN SET PRPLANSTATUS = 'C' WHERE (COMPCODE = '$CompCode') AND (PRENDDATE < '$DateToProcess')";
				mssql_query($Update);
			}
		}
	}
?>
