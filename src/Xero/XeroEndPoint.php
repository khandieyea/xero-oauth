<?php

namespace Xero;

use OAuth\Common\Consumer\CredentialsInterface;

if(!defined('XERO_STATUS_OK')) define('XERO_STATUS_OK', 'OK');

abstract class XeroEndPoint implements XeroObjectInterface {

	public $endPoint 	= null;

	static $credentials = null;

	static $apiclient 	= null;

	static $_baseConfig = false;

	static $sf 			= null;

	static $format 		= 'xml';

	var $__query_where 	= array();
	
	var $__query_where_wrappers = array();

	var $errors 		= false;

	protected static $keyIDs = array();

	protected static $keyNames = array();

	protected static $mappedHttpErrorCodes = array(

		'200' => 'OK',
		'404' => 'Resource Not Found',

	);

	function __construct($type = null, CredentialsInterface $credentials=null)
	{

		$this 	->setKeyName()
				->setKeyID()
				->reset();
				
		//Lets get our xeroconfig
		$this->_xeroConfig = new \Xero\XeroConfig();

		//Allow manually passing a credentials object
		if(!is_null($credentials))
			$type = $credentials;
		
		$this->setUpConnections($type, TRUE);

		return $this;

	}

	function reset()
	{

		//Reset the errors array
		$this->errors = $this->_buildErrorClass();

		unset($this->{$this->getKeyID()});

		return $this;

	}

	function _buildErrorClass()
	{

		$e = new \stdClass;
		$e->all = array();
		$e->string = '';

		return $e;

	}

	function error($name, $body)
	{

		$this->errors->{$name} = $body;
		$this->errors->all[$name] = $body;

		return $this;

	}

	/*
		Set the key name identifier
		This is used internally to determine endpoint names, and response key parsing
	*/
	private function setKeyName()
	{

		if(!isset(static::$keyNames[get_class($this)]))
			static::$keyNames[get_class($this)] = $this->get_real_class($this);

		return $this;

	}

	/*
		Get the key name identifier
	*/
	public function getKeyName()
	{

		if(isset(static::$keyNames[get_class($this)]))
			return static::$keyNames[get_class($this)];

		throw new \Exception('Failed to find keyName. Cannot operator without it.');

		return $this;

	}

	/* 
		Set the key ID
		This is the ID of each item returned to act as the 'id' or FK
	*/
	private function setKeyID()
	{

		if(!isset(static::$keyIDs[get_class($this)]))
			static::$keyIDs[get_class($this)] = $this->getKeyName().'ID';

		return $this;

	}

	/*
		Get the name of the key ID
	*/
	public function getKeyID()
	{

		if(isset(static::$keyIDs[get_class($this)]))
			return static::$keyIDs[get_class($this)];

		throw new \Exception('Failed to find keyName. Cannot operator without it.');

		return $this;

	}


	/* 
		Setup the connects to the oAuth wrappers
	*/
	public function setUpConnections($type = null, $soft=false)
	{

		if(is_null(static::$credentials) || $soft === FALSE)
			static::$credentials = $this->__createCredentials($type);

		//If the api client is empty, lets create one based on the xeroConfig
		if(is_null(static::$apiclient) || $soft === FALSE)
			static::$apiclient = $this->__createOAuthClass(static::$credentials);

		return $this;

	}

	/**
		returns the real class name, without namespacing
	*/
	function get_real_class()
	{

	    $classname = get_class($this);

	    if (preg_match('@\\\\([\w]+)$@', $classname, $matches)) {
	        $classname = $matches[1];
	    }

	    return $classname;
	}

	/**
		Returns the API uri Endpoint name
		<url>/<endPoint>/<queryString>
		api.com/users/44?where(active=1)
	**/
	function getEndPoint()
	{	

		return $this->endPoint;

	}

	/* Sets the API uri EndPoint name */
	function setEndPoint()
	{

		//Dont really want to be doing this
		return $this;

	}

