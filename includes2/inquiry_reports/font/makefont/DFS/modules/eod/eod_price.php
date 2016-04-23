<?php
////////////////////////////////////////////////////////////////////////////////////-----
function dateNextToProcess()////////////////////////////////////////////////////////
	{
	$do=$_GET['do']; $while=$_GET['while']; $dateNextToProcess=$_GET['dateNextToProcess'];
	$dateNow = date("n-j-Y", time() + (1 * 24 * 60 * 60)) ;
	if(($do==""||$do=="processEOD")&&($while!="error"))
		{	echo "value='$dateNow' readonly='readonly'";	}
	elseif(($do==""||$do=="processEOD")&&($while=="error"))
		{	echo "value='$dateNextToProcess' readonly='readonly'";	}
	}
////////////////////////////////////////////////////////////////////////////////////-----
function dateToProcess()////////////////////////////////////////////////////////
	{
	$do=$_GET['do']; $while=$_GET['while']; $dateToProcess=$_GET['dateToProcess'];
	//$dateNow = date("n-j-Y", time() + (1 * 24 * 60 * 60)) ;
	$dateNow = date("n-j-Y");
	if(($do==""||$do=="processEOD")&&($while!="error"))
		{	echo "value='$dateNow' readonly='readonly'";	}
	elseif(($do==""||$do=="processEOD")&&($while=="error"))
		{	echo "value='$dateToProcess' readonly='readonly'";	}
	}
////////////////////////////////////////////////////////////////////////////////////-----
function calendar()/////////////////////////////////////////////////////////////////
	{
	echo  "onfocus='this.select();lcs(this)' onclick='event.cancelBubble=true;this.select();lcs(this)'";
	}
////////////////////////////////////////////////////////////////////////////////////-----
function signal()///////////////////////////////////////////////////////////////////
	{
	$do = $_GET['do'];
	echo "value='$do' readonly='readonly'";
	}
////////////////////////////////////////////////////////////////////////////////////-----
function opsName()//////////////////////////////////////////////////////////////////
	{
	$do=$_GET['do']; $while=$_GET['while'];
	$username = "1234";
	echo "value='$username' disabled='disabled'";
	}
////////////////////////////////////////////////////////////////////////////////////-----
function eodButton()/////////////////////////////////////////////////////////////
	{
	$do = $_GET['do']; $while=$_GET['while'];
	if($do==""||$do=="processEOD")
		{	echo "<input type='submit' class='queryButton' name='saveRecordbutt' id='saveRecordbutt' value='Process End of Day' title='Process End of Day'/>";	}
	}
////////////////////////////////////////////////////////////////////////////////////-----
function cancelButton()/////////////////////////////////////////////////////////////
	{
	$do = $_GET['do']; $while=$_GET['while'];
	if($do==""||$do=="processEOD")
		{	echo "<input type='button' class='queryButton' name='cancelButt' id='cancelButt' value='Cancel' title='Cancel' onclick='var confirmCancel = confirm(\"Are you sure you want to terminate End of Day?\"); if(confirmCancel) { window.location=\"eod_main.php\"}' disabled='disabled'/>"; }
	}
////////////////////////////////////////////////////////////////////////////////////-----
function whiles()///////////////////////////////////////////////////////////////////
	{
	$while = $_GET['while'];
	echo "value='$while' readonly='readonly'";
	}
////////////////////////////////////////////////////////////////////////////////////
?>
<script src="calendar.js"></script>
<!--THIS IS THE CSS-->
<link rel='stylesheet' type='text/css' href='../../includes/style.css'></link><div id='frame_body'>
<!--THIS IS THE HEADER-->
<hr>
    <form name="form" id="form" method="post" onsubmit="return eOd(this)">
<div class='header'>
  <div class='details'>
    <div class='header'>
      <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="50%">&nbsp;</td>
          <td colspan="2"><div align='right'><?php eodButton(); ?>
            <?php cancelButton(); ?>
          </div></td>
        </tr>
        <tr>
          <td width="55%" rowspan="2" align="center" valign="top"><table width="85%" border="0">
              <tr>
              <td width="30%"><b><b>Operator's Name:</b></b></td>
              <td width="20%">&nbsp;</td>
              <td width="30%"><b><b><b><b><b>
                <input type="text" class="textbox"  name="opsName" id="opsName" maxlength="11"
                <?php opsName(); ?>/>
              </b></b></b></b></b></td>
              <td colspan="2">&nbsp;</td>
              </tr>
              <tr>
              <td colspan="2"><b><b>Processing Date to Close/End:</b></b></td>
              <td><b><b><b><b><b><b><b><b><b>
                <input type="text" class="textbox"  name="dateToProcess" id="dateToProcess" maxlength="11"
                <?php calendar(); ?><?php dateToProcess(); ?>/>
              </b></b></b></b></b></b></b></b></b></td>
              <td colspan="2">&nbsp;</td>
              </tr>
            <tr>
              <td>&nbsp;</td>
              <td colspan="2">&nbsp;</td>
              <td width="20%">&nbsp;</td>
              <td width="20%">&nbsp;</td>
            </tr>
            <tr>
              <td colspan="2"><b>Next Processing Date:</b></td>
              <td><b><b><b><b><b>
                <input type="text" class="textbox"  name="dateNextToProcess" id="dateNextToProcess" maxlength="11"
                <?php calendar(); ?><?php dateNextToProcess(); ?>/>
              </b></b></b></b></b></td>
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
            <b><b><b><b><b>
            <input  name="whiles" type="hidden" class="textbox" id="whiles" <?php whiles(); ?>/>
            </b></b></b></b></b></b></b></td>
        </tr>
        <tr>
          <td align="center" valign="top">&nbsp;</td>
          <td align="center" valign="bottom"><br /></td>
        </tr>
      </table>
    </div>
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

<label>

</label>
<script type="text/javascript">
var whiles = document.getElementById('whiles').value;
var dateToProcess = document.getElementById('dateToProcess').value;
if(whiles=="error")
	{
	alert("Sorry, but you've already Processed the \"End of Day\" on Date "+dateToProcess);
	document.getElementById('dateToProcess').focus();
	document.getElementById('dateToProcess').select();
	}
	
function eOd()
	{
	var opsName = document.getElementById('opsName').value;
	var dateToProcess = document.getElementById('dateToProcess').value;
	var dateNextToProcess = document.getElementById('dateNextToProcess').value;
	if(opsName=="")
		{
		alert ("Operator's Name: Missing");
		document.getElementById('opsName').focus();
		return false;
		}
	else if(dateToProcess>dateNextToProcess)
		{
		alert ("Next Processing Date: Must not be earlier than Processing Date");
		document.getElementById('dateNextToProcess').focus();
		document.getElementById('dateNextToProcess').select();
		return false;
		}
	else
		{
		var eodConfirm = confirm("Process End of Day... Do you want to continue?");
		if(eodConfirm)
			{
			form.action = "eod_transaction.php?do=processEOD&opsName="+opsName+"&dateToProcess="+dateToProcess+"&dateNextToProcess="+dateNextToProcess;
			return true;
			}
		else
			{
			alert ("End of Day Processing Terminated...");
			return false;
			}
		}
	}
////////////////////////////////////////////////////////////////////////////////////
</script>
