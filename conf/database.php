<?php

switch (conf::get('APPLICATION_ENV'))
{
	case 'live':
		conf::set('MYSQL_SERVER', 'localhost');
		conf::set('MYSQL_USERNAME', 'root');
		conf::set('MYSQL_PASSWORD', '');
		conf::set('MYSQL_DATABASE', '');
		break;
	case 'loadrunner':
		conf::set('MYSQL_SERVER', '192.168.0.1:3306');
		conf::set('MYSQL_USERNAME', 'mysql');
		conf::set('MYSQL_PASSWORD', 'abcd1234');
		conf::set('MYSQL_DATABASE', 'okapi.bancbox');
		break;
	default:
		conf::set('MYSQL_SERVER', '127.0.0.1');
		conf::set('MYSQL_USERNAME', '');
		conf::set('MYSQL_PASSWORD', '');
		conf::set('MYSQL_DATABASE', '');
}