	/**
		Add's to the where array
	**/
	public function where($field=false, $data=false)
	{

		if(!is_array($field))
			$field = array($field => $data);

		foreach($field as $f => $d)
			if($field !== $this->getKeyID())
				$this->__query_where[trim($f)] = trim($d);
		
		return $this;

	}

	//TOFIX
	function get($id=null)
	{

		$uri = $this->buildUri($id);

		$r = $this->__exec($uri, 'GET');

		return $r;

	}

	/**
		Build the end point URI
	**/
	public function buildUri($epsuffix=null)
	{

		//Init array and add end point
		$uri = array($this->getEndPoint());

		//add ep suffix if it's set
		if(!is_null($epsuffix))
			$uri[] = $epsuffix;

		//Implode uri, and append query
		return implode('/', $uri).'?'.http_build_query($this->buildUriQuery());

	}


	//TOFIX
	function save()
	{

		//returnrns booleon

		//Let's post the update, OR, put for a new one
		$method = $this->exists() ? 'POST' : 'PUT';


		$finalEndPoint = $this->getEndPoint();


		if($method == 'POST')
			$finalEndPoint .= '/'.$this->{$this->key_id};


		$r = $this->__exec($finalEndPoint, $method, TRUE);


		return ($r === FALSE ? FALSE : TRUE);

	}

	/*
		Some API's require silly syntax around their filter arguments
		
			query_where_wrappers lets you prefix and suffix the values to help make things easier

			Contact = GUID("55");

			->where('Contact', 'GUID("55"');

			When query_where_wrappers are used, we can simply do

			->where('Contact', '55');

			And the wrappers will add 'GUID("' on the front, and '")' on the end of the value.
	*/

	function parse_query_where_wrappers($data, $field)
	{

		if(!isset($this->__query_where_wrappers[$field]))
			return $data;

		foreach($this->__query_where_wrappers[$field] as $name => $fixes)
			$data = $fixes['prefix'].$data.$fixes['suffix'];
		
		return $data;

	}

	/**
		This needs to be turned into a buildURI
		And take into account the endpoint, ID, and where/order paramaters
	**/
	function buildUriQuery()
	{

		$data = array();

		if(count($this->__query_where) > 0)
		{
			
			$data['where'] = array();
			// $first = true;
			foreach($this->__query_where as $x => $a)
			{

				if(isset($operator))
				{
					//Room for other things like OR
					$data['where'][] = ' AND '; 
				}
				else
					$operator = true;

				if(($pos = strpos($x, ' ')) !== FALSE)
				{
					list($x, $sep) = explode(' ', $x);
				}
				else
					$sep = '==';

				$data['where'][] = $x.' '.$sep.' '.$this->parse_query_where_wrappers($a,$x);

			}
		}

		if(count($data) == 0)
			return false;

		foreach($data as $type => $d)
			$data[$type] = implode('', $d);
		
		return $data;

	}


	/** 
		Internal method to fire the API Request 
	**/
	private function __exec( $ep, $method='GET', $buildBody = false )
	{

		$body = null;

		if($buildBody)
			$body = $this->_buildBody();

		// static::$apiclient->setTimeout(45);

		// $response = static::$apiclient->format(static::$format)->request($fep, $method, $body);

		$response = static::$apiclient->request($ep, $method, $body);

		if($response === FALSE || is_null($response) || empty($response))
			return $this->error('no response','the response returned nothing..');
		else if(static::$apiclient->getLastHttpCode() != 200)
			return $this->error($this->mappedHttpErrorCode(static::$apiclient->getLastHttpCode()), $response);
		else if(!$this->parseResponse($response))
			return $this->error('aprResponseParseError','YES REALLY');

		//_parseInResponseObject
		return $this;

	}

	/**
		Method to help prepare the object for sending
			Takes all object properties that are parts of the fields property,
			and builds a new object to be converted into the required format.
		TOFIX
	**/
	private function _buildBody()
	{

		$data = new \stdClass();

		$data->{$this->key_name} = new \stdClass();

		foreach($this->fields as $f)
			$data->{$this->key_name}->{$f} = $this->{$f};

		if(static::$format == 'json')
			$data = json_encode($data);

		if(static::$format=='xml')
			$data = XBODY::toXml($data, $this->endPoint);

		return $data;

	}

