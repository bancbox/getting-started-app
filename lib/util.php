<?php

namespace util;
use \conf;

function array_cleanup($arr)
{
	$new_arr = array();
	$is_assoc = is_assoc($arr);
	foreach($arr as $key => $value)
	{
		if (!empty($value))
		{
			if ($is_assoc)
			{
				$new_arr[$key] = $value;
			}
			else
			{
				$new_arr[] = $value;
			}
		}
	}
	return $new_arr;
}

function rem_array($array, $to_find)
{
	if (!is_array($to_find))
	{
		$to_find = array($to_find);
	}
	
	foreach ($array as $key => $value)
	{
		if (in_array($value, $to_find, true))
		{
			unset($array[$key]);
		}
	}
	return $array;
}

function to_array($data)
{
	return is_array($data) ? $data :
	(is_object($data) ? array($data) : (array)$data);
}

function is_assoc($arr)
{
	return array_keys($arr) !== range(0, count($arr) - 1);
}

function is_email($email)
{
	$regex =	'!^'.
				'[_a-zA-Z0-9-]+'.       /* One or more underscore, alphanumeric,
									   or hyphen charactures. */
				'(\.[_a-zA-Z0-9-]+)*'.  /* Followed by zero or more sets consisting
									   of a period and one or more underscore,
									   alphanumeric, or hyphen charactures. */
				'@'.                 /* Followed by an "at" characture. */
				'[_a-zA-Z0-9-]+'.        /* Followed by one or more alphanumeric
									   or hyphen charactures. */
				'(\.[a-zA-Z0-9-]{2,})+'./* Followed by one or more sets consisting
									   of a period and two or more alphanumeric
									   or hyphen charactures. */
				'$!';

	return preg_match($regex, $email);
}

function is_ssn($value)
{
	//Example: 078-05-1120
	return (boolean) preg_match('/^[\d]{3}-[\d]{2}-[\d]{4}$/',$value);
}

function is_phone_number($value)
{
	return (boolean) preg_match('/^[\d]{10}$/', $value);
}

function is_routing_number($value)
{
	// First, remove any non-numeric characters.
	
	$t = "";
	for ($i = 0; $i < strlen($value); $i++)
	{
	//	if (is_numeric($value[$i]))
		{
			$t .= $value[$i];
		}
	}
	
	// Check the length, it should be nine digits.
	
	if (strlen($t) != 9)
	{
		return false;
	}
	
	$n = 0;
	for ($i = 0; $i < strlen($t); $i += 3)
	{
		$n += (int)  $t[$i]      * 3
		   +  (int) ($t[$i + 1]) * 7
		   +  (int) ($t[$i + 2]);
	}
	
	// If the resulting sum is an even multiple of ten (but not zero),
	// the aba routing number is good.
	
	return ($n != 0 && $n % 10 == 0) ? true : false;
}

function is_zipcode($value)
{
	return preg_match('/(^\d{5}$)|(^\d{5}-\d{4}$)/', $value);
}

function is_card_number($value)
{
	return preg_match('/^\d{4}\-?\d{4}\-?\d{4}\-?\d{4}$/', $value);
}

function is_cvv($value)
{
	return preg_match('/^\d{3}$/', $value);
}

function generate_password($length = 8)
{
	$sir = 'abcdefghijklmnopqrstuvwxzABCDEFGHIJKLMNOPQRSTUVWXZ0123456789';
	$parola = '';

	for ($i=0; $i<$length; $i++) $parola .= substr($sir, mt_rand(0, strlen($sir)), 1);

	return $parola;
}

function show_time(&$t, $name = 't')
{
	echo $name.': '.round((microtime(true)-$t)*1000, 4)."ms\n<br />";
	flush();
	$t = microtime(true);
}

function curl_get($url, $timeout = 500, $post = null, $ignore_code = false)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//	curl_setopt($ch, CURLOPT_USERAGENT, '');
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, $timeout);
	if ($post)
	{
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	}
	
	$response = curl_exec($ch);
	$info = curl_getinfo($ch);
//	if (!$response)
//	{
//		throw new \Error(500, curl_error($ch));
//	}
	curl_close ($ch);
	
	if ($info['http_code'] == 200 || $ignore_code)
	{
		return $response;
	}
	else
	{
		return false;
	}
	
}

function tokenize($query, $regexp = '/[a-z0-9\-]+/i')
{
	preg_match_all($regexp, $query, $query_vars);
	
	$clean_query = implode(' ', $query_vars[0]);
	if(strlen($clean_query) && substr($query, -1) == ' ')
	{
		$clean_query .= ' ';
	}
	
	return strtolower($clean_query);
}
