<?
include "../../functions/inquiry_session.php";
require_once "../../includes/config.php";
require_once "../../functions/db_function.php";
require_once "../etc/etc.obj.php";
require_once "../../functions/inquiry_function.php";
$gmt = time() + (8 * 60 * 60);
$date = date("m-d-Y", $gmt);
$db = new DB;
$db->connect();

###########################################################
$fromdate=$_POST['hide_from_date2'];
$po_number=$_POST['hide_po_number2'];
$vendor=$_POST['hide_vendor2'];
$todate=$_POST['hide_to_date2'];
$hide_sql=$_POST['hide_sql2'];
$hide_find_vendor=$_POST['hide_find_vendor_details2'];
$hide_numeric=$_POST['hide_numeric_details2'];
$prod_number=$_POST['radio_view_prod'];
###########################################################

###########################################################
$query_po_rcr="SELECT * FROM tblRcrHeader WHERE poNumber = $po_number";
$result_po_rcr=mssql_query($query_po_rcr);
$num_po_rcr = mssql_num_rows($result_po_rcr);
if ($num_po_rcr > 0) {
	$grid_rcr_number=mssql_result($result_po_rcr,0,"rcrNumber");
	$grid_date=mssql_result($result_po_rcr,0,"rcrDate");
	if ($grid_date>"") {
		$date = new DateTime($grid_date);
		$grid_date = $date->format("m-d-Y");		
	} else {
		$grid_date="";
	}
	$query_rcr_details="SELECT * FROM tblRcrItemDtl WHERE prdNumber = $prod_number AND rcrNumber = $grid_rcr_number";
	$result_rcr_details=mssql_query($query_rcr_details);
	$num_rcr_details = mssql_num_rows($result_rcr_details);	
} 
###########################################################

?>

<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript" type="text/JavaScript">
<!--
function MM_reloadPage(init) {  //reloads the window if Nav4 resized
  if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
    document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
  else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
}
MM_reloadPage(true);
//-->
</script>
</head>

<body>
<div class='header'> 
  <div class='header'> 
    <div class='details'>
