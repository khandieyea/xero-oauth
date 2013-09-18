<?php

namespace OAuth\Common\Consumer;

/**
 * Value object for the credentials of an OAuth service.
 */
class CredentialsRsaSha1 extends Credentials
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
