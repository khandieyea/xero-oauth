<?php

namespace Xero;

use Xero\Signature\SignatureRsaSha1;
use OAuth\OAuth1\Signature\SignatureInterface;

use OAuth\OAuth1\Token\StdOAuth1Token;
use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\Common\Http\Uri\Uri;

use OAuth\Common\Consumer\CredentialsInterface;
use Xero\Credentials;

use OAuth\Common\Http\Uri\UriInterface;
use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\Common\Http\Client\ClientInterface;


class PrivateClient  extends \OAuth\OAuth1\Service\AbstractService
{

    public function __construct(CredentialsInterface $credentials, ClientInterface $httpClient, TokenStorageInterface $storage, SignatureInterface $signature, UriInterface $baseApiUri = null)
    {
    	
        //This is a bit of a hack; but Not really sure what else todo.
        $signature = new SignatureRsaSha1($credentials);

        parent::__construct($credentials, $httpClient, $storage, $signature, $baseApiUri);

        if (null === $baseApiUri) {
            $this->baseApiUri = new Uri('https://api.xero.com/api.xro/2.0/');
        }

        //Hack like a shit cunt.
        $a = new \OAuth\OAuth1\Token\StdOAuth1Token();
        $a->setAccessToken($this->credentials->getConsumerId());
        $a->setAccessTokenSecret($this->credentials->getConsumerSecret());
        $this->storage->storeAccessToken($this->service(), $a);

    }


    protected function getExtraApiHeaders()
    {
        return array(
            'Accept' => 'application/json',
        );
    }
    
    public function request($path, $method = 'GET', $body = null, array $extraHeaders = array())
    {
        echo pre(__METHOD__.__LINE__);
        
        return parent::request($path,$method,$body,$extraHeaders);            
    }


    /**
     * {@inheritdoc}
     */
    public function getRequestTokenEndpoint()
    {
        echo pre(__METHOD__.__LINE__);
        return new Uri($this->baseApiUri . 'oauth/request_token');
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorizationEndpoint()
    {
        echo pre(__METHOD__.__LINE__);
        return new Uri($this->baseApiUri);
    }

    /**
     * {@inheritdoc}
     */
    public function getAccessTokenEndpoint()
    {
        echo pre(__METHOD__.__LINE__);
        return new Uri($this->baseApiUri . 'oauth/access_token');
    }

    /**
     * {@inheritdoc}
     */
    protected function parseRequestTokenResponse($responseBody)
    {
        echo pre(__METHOD__.__LINE__);
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
        echo pre(__METHOD__.__LINE__);
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
        echo pre(__METHOD__.__LINE__);
        return 'RSA-SHA1';
    }
}