<form action="po_inq_details.php" method="post" name="formissi" id="formissi">
        <table width="100%" border="0">
          <tr bgcolor="#DEEDD1"> 
            <td width="11%"><div align="right"><font size="2" face="Arial, Helvetica, sans-serif">From 
                Date<b><b> </b></b></font></div></td>
            <td width="88%"><font size="2" face="Arial, Helvetica, sans-serif"><b><b><b> 
              <input name="fromdate" type="text" id="fromdate" value="<? echo $fromdate; ?>" size="10" maxlength="10" readonly="true">
              </b></b></b>To Date<b><b><b> 
              <input name="todate" type="text" id="todate" value="<? echo $todate; ?>" size="10" maxlength="10" readonly="true">
              </b></b></b></font></td>
          </tr>
          <tr bgcolor="#DEEDD1"> 
            <td width="11%" height="24"><div align="right"><font size="2" face="Arial, Helvetica, sans-serif"> 
                Vendor </font></div></td>
            <td><font size="2" face="Arial, Helvetica, sans-serif"> 
              <input name="vendor" type="text" id="vendor" value="<? echo $vendor; ?>" size="50" readonly="true">
              <span class="style20"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><span class="style18"><b><b><span class="style1"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b> 
              </b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></b></b></span></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span> 
              <span class="style20"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><span class="style18"><b><b><span class="style1"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b> 
              </b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></b></b></span></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span> 
              <b><b><b> </b></b></b> </font></td>
          </tr>
          <tr bgcolor="#DEEDD1"> 
            <td><div align="right"><font size="2" face="Arial, Helvetica, sans-serif">PO 
                Number </font></div></td>
            <td><font size="2" face="Arial, Helvetica, sans-serif"> 
              <input name="po_number" type="text" id="po_number" value="<? echo $po_number; ?>" size="25" readonly="true">
              Product Number 
              <input name="po_number2" type="text" id="po_number2" value="<? echo $prod_number; ?>" size="25" readonly="true">
              </font></td>
          </tr>
          <tr bgcolor="#DEEDD1"> 
            <td>&nbsp;</td>
            <td><font size="2" face="Arial, Helvetica, sans-serif"> 
              <input name='po_inq_received_back_button' type='submit' class='queryButton' id='inquire4' title='Back to PO Header' value='Back'/>
              <span class="style20"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><span class="style18"><b><b><span class="style1"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b> 
              <input name="hide_from_date" type="hidden" id="hide_from_date2" value="<?php echo $fromdate; ?>">
              <input name="hide_to_date" type="hidden" id="hide_numeric2" value="<?php echo $todate; ?>">
              <input name="hide_vendor" type="hidden" id="hide_find_vendor3" value="<?php echo $vendor; ?>">
              <input name="hide_po_number" type="hidden" id="hide_po_number" value="<?php echo $po_number; ?>">
              <input name="hide_sql" type="hidden" id="hide_sql" value="<?php echo $hide_sql; ?>">
              <input name="hide_find_vendor" type="hidden" id="hide_find_vendor" value="<?php echo $hide_find_vendor; ?>">
              <input name="hide_numeric" type="hidden" id="hide_numeric" value="<?php echo $hide_numeric; ?>">
              <input name="hide_prod_number" type="hidden" id="hide_numeric3" value="<?php echo $prod_number; ?>">
              </b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></b></b></span></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></font> 
              <font size="2" face="Arial, Helvetica, sans-serif"><span class="style20"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><span class="style18"><b><b><span class="style1"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b> 
              </b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></b></b></span></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span> 
              </font></td>
          </tr>
        </table>
        </form>
      <p>&nbsp;</p>
      <p>&nbsp;</p>
      <p>&nbsp;</p>
      <p>&nbsp;</p>
      <p>&nbsp;</p>
      <form action="po_inq_received.php" method="post" name="form_po_received" id="form_po_received">
        <div align="center">
          <div id="Layer1" style="position:absolute; left:5px; top:122px; width:99%; height:153px; z-index:1; overflow: auto;"> 
            <table width="45%" border="0" align="center" >
              <tr bgcolor="#DEEDD1"> 
                <td height="24" colspan="5"><div align="center"><font color="#FF0000" size="2" face="Arial, Helvetica, sans-serif">Details 
                    of Received </font><font size="2" face="Arial, Helvetica, sans-serif"><span class="style20">
                    <?
				  if ($num_rcr_details>"") {
						echo " - ". $num_rcr_details . " record/s found.";	  
				  } else {
				  		echo  "";
				  }
		  
		  ?>
                    </span></font></div></td>
              </tr>
              <tr bgcolor="#6AB5FF"> 
                <td width="77"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">RCR 
                    Number </font></strong></div></td>
                <td width="82"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">RCR 
                    Date </font></strong></div></td>
                <td width="63" height="24"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">Good</font></strong></div></td>
                <td width="63"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif"><span class="style8 style20">Bad</span></font></strong></div></td>
                <td width="54"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">Free 
                    <? 
				for ($i=0;$i<$num_rcr_details;$i++){ 
					$grid_number=mssql_result($result_rcr_details,$i,"rcrNumber");
					$grid_good=mssql_result($result_rcr_details,$i,"rcrQtyGood");
					$grid_bo=mssql_result($result_rcr_details,$i,"rcrQtyBad");
					$grid_free=mssql_result($result_rcr_details,$i,"rcrQtyFree");
			?>
                    </font></strong></div></td>
              </tr>
              <tr bgcolor="#DEEDD1"> 
                <td bgcolor="#DEEDD1"><font size="2" face="Arial, Helvetica, sans-serif"> 
                  <?
			echo $grid_number;
		  ?>
                  </font></td>
                <td><font size="2" face="Arial, Helvetica, sans-serif">
                  <?
				echo $grid_date;
		  ?>
                  </font></td>
                <td><div align="right"><font size="2" face="Arial, Helvetica, sans-serif"> 
                    <?
			echo $grid_good;
		  ?>
                    </font></div></td>
                <td><div align="right"><font size="2" face="Arial, Helvetica, sans-serif"><span class="style20"> 
                    <?
			
		  echo $grid_bo;
		  ?>
                    </span></font></div></td>
                <td><div align="right"><font size="2" face="Arial, Helvetica, sans-serif"><span class="style20"> 
                    </span><span class="style20"> 
                    <?
		  echo $grid_free;
		  ?>
                    </span><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><span class="style18"><b><b><span class="style20"> 
                    <?
		  }
		  ?>
                    </span></b></b></span></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></font></div></td>
              </tr>
            </table>
          </div>
          <font size="2" face="Arial, Helvetica, sans-serif"> <span class="style20"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><span class="style18"><b><b><span class="style1"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b>
          <input name="hide_po_number2" type="hidden" id="hide_po_number2" value="<?php echo $po_number; ?>">
          </b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></b></b></span></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></font> 
        </div>
      </form>
      <form action="" method="post" name="form1" id="form1">
      </form>
      <p align="center"><font size="2" face="Arial, Helvetica, sans-serif"> </font><font size="2" face="Arial, Helvetica, sans-serif"> 
        </font></p>
    </div>
  </div>
</div>
</body>
</html>
