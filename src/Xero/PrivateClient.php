<?php

namespace Xero;

 use OAuth\OAuth1\Signature\SignatureInterface;
use OAuth\OAuth1\Signature\SignatureRsaSha1;

use OAuth\OAuth1\Token\StdOAuth1Token;
use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\Common\Http\Uri\Uri;

use Xero\Credentials;

use OAuth\Common\Http\Uri\UriInterface;
use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\Common\Http\Client\ClientInterface;


class PrivateClient  extends \OAuth\OAuth1\Service\AbstractService{

    public function __construct(Credentials $credentials, ClientInterface $httpClient, TokenStorageInterface $storage, SignatureInterface $signature, UriInterface $baseApiUri = null)
    {

    	var_dump(get_class($credentials) .' - '. implode(',', class_implements($credentials)));
    	var_dump(get_class($httpClient) .' - '. implode(',', class_implements($httpClient)));
    	var_dump(get_class($storage) .' - '. implode(',', class_implements($storage)));
    	var_dump(get_class($signature) .' - '. implode(',', class_implements($signature)));

        //This is a bit of a hack; but Not really sure what else todo.
        //$signature = new SignatureRsaSha1($credentials);


       parent::__construct($credentials, $httpClient, $storage, $signature, $baseApiUri);

        if (null === $baseApiUri) {
            $this->baseApiUri = new Uri('https://api.xero.com/api.xro/2.0/');
        }

        $a = new \OAuth\OAuth1\Token\StdOAuth1Token();
        
        $a->setAccessToken('KIP4ZWHIGMRZZRM9S33NCDVXSQSNFT');
        $a->setAccessTokenSecret('TBGIBEBPGIUUTAWZHD0HVIZ3OZH7QG');
        
        $this->storage->storeAccessToken($this->service(), $a);

    }

    /**
     * {@inheritdoc}
     */
    public function getRequestTokenEndpoint()
    {
        return new Uri($this->baseApiUri . 'oauth/request_token');
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorizationEndpoint()
    {
        return new Uri($this->baseApiUri);
    }

    /**
     * {@inheritdoc}
     */
    public function getAccessTokenEndpoint()
    {
        return new Uri($this->baseApiUri . 'oauth/access_token');
    }

    /**
     * {@inheritdoc}
     */
    protected function parseRequestTokenResponse($responseBody)
    {
        parse_str($responseBody, $data);

        if (null === $data || !is_array($data)) {
            throw new TokenResponseException('Unable to parse response.');
        } elseif (!isset($data['oauth_callback_confirmed']) || $data['oauth_callback_confirmed'] != 'true') {
            throw new TokenResponseException('Error in retrieving token.');
        }

        return $this->parseAccessTokenResponse($responseBody);
    }

    /**
     * {@inheritdoc}
     */
    protected function parseAccessTokenResponse($responseBody)
    {
        parse_str($responseBody, $data);

        if (null === $data || !is_array($data)) {
            throw new TokenResponseException('Unable to parse response.');
        } elseif (isset($data['error'])) {
            throw new TokenResponseException('Error in retrieving token: "' . $data['error'] . '"');
        }

        $token = new StdOAuth1Token();

        $token->setRequestToken($data['oauth_token']);
        $token->setRequestTokenSecret($data['oauth_token_secret']);
        $token->setAccessToken($data['oauth_token']);
        $token->setAccessTokenSecret($data['oauth_token_secret']);

        $token->setEndOfLife(StdOAuth1Token::EOL_NEVER_EXPIRES);
        unset($data['oauth_token'], $data['oauth_token_secret']);
        $token->setExtraParams($data);

        return $token;
    }


    function getSignatureMethod()
    {
        //This actually does nothing - it's just a placeholder to please the signatureinterface
        return 'RSA-SHA1';
    }
}
