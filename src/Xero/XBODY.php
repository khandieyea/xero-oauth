<?php


namespace Xero;

class XBODY {


	static $body = false;

	static $defaultRoot = 'xmldoc';

	static function xmlToString($data, $root=null)
	{

		if(is_null($root))
			$root = static::$defaultRoot;
			
		if(!is_array($data) && !is_object($data))
			return false;

	
		//Lets get to work.
		$body = static::newXML($root);

		static::parseCollectionToXml($data, $body);

		return $body->saveXML();

	}

	private static function newXML($root = null){

		if(is_null($root))
			$root = static::$defaultRoot;

		return new \SimpleXMLElement('<'.$root.'></'.$root.'>', LIBXML_NOXMLDECL);
	}

	static function parseCollectionToXml($data, &$node)
	{

	    foreach($data as $key => $value) {

	        if(is_array($value) || is_object($value)) {
	            if(!is_numeric($key)){
	                $subnode = $node->addChild("$key");
	                static::parseCollectionToXml($value, $subnode);
	            }
	            else{
	                $subnode = $node->addChild("item$key");
	                static::parseCollectionToXml($value, $subnode);
	            }
	        }
	        else {
	            $node->addChild("$key","$value");
	        }
	        
	    }

	}


	static function stringToXml($string)
	{

		try {
			$r = new \SimpleXMLElement($string);
		} catch(\Exception $e)
		{
			$r = false;
		}

		return $r;

	}

}