	function mappedHttpErrorCode($code)
	{

		if(!isset(static::$mappedHttpErrorCodes[$code]))
			return '0::Unknown HTTP Code';

		return $code.'::'.static::$mappedHttpErrorCodes[$code];

	}

	/**
		Parse a raw response into the configured format
	**/
	private function parseResponseFormat($raw='')
	{

		$r = false;

		switch(static::$format)
		{
			case 'json':
				$x = json_decode($raw);
				$r = (json_last_error() === JSON_ERROR_NONE ? $x : $this->error('Failed decoding JSON', json_last_error())->returnFalse());
				break;
			case 'xml':
				$r = XBODY::stringToXml($raw);
				break;
		}

		return $r;

	}

	//TOFIX
	private function parseResponse($raw='')
	{

		$response = $this->parseResponseFormat($raw);

		if(!is_object($response) || !isset($response->Status) || $response->Status != XERO_STATUS_OK )
			return $this->error('Invalid or missing status','Status not equal to XERO_STATUS_OK');

		$result = $response->xpath('/'.$response->getName().'/'.$this->getEndPoint().'/'.$this->getKeyName());

		if(count($result) > 0)
		{

			$this->_spawn_object($this, $result[0]);

			$this->all[0] = $this->get_clone();

			if(count($result) > 1)
			{

				$first = TRUE;
				$model = get_class($this);

				foreach($result as $index => $object)
				{	

					if($first)
					{
						$first = FALSE;
						continue;
					}

					$item = new $model;

					$this->_spawn_object($item, $object);

					$this->all[$index] = $item;

				}

			}

		}

		return $this;
	}

	/**
		Interal METHOD
		Takes the XMLObjects from API responses, and parses them into a instance object and populates the properties
	**/
	function _spawn_object($item, $data)
	{

		foreach($data as $k => $v)
		{	
			$item->fields[] = (string) $k;
			$item->{$k} = (string) $v;
		}

		$item->_refresh_stored_values();

		return $item;

	}

	/** 
		Stores the values of each object on instantiation
		This lets other methods determine if any data has changed.
		Helps save API calls.
	**/
	protected function _refresh_stored_values()
	{

		// Update stored values
		foreach ($this->fields as $field)
		{
			$this->stored->{$field} = $this->{$field};
		}

	}

	/**
		Test if the particular object exists or not
	**/
	function exists() 
	{

		return isset($this->{$this->getKeyID()});

	}

	/**
		Clone this object
		Used mostly when populating result sets
	**/
	public function get_clone()
	{
		return clone($this);
	}



	/*************
		INIT METHODS - these are kinda retarded
		***************/


	private function __createCredentials($type=null)
	{

		if($type instanceof CredentialsInterface)
			return $type;

		if(is_null($type))
		{

			if(is_null($this->_xeroConfig->_defaultType))
				$type = 'public';
			else
				$type = $this->_xeroConfig->_defaultType;
			
		}

		$this->__applicationType = $type;

		$callback = ($type == 'private' ? $this->_xeroConfig->_generatePrivateCallBack() : '?callback=');
		
		return new Credentials(
			$this->_xeroConfig->getConsumerKey(),
			$this->_xeroConfig->getConsumerSecret(),
			$callback
		);
	}

	private function __createOAuthClass(CredentialsInterface $c)
	{

		// return $this->type;
		switch($this->__applicationType)
		{
			case 'private':
				return $this->__serviceFactory()
						->setHttpClient(new \OAuth\Common\Http\Client\CurlClient())
						->createService('xeroprivate', $c, new \OAuth\Common\Storage\Memory());
				break;
			default:
				throw new \Exception('OMG');

		}

		return;

	}

	private function __serviceFactory()
	{

		if(is_null(static::$sf))
			static::$sf = new \Xero\ServiceFactory();

		static::$sf->registerService('xeroprivate', '\Xero\PrivateClient');

		return static::$sf;

	}

}

?>
