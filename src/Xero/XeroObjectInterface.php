<?php

namespace Xero;

use OAuth\Common\Consumer\CredentialsInterface;

interface XeroObjectInterface {
	
	// const endPoint = null;

	function __construct($type, CredentialsInterface $credentials);

	function getEndPoint();

}


?>
