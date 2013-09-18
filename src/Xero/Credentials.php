<?php

namespace Xero;

use \Xero\Consumer\CredentialsCert;

/**
 * Value object for the credentials of an OAuth service.
 */
class Credentials extends \OAuth\Common\Consumer\Credentials 
// implements \OAuth\Common\Consumer\Credentials
{
    /**
     * @var string
     */
    protected $publicKey;

    /**
     * @var string
     */
    protected $privateKey;

    /**
     * @var string
     */
    protected $callbackUrl;

    /**
     * @param string $consumerId
     * @param string $consumerSecret
     * @param string $callbackUrl
     */
    public function __construct($consumerId, $consumerSecret, $privateKey, $publicKey, $callbackUrl='')
    {   

        $this->privateCert  = new CredentialsCert($privateKey);
        $this->publicCert   = new CredentialsCert($publicKey);
        
        parent::__construct($consumerId, $consumerSecret, $callbackUrl);

    }

}
