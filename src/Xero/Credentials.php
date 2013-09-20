<?php

namespace Xero;

use \Xero\Consumer\CredentialsCert;

/**
 * Value object for the credentials of an OAuth service.
 */
class Credentials extends \OAuth\Common\Consumer\Credentials implements \OAuth\Common\Consumer\CredentialsInterface
{
    /**
     * @param string $consumerId
     * @param string $consumerSecret
     * @param string $callbackUrl
     */
    public function __construct($consumerId, $consumerSecret, $callbackUrl='')
    {   

        if(is_array($callbackUrl))
        {
            if(isset($callbackUrl['privateCert']))
               $this->privateCert  = new CredentialsCert($callbackUrl['privateCert']);

            if(isset($callbackUrl['publicCert']))
               $this->publicCert  = new CredentialsCert($callbackUrl['publicCert']);
           
        }

        parent::__construct($consumerId, $consumerSecret, $callbackUrl);

    }

}
