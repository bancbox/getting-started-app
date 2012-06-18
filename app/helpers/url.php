<?php

namespace helper;


/**
 * @package FinallyPHP.helper
 */
function url($action = null, $controller = null, $app = null, $extension = null, $extra_query = null, $force_secured = false)
{
	return \url\internal($action, $controller, $app, $extension, $extra_query, $force_secured);
}

/**
 * @package FinallyPHP.helper
 */
function e_url($action = null, $controller = null, $app = null, $extension = null, $extra_query = null)
{
	return \url\external($action, $controller, $app, $extension, $extra_query);
}

function url_add_params($url, array $new_params, $remove_empty = true)
{
	$URL = parse_url($url);
	
	if (isset($URL['query']))
	{
		parse_str($URL['query'], $current_params);
		$params = array_merge($current_params, $new_params);
	}
	else
	{
		$params = $new_params;
	}
	
	$p = array();
	foreach ($params as $k => $v)
	{
		if (!empty($v) || !$remove_empty)
		{
			$p[] = "$k=$v";
		}
	}
	
	return $URL['scheme'] . '://' . $URL['host'] . $URL['path'] . '?' . implode('&', $p);
}