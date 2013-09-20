<?php

namespace Xero;

class ServiceFactory Extends \OAuth\ServiceFactory
{
    public function __construct()
    {

        $this->registerService('xeroprivate', '\Xero\PrivateClient');


    }
}
