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


class PrivateClient extends \OAuth\OAuth1\Service\AbstractService
{
    // var $requestCallbacks = array();

    // var $format = 'data';

    // var $xeroErrorMap = array(
    //     '200' => 'OK',
    //     '400' => 'Bad Request::A validation exception has occured',
    //     '401' => 'Unauthorised::Invalid authorization credentials',
    //     '403' => 'Forbidden::The client SSL certificate was not valid. This indicates the Xero Entrust certificate required for partner API applications is not being supplied in the connection',
    //     '404' => 'Not Found::The resource you have specified cannot be found',
    //     //More to come.
    // );

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

    // function setFormat($format)
    // {
    //     $this->format = $format;

    //     return $this;
    // }

    // function getFormat()
    // {
    //     return $this->format;
    // }

    /*
        Pass through to the http client to set the timeout 
    */
    public function setTimeout($time)
    {
        $this->httpClient->setTimeout($time);
        return $this;
    }

    public function getLastHttpCode()
    {
        return $this->httpClient->last_http_code;
    }

    // protected function getExtraApiHeaders()
    // {
    //     $r = array(
    //       //  'Accept' => 'application/json',
    //     );

    //     if($this->getFormat() === 'json')
    //         $r['Accept'] = 'application/json';

    //     return $r;

    // }

    // public function _isResponseOK($code)
    // {

    //     if($code == 200)
    //         return true;

    //     return false;

    // }
    
    // public function request($path, $method = 'GET', $body = null, array $extraHeaders = array())
    // {
    //     //echo pre(__METHOD__.__LINE__);
        
    //     $response = parent::request($path,$method,$body,$extraHeaders);    

    //     //Lets take a look at the http status code
        
    //     if(($isOK = $this->_isResponseOK($this->httpClient->last_http_code)) !== TRUE)
    //     {

    //         echo pre($response);
    //         echo pre($this->httpClient->last_http_code);

    //         return false;

    //     } 

    //     return new \SimpleXMLElement($response);


    //     if($this->format == 'json')
    //     {
    //         //we're expecting a json response; So, lets take a look.
    //         $response = json_decode($response);

    //         if(json_last_error() !== JSON_ERROR_NONE)
    //         {   

    //             return false;
    //             // $response = new \stdClass;
    //             // $response->Status = $this->xeroErrorMap[$this->httpClient->last_http_code];
    //             // return $response;
    //         }

    //         return $response;
          
    //     }
    //     else
    //         exit('Only JSON OR XML format is currently supported.');

    //     try {
    //         $response = $this->processCallbacks($response);
    //     }
    //     catch (\Exception $e)
    //     {
    //         $response = new \stdClass;
    //         $response->Status = 'FilterError::'.$e->getMessage();
    //     }

    //     // if(!isset($response->Status))
    //     //     $response->Status = $responseStatus;

    //     return $response;

    // }

    // public function formatJson()
    // {
    //     return $this->format('json');
    // }

    // public function format($type='data')
    // {
    //     $this->format = $type;

    //     return $this;
    // }


    /**
     * {@inheritdoc}
     */
    public function getRequestTokenEndpoint()
    {
    //     //echo pre(__METHOD__.__LINE__);
    //     return new Uri($this->baseApiUri . 'oauth/request_token');
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorizationEndpoint()
    {
        //echo pre(__METHOD__.__LINE__);
        return new Uri($this->baseApiUri);
    }

    /**
     * {@inheritdoc}
     */
     public function getAccessTokenEndpoint()
     {
    //     //echo pre(__METHOD__.__LINE__);
    //     return new Uri($this->baseApiUri . 'oauth/access_token');
     }

    /**
     * {@inheritdoc}
     */
     protected function parseRequestTokenResponse($responseBody)
     {

    // {
    //     //echo pre(__METHOD__.__LINE__);
    //     parse_str($responseBody, $data);

    //     if (null === $data || !is_array($data)) {
    //         throw new TokenResponseException('Unable to parse response.');
    //     } elseif (!isset($data['oauth_callback_confirmed']) || $data['oauth_callback_confirmed'] != 'true') {
    //         throw new TokenResponseException('Error in retrieving token.');
    //     }

         return $this->parseAccessTokenResponse($responseBody);
     }

     protected function parseAccessTokenResponse($responseBody)
     {
        return;
     }


    function getSignatureMethod()
    {
        //This actually does nothing - it's just a placeholder to please the signatureinterface
        //echo pre(__METHOD__.__LINE__);
        return 'RSA-SHA1';
    }

    // public function getContact($id)
    // {
    //     $this->registerRequestCallback('parseObjectFromList', array('Contacts' => array('ContactID'=>$id)));
    //     return $this->request('/Contact/'.$id,'GET');
    // }


    // public function getInvoice($id)
    // {
    //      $this->registerRequestCallback('parseObjectFromList', array('Invoices' => array('InvoiceID'=>$id)));
    //     return $this->request('/Invoice/'.$id, 'GET');
    // }

    // public function saveInvoice($id=false, $body=array())
    // {

    //     $method = 'POST';

        
    //     $body = array(
    //         'Invoice' => array(
    //             'Status'=> 'aids',

    //         )
    //     );

    //     if($id !== FALSE)
    //     {
    //         $body['Invoice']['InvoiceID'] = $id;
    //         $method = 'POST';
    //     }

    //     // $body = array('Invoices' => $body);

    //     //echo pre($body);
    //     $body = json_encode($body);
    //     //echo pre($body);

    //     return $this->request('/Invoice', $method, $body);

    // }

    // public function registerRequestCallback($method, $filters = array())
    // {
    //     $this->requestCallbacks[$method] = $filters;
    //     return $this;
    // }

    // public function processCallbacks($data=false)
    // {
    //     foreach($this->requestCallbacks as $method => $filters)
    //         $data = $this->{$method}($data, $filters);
        
    //     return $data;

    // }

    // public function parseObjectFromList($data, $filters=array())
    // {
      
    //     foreach($filters as $x => $matches)
    //     {

    //         if(is_array($matches))
    //         {

    //             if(!isset($data->{$x}))
    //                 throw new \Exception("Failed to find resource {$x} in response");

    //             foreach($data->{$x} as $index => $result)
    //             {   
    //                 foreach($matches as $field => $val)
    //                     if(!isset($result->{$field}) || strtoupper($result->{$field}) !== strtoupper($val))
    //                         continue 2;

    //                 $result->Status = $data->Status;

    //                 return $result;

    //             }

    //             throw new \Exception('Failed to find filtered-resource ['.http_build_query($matches).']');

    //         }

    //     }

    //     return $data;

    // }

    // public function 
}
