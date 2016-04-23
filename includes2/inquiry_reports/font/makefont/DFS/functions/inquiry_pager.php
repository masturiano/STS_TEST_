<?

function fPageLinks($limit_start = 0, $record_count = 0, $limit =0, $action="")
{
	$search_selection = $_GET['search_selection'] ;
	$txtSearch = $_GET['txtSearch'] ;
	$cmbSearch = $_GET['cmbSearch'] ;
	$action2 = $_GET['action2'] ;
	$lastrec = $limit_start;
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
		$val_last = $record_count;
		for ($i = 0; $i < $record_count; $i++)
		{
			$nRemain = $record_count%$limit;
			if ($record_count > $limit)
			{
				$val_last = $record_count - $nRemain;
			}
		}
 
		
	$prev = "<img height='55%' src=../Images/prev_blu.gif title='previous'> ";
	$next = "<img height='55%' src=../Images/next_blu.gif title='next'> ";
	$first = "<img height='55%' src=../Images/start_blu.gif title='first'> ";
	$last = "<img height='55%' src=../Images/end_blu.gif title='last'> ";
	if ($limit_start > 0)
	{
		$first = "<img height='55%' src='../Images/start_blu.gif' onClick=\"location='$PHP_SELF?limit_start=0$action&search_selection=$search_selection&txtSearch=$txtSearch&cmbSearch=$cmbSearch&action2=$action2'\"
					name=\"start\" onMouseOver=\"fChangeImg('start');\" onMouseOut=\"fRestoreImg('start');\" title='first'> ";
		$prev = "<img height='55%' src=\"../Images/prev_blu.gif\" onClick=\"location='$PHP_SELF?limit_start=$prevrec$action&search_selection=$search_selection&txtSearch=$txtSearch&cmbSearch=$cmbSearch&action2=$action2'\"
					name=\"prev\" onMouseOver=\"fChangeImg('prev');\" onMouseOut=\"fRestoreImg('prev');\" title='previous'> ";
	}
	if ($record_count > ($limit_start + $limit))
	{
		$next = "<img height='55%' src=\"../Images/next_blu.gif\" onClick=\"location='$PHP_SELF?limit_start=$nextrec$action&search_selection=$search_selection&txtSearch=$txtSearch&cmbSearch=$cmbSearch&action2=$action2'\"
					name=\"next\" onMouseOver=\"fChangeImg('next');\" onMouseOut=\"fRestoreImg('next');\" title='next'> ";
		$last = "<img height='55%' src=\"../Images/end_blu.gif\" onClick=\"location='$PHP_SELF?limit_start=$val_last$action&search_selection=$search_selection&txtSearch=$txtSearch&cmbSearch=$cmbSearch&action2=$action2'\"
					name=\"end\" onMouseOver=\"fChangeImg('end');\" onMouseOut=\"fRestoreImg('end');\" title='last'> ";
	}
		
	echo $first;
	echo $prev;
	echo " <font color=black>showing $from to $to of &nbsp;$record_count</font> ";
	echo $next;
	echo $last;
}

?>