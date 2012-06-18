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

/**
 * `Required parameter` constant alias.
 *
 * @package FinallyPHP.param
 */
define('REQUIRED_PARAM', 'finallyphp_required_param');

/**
 * Function helper to simulate associative parameters.
 *
 * A p() call must be made within a function body; it uses
 * two parameters.
 *
 * The former is an associative array with user data, usually
 * filled with that function's own (and only) parameter.
 *
 * The latter is also an associative array with all supported
 * param names along with default values.
 * 
 * The first array is referenced and will be automatically
 * populated with default values for omitted parameters.
 *
 * Mark a default value as REQUIRED_PARAM to no longer be
 * optional. An Error will then be thrown when omitted.
 *
 * @package FinallyPHP.param
 *
 * @param array $params   Referenced param input
 * @param array $defaults Param list with default values
 */
function p(array &$params = null, array $defaults)
{
	foreach($defaults as $i => $v)
	{
		if(!array_key_exists($i, (array)$params))
		{
			if($v === REQUIRED_PARAM)
			{
				throw new Error(500, 'Required parameter "' . $i . '"');
			}
			else
			{
				$params[$i] = $v;
			}
		}
	}
}
