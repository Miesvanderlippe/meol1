<?php

require_once('/classes/crypt.php');

class APIInteraction {

    static $publicKey     = 'v01ijeGabLkORWsm';
    static $privateKey    = 'xB0mX6tWkiNGmutGiyswPPdjb9JD5SsBLQnMUoWhLn8H27q7WtL8dNDQ3V0Qccld';
    static $apiUrl        = 'localhost:8080/opdracht5/';

    private function __construct(){} //Going static yo!

    private static function CurlGet($url){
        
        $curl    = curl_init();
        
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        
        $data    = curl_exec($curl);
        
        curl_close($curl);
        
        return($data);
    }

    private static function URLSuffix($variables){

        $time        = time(); //Just in case hashing takes more than a second.

        $variables['timestamp'] = strval($time);

        $hash        = Crypt::MakeHash(self::$privateKey, $variables);

        $URLSuffix   = '/'.self::$publicKey .'/'. $time .'/'. $hash;

        return $URLSuffix;
    }

    public static function GetAnimals(){

        $vars = array();

        $url = self::$apiUrl . 'dieren' . self::URLSuffix($vars);

        $data = self::CurlGet($url);

        return $data;
    }
}