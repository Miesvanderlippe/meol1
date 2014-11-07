<?php

class Weatherunderground{
	
	/* API stuff*/
	protected $apikey = "b20a23e4857a0c68";
	protected $apiurl = "http://api.wunderground.com/api/";
	
	/* Variables */
	public $ConditionsRawJSON;
	public $GeoLookupsRawJSON;
	
	public function __construct($country ='France', $city='Paris'){
		
		/* Conditions */
		$requestURL = $this->apiurl . $this->apikey . '/conditions/q/' . $country . '/' . $city . '.json';
		$data = $this->CurlGet($requestURL);
		
		$this->ConditionsRawJSON = $data;
		
		/* Geolookup*/
		$requestURL = $this->apiurl . $this->apikey . '/geolookup/q/' . $country . '/' . $city . '.json';
		$data = $this->CurlGet($requestURL);
		
		$this->GeoLookupsRawJSON = $data;
	}
	
	private function CurlGet($url){
		
		$curl	 = curl_init();
		
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		
		$data	 = curl_exec($curl);
		
		curl_close($curl);
		
		return($data);
	}
	
	public function GetGPSLocation(){
		
		$data = $this->GeoLookupsRawJSON;
		$data = json_decode($data);
		
		$lon = $data->{'location'}->{'lon'};
		$lat = $data->{'location'}->{'lat'};
		
		return ($lat . ', ' . $lon);
	}
}

$api = new Weatherunderground();
$api->GetGPSLocation();

?>
<html>
	<head>
		<title>MEOL1 - Opdracht 1</title>
	</head>
	
	<body>
	</body>
</html>