<?php

class MySoapClient extends SoapClient
{
	public function __doRequest($request, $location, $action, $version, $one_way = 0)
	{
		$request = str_replace("SOAP-ENV", "soapenv", $request);
		
		$xml = new DOMDocument('1.0');
		$xml->loadXML($request);
		
		$header = $xml->createElement("soapenv:Header");
		
		$envelope = $xml->childNodes->item(0);
		$body = $envelope->childNodes->item(0);
		
		$envelope->insertBefore($header, $body);
		
		$security = $xml->createElement("wsse:Security");
		$security->setAttribute("soapenv:mustUnderstand", "1");
		$security->setAttribute("xmlns:wsse", "http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd");
		$header->appendChild($security);
		
		$userToken = $xml->createElement("wsse:UsernameToken");
		$userToken->setAttribute("wsu:Id", "XWSSGID-1261544568770-474929336");
		$userToken->setAttribute("xmlns:wsu", "http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd");
		$security->appendChild($userToken);
		
		$username = $xml->createElement("wsse:Username");
		$username->appendChild($xml->createTextNode(conf::get("BANCBOX_API_USERNAME")));
		$userToken->appendChild($username);
		
		$password = $xml->createElement("wsse:Password");
		$password->setAttribute("Type", "http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText");
		$password->appendChild($xml->createTextNode(conf::get("BANCBOX_API_PASSWORD")));
		$userToken->appendChild($password);
		
		$request = $xml->saveXML();
		return parent::__doRequest($request, $location, $action, $version, $one_way);
	}
}