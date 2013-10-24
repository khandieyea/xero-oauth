<?php


/**

	You can configure this xeroconfig how ever you like, 
	BUT, it must return an instance of \Xero\Credentails when `generateCredentials(.. is called 
	

**/

namespace Xero;


use OAuth\Common\Consumer\CredentialsInterface;

use OAuth\Common\Http\Client\ClientInterface;

class XeroConfig {

	var $_defaultType = 'private';

	var $_type = null;

	var $consumerKey 	= 'KIP4ZWHIGMRZZRM9S33NCDVXSQSNFT';

	var $consumerSecret = 'TBGIBEBPGIUUTAWZHD0HVIZ3OZH7QG';

	//Define Absolute Path to Cert
	var $privateCert  	= "/application/config/development/xero/certs/privatekey_xero_2013.pem";

	var $publicCert 	= '/path/to/public/cert';


	public function __construct()
	{	

	

	}



	public function _generatePrivateCallBack()
	{
		return array(
			'privateCert' => $this->getPrivateCert(),
			'publicCert'  => $this->publicCert,
		);
	}

	public function getPrivateCert()
	{
		return getcwd().$this->privateCert;
		
	}
	public function getConsumerKey()
	{
		return $this->consumerKey;
	}

	public function getConsumerSecret()
	{
		return $this->consumerSecret;
	}

}


?>
