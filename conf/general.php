<?php

ini_set('mbstring.internal_encoding', 'UTF-8');

conf::set('DEFAULT_EXTENSION', 'html');
conf::set('DEFAULT_APP', 'default');
conf::set('DEFAULT_CONTROLLER', 'main');
conf::set('DEFAULT_ACTION', 'index');

conf::set('ENVIRONMENT', conf::get('APPLICATION_ENV') == 'live' ? 'production' : 'development');
conf::set('LOG_ERROR', true);

conf::set('HTML_CONTENT_TYPE', 'text/html; charset=utf8');
conf::set('JSON_CONTENT_TYPE', 'application/json');
conf::set('CSV_CONTENT_TYPE', 'text/csv');

conf::set('GOOGLE_ANALYTICS_ID', 'UA-27688476-1');

conf::set('SESSION_DEFAULT_LIFE', 432000);//seconds

switch (conf::get('APPLICATION_ENV'))
{
	case 'live':
		conf::set('DEFAULT_EMAIL_TO', 'contact@bancbox.com');
		conf::set('DEFAULT_EMAIL_FROM', 'no-reply@bancbox.com');
		
		conf::set('SMTP_SERVER', 'smtp.sendgrid.net');
		conf::set('SMTP_SERVER_PORT', 587);
		conf::set('SMTP_SERVER_USERNAME', '');
		conf::set('SMTP_SERVER_PASSWORD', '');
		
		conf::set('BANCBOX_API_WDSL', '');
		conf::set("BANCBOX_API_USERNAME", "");
		conf::set("BANCBOX_API_PASSWORD", "");
		conf::set("BANCBOX_API_SUBSCRIBER_ID", "");
		break;
	case 'loadrunner':
		conf::set('DEFAULT_EMAIL_TO', 'danut@weednet.ro');
		conf::set('DEFAULT_EMAIL_FROM', 'danut@weednet.ro');
		
	//	conf::set('SMTP_SERVER', '192.168.110.6');
		
		conf::set('BANCBOX_API_WDSL', 'https://sandbox-api.bancbox.com/BBXPort?wsdl');
		conf::set("BANCBOX_API_USERNAME", "dan.chereches@okapidev.com");
		conf::set("BANCBOX_API_PASSWORD", "BB-284165");
		conf::set("BANCBOX_API_SUBSCRIBER_ID", "202157");
		break;
	default:
		conf::set('DEFAULT_EMAIL_TO', 'dan.chereches@okapidev.com');
		conf::set('DEFAULT_EMAIL_FROM', 'dan.chereches@okapidev.com');
		
		conf::set('SMTP_SERVER', '192.168.110.6');
		
		conf::set('BANCBOX_API_WDSL', '');
		conf::set("BANCBOX_API_USERNAME", "");
		conf::set("BANCBOX_API_PASSWORD", "");
		conf::set("BANCBOX_API_SUBSCRIBER_ID", "");
		break;
}
