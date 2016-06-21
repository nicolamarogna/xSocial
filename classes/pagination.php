<?php 
class Pagination {
	
	public static function paginate($array, $start, $pp = 0)
	{
		$i = ($pp) ? $pp : PP;	// items per page
		//echo $i;die;
		$n = sizeof($array);	// items
		$p = ceil($n/$i);		// pages
		if ($array) {
			return array(array_slice($array, $start*$i, $i), array($p, $start, $n));
		} else {
			return FALSE;
		}
	}
	
	public static function pager($url, $info, $section = 5, $inline = false)
	{
		$what = ($inline) ? 'span' : 'div';
		$link = '<'.$what.' class="xsmall">Trovati '.$info[2].' elementi in '.$info[0].' pagine&nbsp;&nbsp;&nbsp;</'.$what.'>';
		
		// define window
		$w = intval($info[1]/$section);
		
		if ($info[1] > 0) {
			$link .= '<a class="xsmall" href="'.$url.'0" title="'._FIRST_PAGE.'">1</a>';
			$link .= '<a class="xsmall" href="'.$url.($info[1]-1).'" title="'._PREVIOUS.'"><<<</a>';
		}
		
		for($i = $w*$section; $i < min($info[0], ($w+1)*$section); $i++)
		{
			$link .= ($i == $info[1]) ? '<span class="n">'.($i+1).'</span>' : ' [<a class="xsmall" href="'.$url.$i.'" title="'._PAGE.' '.($i+1).'">'.($i+1).'</a>]';
		}
		
		if ($info[1] < ($info[0]-1)) {
			$link .= '<a class="xsmall" href="'.$url.($info[1]+1).'" title="'._NEXT.'">>>></a>';
			$link .= '<a id="lastPage" class="xsmall" href="'.$url.($info[0]-1).'" title="'._LAST_PAGE.'">'.$info[0].'</a>';
		}
		return $link;
	}

}
