<?php

namespace helper;


/**
 * @package FinallyPHP.helper
 */
function s($string, $charset = 'UTF-8')
{
	$string = htmlspecialchars($string);
	$string = iconv('UTF-8', "$charset//IGNORE", $string);
	
	return $string;
}