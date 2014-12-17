<?php

class Crypt {
	
	public static function RandomString($length = 16){

		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $charactersLength = strlen($characters);
	    
	    $randomString = '';
	    
	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $characters[rand(0, $charactersLength - 1)];
	    }
	    
	    return $randomString;
	}

	public static function MakeHash($key, $hashValues){

		$hashValues  = json_encode($hashValues);
		$hash 		 = hash_hmac('sha1', $data, $key);

		return $hash;
	}

	public static function CheckHash($hash, $key, $hashValues){

		$control = self::MakeHash($key, $hashValues);

		return $hash == $control;
	}
}