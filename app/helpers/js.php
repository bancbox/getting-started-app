<?php

namespace helper;


/**
 * @package FinallyPHP.helper
 */
function js($url)
{
	echo(get_javascript($url) . "\n");
}

/**
 * @package FinallyPHP.helper
 */
function javascript($url)
{
	echo(get_javascript($url) . "\n");
}

/**
 * @package FinallyPHP.helper
 */
function get_javascript($url)
{
	return '<script type="text/javascript" src="' . $url . '"></script>';
}
