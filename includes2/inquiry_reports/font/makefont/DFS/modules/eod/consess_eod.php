<?php
//*************************************************************************
//**	Program Name			:	EOD
//**	Program Description 	:	End of Day Functions
//**	Author					:	Louie B. Datuin
//*************************************************************************		

	class EODFunc
	{		
		function ConsessEOD($DateToProcess, $CompCode, $User)
		{	
			//*************************************************************************
			//**	Clear Product Price
			//**	11/08/2008																		
			//*************************************************************************		
			
			$now = date("m/d/Y");
			//CLEARPRODUCTPRICE  -> CLEARCONCESSPRICE
			$ClearProduct = mssql_query("SELECT * FROM CLEARCONCESSPRICE WHERE (COMPCODE = '$CompCode') AND  (PROMOUNITPRICE > 0) AND
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
			//EXTRACTREGULARPRICE -> ExtractRegularConcessPrice
			$ExtractRegular = mssql_query("SELECT * FROM ExtractRegularConcessPrice WHERE (COMPCODE = '$CompCode') AND  (PRTYPECODE = 1) AND
				(PRENDDAYTAG IS NULL) AND (PRPLANSTATUS IS NULL) AND (PRSTARTDATE < '$DateToProcess')");
			
			$Prod  = "";
			$Start = "";
			$Prec  = "";
			$End   = "";
			$Code  = "";					
			$Event = "";
			$Type  = "";
								
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
						
						$Update = "UPDATE TBLCONSESSPLAN SET PRPRICETAG = 1, PRENDDAYTAG = 'Y' WHERE (COMPCODE = '$CompCode') AND 
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
						
						$Update = "UPDATE TBLCONSESSPLAN SET PRPRICETAG = 1, PRENDDAYTAG = 'Y' WHERE (COMPCODE = '$CompCode') AND 
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
			//EXTRACTPROMOPRICE -> ExtractPromoConcessPrice	
			$ExtractPromo = mssql_query("SELECT * FROM ExtractPromoConcessPrice WHERE (COMPCODE = '$CompCode') AND (PRTYPECODE > 1) 
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

								$Update = "UPDATE TBLCONSESSPLAN SET PRPRICETAG = '1', PRENDDAYTAG = 'Y' WHERE (COMPCODE = '$CompCode') 
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

						$Update = "UPDATE TBLCONSESSPLAN SET PRENDDAYTAG = 'Y' WHERE (COMPCODE = '$CompCode') 
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
			//CLOSEPROMOPRICE -> ClosePromoConcessPrice	
			$ClosePromo = mssql_query("SELECT * FROM ClosePromoConcessPrice WHERE (COMPCODE = '$CompCode') AND (PRPLANSTATUS IS NULL) AND
				(PRTYPECODE > 1) AND (PRENDDATE < '$DateToProcess')");
				
			while ($ClosePromoCost = mssql_fetch_array($ClosePromo))
			{
				$Update = "UPDATE TBLCONSESSPLAN SET PRPLANSTATUS = 'C' WHERE (COMPCODE = '$CompCode') AND (PRENDDATE < '$DateToProcess')";
				mssql_query($Update);
			}
		}
	}
?>
