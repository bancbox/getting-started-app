<?php

namespace helper;

/**
 * truncate modifier
 *
 * Type:     modifier<br>
 * Name:     truncate<br>
 * Purpose:  Truncate a string to a certain length if necessary,
 *           optionally splitting in the middle of a word, and
 *           appending the $etc string or inserting $etc into the middle.
 * @author   Monte Ohrt <monte at ohrt dot com>
 * @param string
 * @param integer
 * @param string
 * @param boolean
 * @param boolean
 * @return string
 */
function truncate($string, $length = 80, $etc = '..',
$break_words = true, $middle = false)
{
	if ($length == 0)
	return '';
	
	if (strlen($string) > $length) {
		$length -= min($length, ($etc == '..' ? 1 : strlen($etc)));
		if (!$break_words && !$middle) {
			$string = preg_replace('/\s+?(\S+)?$/', '', mb_substr($string, 0, $length+1));
		}
		if(!$middle) {
			return mb_substr ($string, 0, $length) . $etc;
		} else {
			return mb_substr ($string, 0, $length/2) . $etc . mb_substr($string, -$length/2);
		}
	} else {
		return $string;
	}
}

function html_encode($string)
{
	return htmlentities($string, ENT_COMPAT, 'UTF-8');
}

function xml_encode($string)
{
	return \util\xml_encode($string);
}