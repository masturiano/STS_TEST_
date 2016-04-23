<?php
	function populatelist($myarray,$ccdata,$conname,$on_action='') 
	{
		$obj = '<select name="' . $conname . '" id="' . $conname . '"' . $on_action .'>';
		//echo $on_action;
		$ii=0; $nflag=0;
		
		while($ii < count($myarray)) 
		{
			$ddata = split("xOx",$myarray[$ii]);
			if($ddata[0] == $ccdata) 
			{
				$mselected = 'selected="selected" ';
				$nflag = 1;
			}
			else 
			{
				$mselected = '';
			}
			$obj .= '                <option ' . $mselected . ' value="' . $ddata[0] . '">' . $ddata[1] . '</option>' . "\n";						
			$ii++;
		}
		if($nflag == 0) 
		{
			$obj .= '                <option selected="selected" value="' . $ccdata . '"></option>' . "\n";
		}
		$obj .=	'</select>';
		echo $obj;
	}
	
	function showmess($error='') 
	{
		$showerror = '<script language="javascript" type="text/javascript">';
		$showerror .= 'alert("' .  $error . '")';
		$showerror .= '</script>';
		echo $showerror;
	}
	

	function showmenum($nval=0,$nhaba=0) {
		$cmval = '';
		if(($nval + 0) > 0) 
		{
			$cmval = $nval;
		}
		return $cmval;
	}
	
	function fmenum($nval=0,$nhaba=0) {
		$cmval = '';
		if(($nval + 0) > 0) {
			$cmval = str_pad(sprintf('%.2f',$nval),$nhaba,' ',STR_PAD_LEFT);
		}
		return $cmval;
	}
	
	function oa_fmenum($nval=0,$nhaba=0) {
		//return str_pad(sprintf('%.2f',$nval),$nhaba,' ',STR_PAD_LEFT);
		return str_pad(number_format($nval+0,2,'.',','),$nhaba,' ',STR_PAD_LEFT);
	}

	function oa_fmenum_4($nval=0,$nhaba=0) {
		//return str_pad(sprintf('%.2f',$nval),$nhaba,' ',STR_PAD_LEFT);
		return str_pad(number_format($nval+0,4,'.',','),$nhaba,' ',STR_PAD_LEFT);
	}
	
	function oa_dmenum($nval=0,$nhaba=0) {
		return sprintf('%.2f',$nval);
	}
	
	function oa_intonly($nval=0,$nhaba=0) {
		$nval = ((int) $nval);
		return str_pad(trim($nval,' '),$nhaba,'0',STR_PAD_LEFT);
	}
	
	function oa_nodot($nval=0,$nhaba=0) {
		$nval = number_format($nval+0,2,'.','');
		$nval = str_replace('.','',$nval);
		return str_pad(trim($nval,' '),$nhaba,'0',STR_PAD_LEFT);
	}
	
	
	function oa_ndmenum($nval=0,$nhaba=0) {
		return str_pad(sprintf('%.0f',$nval),$nhaba,' ',STR_PAD_LEFT);
	}
	
	function oa_nospchar($cdatame='') {
		//$spchar = ',-[]}{()\/|;:%@';
		$cddata = str_replace(',','',$cdatame);
		$cddata = str_replace('-','',$cddata);
		$cddata = str_replace('[','',$cddata);
		$cddata = str_replace(']','',$cddata);
		$cddata = str_replace('{','',$cddata);
		$cddata = str_replace('}','',$cddata);
		$cddata = str_replace('(','',$cddata);
		$cddata = str_replace(')','',$cddata);
		$cddata = str_replace('|','',$cddata);
		$cddata = str_replace(';','',$cddata);
		$cddata = str_replace(':','',$cddata);
		$cddata = str_replace('%','',$cddata);
		$cddata = str_replace('@','',$cddata);
		$cddata = str_replace("'",'',$cddata);
		$cddata = str_replace('"','',$cddata);
		$cddata = str_replace('^','',$cddata);
		$cddata = str_replace('&','',$cddata);
		return $cddata;
	}
	
	function mymonths() 
	{
		$mymonths =array();
		$mymonths[]=1 . "xOx" . "JANUARY";
		$mymonths[]=2 . "xOx" . "FEBRUARY";
		$mymonths[]=3 . "xOx" . "MARCH";
		$mymonths[]=4 . "xOx" . "APRIL";	
		$mymonths[]=5 . "xOx" . "MAY";
		$mymonths[]=6 . "xOx" . "JUNE";
		$mymonths[]=7 . "xOx" . "JULY";
		$mymonths[]=8 . "xOx" . "AUGUST";	
		$mymonths[]=9 . "xOx" . "SEPTEMBER";
		$mymonths[]=10 . "xOx" . "OCTOBER";
		$mymonths[]=11 . "xOx" . "NOVEMBER";
		$mymonths[]=12 . "xOx" . "DECEMBER";	
		return $mymonths;
	}

	function daysofmonths() 
	{
		$mydays = array();
		$i = 1;
		for($i = 1; $i <= 31; $i++) 
		{
			if ($i <= 9)
			{
				$mydays[] = "0". $i . "xOx" . str_pad($i,2,"0",STR_PAD_LEFT);
			}
			else
			{
				$mydays[] = $i . "xOx" . str_pad($i,2,"0",STR_PAD_LEFT);
			}
		}
		return $mydays;
	}
	
?>
