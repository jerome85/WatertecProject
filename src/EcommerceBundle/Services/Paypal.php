<?php

namespace EcommerceBundle\Services;

class Paypal
{
    private $pwd;
    private $signature;
    private $user;
    private $endpoint;
    public $errors = array();
    
    public function __construct($params = array()){
        
        if($params['pwd']){
            $this->pwd = $params['pwd'];
        }
        
        if($params['signature']){
            $this->signature = $params['signature'];
        }
        
        if($params['user']){
            $this->user = $params['user'];
        }
        
        if($params['endpoint']){
            $this->endpoint = $params['endpoint'];
        }
    }
    
    public function request($method, $params)
    {
        $params = array_merge($params, array(
                    'METHOD' => $method,
                    'VERSION' => '204.0',
                    'USER' => $this->user,
                    'SIGNATURE' => $this->signature,
                    'PWD' => $this->pwd
                ));
        
        $params = http_build_query($params);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->endpoint,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $params,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_VERBOSE => 1
        ));
        
        $response = curl_exec($curl);
        $responseArray = array();
        parse_str($response, $responseArray);
        
        if(curl_errno($curl))
        {
            $this->errors = $curl_error($curl);
            curl_close($curl);
            return false;
        }
        else
        {
            if($responseArray['ACK'] == 'Success'){
                curl_close($curl);
                return $responseArray;
            } else {
                $this->errors = $responseArray;
                curl_close($curl);
                return false;
            }
        }
    }
}
