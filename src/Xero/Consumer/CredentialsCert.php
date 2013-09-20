<?php

namespace Xero\Consumer;

/**
 * Value object for the credentials of an OAuth service.
 */
class CredentialsCert 
{

    protected $path;


    protected $cacheKey = false;

    protected $cert = false;

  
    public function __construct($path='')
    {   

        $this->path = $path;
        
    }

    public function setPath($path='')
    {
        $this->path = $path;

        return $this;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getCert($cache=false)
    {

        if($cache === true && $this->cert !== false && $this->cacheKey === $this->path)
                return $this->cert;
        

        $this->cacheKey = $this->path;
        echo pre($this->path);
        if(!is_readable($this->path))
            throw new \exception('SSL Cert not found - '.$this->path);

        $cert = file_get_contents($this->path);

        if($cache === TRUE)
            $this->cert = $cert;


        return $this->cert;

    }



}
