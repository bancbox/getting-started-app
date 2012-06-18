<?php

// Pagination functions
namespace helper;

/**
* Pagination routine, generates page number sequence
* tpl_prefix is for using different pagination blocks at one page
*/
function generate_pagination($base_url, $num_items, $per_page, $start_item, $add_prevnext_text = false, $prefix = '')
{
	// Make sure $per_page is a valid value
	$per_page = ($per_page <= 0) ? 1 : $per_page;
	if ($prefix)
	{
		$prefix = $prefix . '_';
	}
	
	$seperator = '';//<span class="page-sep">,</span>';
	$total_pages = ceil($num_items / $per_page);
	
	if ($total_pages == 1 || !$num_items)
	{
		return false;
	}
	
	$on_page = floor($start_item / $per_page) + 1;
	$url_delim = (strpos($base_url, '?') === false) ? '?' : '&amp;';
	
	$page_string = ($on_page == 1) ? '<strong>1</strong>' : '<a href="' . $base_url . '">1</a>';
	
	//modificarea dimensiunii sirului de pagini (sa incapa mai bine in bara)
	$lungime_sir=3;
	
	if ($total_pages > $lungime_sir)
	{
		$start_cnt = min(max(1, $on_page - $lungime_sir-1), $total_pages - $lungime_sir);
		$end_cnt = max(min($total_pages, $on_page + $lungime_sir-1), $lungime_sir+1);
		
		$page_string .= ($start_cnt > 1) ? ' <span>...</span> ' : $seperator;
		
		for ($i = $start_cnt + 1; $i < $end_cnt; $i++)
		{
			$page_string .= ($i == $on_page) ? '<strong>' . $i . '</strong>' : '<a href="' . $base_url . "{$url_delim}{$prefix}offset=" . (($i - 1) * $per_page) . '">' . $i . '</a>';
			if ($i < $end_cnt - 1)
			{
				$page_string .= $seperator;
			}
		}
		
		$page_string .= ($end_cnt < $total_pages) ? ' <span>...</span> ' : $seperator;
	}
	else
	{
		$page_string .= $seperator;
		
		for ($i = 2; $i < $total_pages; $i++)
		{
			$page_string .= ($i == $on_page) ? '<strong>' . $i . '</strong>' : '<a href="' . $base_url . "{$url_delim}{$prefix}offset=" . (($i - 1) * $per_page) . '">' . $i . '</a>';
			if ($i < $total_pages)
			{
				$page_string .= $seperator;
			}
		}
	}
	
	$page_string .= ($on_page == $total_pages) ? '<strong>' . $total_pages . '</strong>' : '<a href="' . $base_url . "{$url_delim}{$prefix}offset=" . (($total_pages - 1) * $per_page) . '">' . $total_pages . '</a>';
	
	if ($add_prevnext_text)
	{
		if ($on_page != 1)
		{
			$page_string = '<a href="' . $base_url . "{$url_delim}{$prefix}offset=" . (($on_page - 2) * $per_page) . '">Prev</a>&nbsp;&nbsp;' . $page_string;
		}
		
		if ($on_page != $total_pages)
		{
			$page_string .= '&nbsp;&nbsp;<a href="' . $base_url . "{$url_delim}{$prefix}offset=" . ($on_page * $per_page) . '">Next</a>';
		}
	}
	
	return $page_string;
}
