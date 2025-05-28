<?php

use Bitrix\Main\Web\HttpClient;

class ApiHandler
{
    protected $httpClient;
    protected $domain;
    protected $login;
    protected $password;

    public function __construct()
    {
        $this->domain = DOMAIN_PORTAL; 
        $this->login = LOGIN_PORTAL;
        $this->password = PASSWORD_PORTAL;
        
        $this->initializeHttpClient();
    }

    protected function initializeHttpClient()
    {
        $this->httpClient = new HttpClient();
        $this->httpClient->setTimeout(30);
        $this->httpClient->setStreamTimeout(60);
        $this->httpClient->setHeader('Content-Type', 'application/json', true);
        $this->httpClient->setAuthorization($this->login, $this->password);
    }

    protected function sendRequest(string $method, string $endpoint, array $data = [])
    {
        $url = 'https://' . $this->domain . $endpoint;
        $response = $this->httpClient->$method($url, json_encode($data));
        
        if ($this->httpClient->getStatus() !== 200) {
            throw new Exception('API request failed with status: ' . $this->httpClient->getStatus());
        }
        
        return json_decode($response, true);
    }
}