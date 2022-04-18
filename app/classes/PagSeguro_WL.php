<?php

class PagSeguro {
    
    private $token = '736AFAF9FD8944F29DB2068DA881985F';
    // private $token = '95C7BFB7BA5E4E1CA6A6D0A70752D5B3'; //sandbox
    private $email = 'guigirao@hotmail.com';
    private $currency = 'BRL';
    private $checkout_url = '';
    private $url = 'https://ws.pagseguro.uol.com.br/v2/checkout';
    private $sandbox = false;
    
    public function __construct() {
        if ($this->sandbox) $this->url = 'https://ws.sandbox.pagseguro.uol.com.br/v2/checkout';
    }
    
    public function getCode ($id, $name, $price, $id_ref) {
        $data['token'] = $this->token;
        $data['email'] = $this->email;
        $data['currency'] = $this->currency;
        $data['itemId1'] = $id;
        $data['itemQuantity1'] = '1';
        $data['itemDescription1'] = $name;
        $data['itemAmount1'] = $price;
        $data['reference'] = $id_ref;
        $data['shippingAddressRequired'] = 'false';
        
        // $url = 'https://ws.pagseguro.uol.com.br/v2/checkout';
        $url = 'https://ws.pagseguro.uol.com.br/v2/checkout';
        
        $data = http_build_query($data);
        
        $curl = curl_init($url);
        
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        $xml = curl_exec($curl);
        
        curl_close($curl);
        
        $xml = simplexml_load_string($xml);
        return $xml->code;
    }
}