<?

function fPageLinks($limit_start = 0, $record_count = 0, $limit =0, $action="")
{
	
	$lastrec    = $limit_start;
	$from		= $limit_start+1;
	$tmp		= $limit_start+$limit;
	
	if($tmp >= $record_count)
		$to=$record_count;
	else
		$to=$limit_start+$limit;
		
	if($from > $to)
	{
		$from = 0;
  		$limit_start = 0;
	}

	if ($limit_start	== 0)
	{
		$prevrec = 0;
		$nextrec = $limit;
	}
	elseif ($limit_start > 0)
	{
		$prevrec = $limit_start - $limit;
		$nextrec = $limit_start + $limit;
	}

	if ($prevrec < 0)
		$prevrec = 0;
	elseif ($nextrec >= $record_count)
		$nextrec = $lastrec;
	
	//compute for value of 'last link'
	
	$nRemain = $record_count%$limit;
	if($nRemain == 0){
		$val_last = $record_count-$limit;	
	}
	else{
		$val_last =  $record_count-$nRemain;	
	}
	
	$prev = "<img height='55%' src=../../Images/prev_blu.gif title='previous'> ";
	$next = "<img height='55%' src=../../Images/next_blu.gif title='next'> ";
	$first = "<img height='55%' src=../../Images/start_blu.gif title='first'> ";
	$last = "<img height='55%' src=../../Images/end_blu.gif title='last'> ";
	if ($limit_start > 0)
	{
		$first = "<img height='55%' src='../../Images/start_blu.gif'   onClick=\"pagerAjax('".basename($_SERVER['PHP_SELF'])."',0,'$action')\"
					name=\"start\" onMouseOver=\"fChangeImg('start');\" onMouseOut=\"fRestoreImg('start');\" title='first'> ";
		$prev = "<img height='55%' src=\"../../Images/prev_blu.gif\"  onClick=\"pagerAjax('".basename($_SERVER['PHP_SELF'])."',$prevrec,'$action')\"
					name=\"prev\" onMouseOver=\"fChangeImg('prev');\" onMouseOut=\"fRestoreImg('prev');\" title='previous'> ";
	}
	if ($record_count > ($limit_start + $limit))
	{
		$next = "<img height='55%' src=\"../../Images/next_blu.gif\"  onClick=\"pagerAjax('".basename($_SERVER['PHP_SELF'])."',$nextrec,'$action')\"
					name=\"next\" onMouseOver=\"fChangeImg('next');\" onMouseOut=\"fRestoreImg('next');\" title='next'> ";
		$last = "<img height='55%' src=\"../../Images/end_blu.gif\" onClick=\"pagerAjax('".basename($_SERVER['PHP_SELF'])."',$val_last,'$action')\"
					name=\"end\" onMouseOver=\"fChangeImg('end');\" onMouseOut=\"fRestoreImg('end');\" title='last'> ";
	}
	echo "<img src='../../Images/done.gif' id='pageIndicator'>";
	echo $first;
	echo $prev;
	echo " <font color=white>showing $from to $to of &nbsp;$record_count</font> ";
	echo $next;
	echo $last;
}

?>