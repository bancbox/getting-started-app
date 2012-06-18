<?php

namespace helper;


/**
 * @package FinallyPHP.helper
 */
function css($url)
{
	echo(get_stylesheet($url) . "\n");
}

/**
 * @package FinallyPHP.helper
 */
function stylesheet($url)
{
	echo(get_stylesheet($url) . "\n");
}

/**
 * @package FinallyPHP.helper
 */
function get_stylesheet($url)
{
	return '<link rel="stylesheet" type="text/css" href="' . $url . '" />';
}
