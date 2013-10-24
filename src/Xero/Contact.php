<?php

namespace Xero;

use OAuth\Common\Consumer\CredentialsInterface;

class Contact extends XeroEndPoint implements XeroObjectInterface {

	//This is used for the RESTful API URL suffix
	var $endPoint = 'Contacts';

	//This used to define the 'relational' key
	// var $key_id = 'InvoiceID';

	// var $key_name = false;


	// var $__query_where_wrappers = array(

	// 	'Contact.ContactID' => array(
	// 		'guid' => array(
	// 			'prefix' => 'Guid("',
	// 			'suffix' => '")',
	// 		),
	// 	),

	// 	'InvoiceID' => array(
	// 		'guid' => array(
	// 			'prefix' => 'Guid("',
	// 			'suffix' => '")',
	// 		),
	// 	),

	// 	'Date' => array(
	// 		array(
	// 			'prefix' => 'DateTime(',
	// 			'suffix' => ')',
	// 		),
	// 	),

	// );

	function __construct($type = null, CredentialsInterface $credentials=null)
	{	

		parent::__construct($type, $credentials);

		// echo pre(CONST::endPoint);

	}


}


?>
