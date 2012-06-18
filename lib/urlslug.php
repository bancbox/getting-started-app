<?php

/*!
 * FinallyPHP
 * 
 * @link http://sourceforge.net/projects/finallyphp/
 * @author Ovidiu Chereches <hello@ovidiu.ch>
 * 
 * @copyright Copyright (c) 2010, Ovidiu Chereches
 * @license http://legal.ovidiu.ch/licenses/MIT MIT License
 */

namespace url;


/**
 * URL slug generator.
 *
 * @package FinallyPHP.url
 *
 * @param  string $url URL string
 * @return string      Transformed URL string
 */
function slug($url)
{
	$slug = trim($url);
	
	$replacements = array(
		'a' => array('ă', 'â', 'à', 'á', 'ã', 'ä'),
		'A' => array('Ă', 'Â', 'À', 'Á', 'Ã', 'Ä'),
		'c' => 'ç',
		'C' => 'Ç',
		'e' => array('è', 'é', 'ê', 'ë'),
		'E' => array('È', 'É', 'Ê', 'Ë'),
		'i' => array('î', 'ì', 'í', 'ï'),
		'I' => array('Î', 'Ì', 'Í', 'Ï'),
		'n' => 'ñ',
		'C' => 'Ñ',
		'o' => array('ò', 'ó', 'ô', 'õ', 'ö', 'ø'),
		'O' => array('Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø'),
		's' => array('ş', 'š'),
		'S' => array('Ş', 'Š'),
		't' => 'ţ',
		'T' => 'Ţ',
		'u' => array('ù', 'ú', 'û', 'ü', 'µ'),
		'U' => array('Ù', 'Ú', 'Û', 'Ü'),
		'y' => array('ý', 'ÿ'),
		'Y' => array('Ý', 'Ÿ'),
		'z' => 'ž',
		'Z' => 'Ž',
		
		'th' => 'þ',
		'TH' => 'Þ',
		'dh' => 'ð',
		'DH' => 'Ð',
		'ss' => 'ß',
		'oe' => 'œ',
		'OE' => 'Œ',
		'ae' => 'æ',
		'AE' => 'Æ',
		
		'plus'   => '+',
		'and'    => '&',
		'at'     => '@',
		'equals' => '=',
		'-'      => array('_', ' ', '–', '—'),
	);
	foreach($replacements as $k => $v)
	{
		$slug = str_replace($v, $k, $slug);
	}
	
	$patterns = array('/\n|\r/s', '/-+/', '/[^a-z0-9-]/i');
	$replacements = array('', '-', '');
	$slug = preg_replace($patterns, $replacements, $slug);
	
	return strtolower($slug);
}

function params($p = array())
{
	$r = array();
	
	foreach ($p as $k => $v)
	{
		$r[] = urlencode($k) .'='. urlencode($v);
	}
	
	if ($r)
	{
		return '?' . implode('&', $r);
	}
	
	return null;
}