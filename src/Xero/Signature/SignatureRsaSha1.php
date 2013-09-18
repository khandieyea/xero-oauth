<?php

namespace Xero\Signature;

use OAuth\OAuth1\Signature\Signature;

class SignatureRsaSha1 extends Signature
{

    protected function hash($data)
    {
      
        $cert = $this->credentials->privateCert->getCert(true);

        //Resource
        $pkID = openssl_pkey_get_private($cert);

       	if(!openssl_sign($data, $signature, $pkID))
       		throw new \Exception('Failed to Sign');

       	openssl_free_key($pkID);

       	return $signature;

    }
}
