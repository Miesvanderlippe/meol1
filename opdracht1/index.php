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

	public function GetTemperature($unit = 'c'){

		$data = $this->ConditionsRawJSON;
		$data = json_decode($data, true);

		switch(strtolower($unit)){
			case 'c':
				$temperature = $data['current_observation']['temp_c'];
				break;
			case 'f':
				$temperature = $data['current_observation']['temp_f'];
				break;
			default :
				$temperature = $data['current_observation']['temp_c'];
		}

		return $temperature;
	}

	public function GetPerceivedTemperature($unit = 'c'){

		$data = $this->ConditionsRawJSON;
		$data = json_decode($data, true);

		switch(strtolower($unit)){
			case 'c':
				$temperature = $data['current_observation']['feelslike_c'];
				break;
			case 'f':
				$temperature = $data['current_observation']['feelslike_f'];
				break;
			default :
				$temperature = $data['current_observation']['feelslike_c'];
		}

		return $temperature;
	}
	
	public function GetGPSLocation(){
		
		$data = $this->GeoLookupsRawJSON;
		$data = json_decode($data);
		
		$lon = $data->{'location'}->{'lon'};
		$lat = $data->{'location'}->{'lat'};
		
		return ($lat . ', ' . $lon);
	}
	
	public function NearbyStations($radius = 100, $type = 'both'){
		
		$data = $this->GeoLookupsRawJSON;
		$data = json_decode($data, true);
		
		/* Split data to airports and stations */
		$airports = $data['location']['nearby_weather_stations']['airport']['station'];
		$stations = $data['location']['nearby_weather_stations']['pws']['station'];
		
		/* Strip stations that are too far away from city*/
		foreach($stations as $station=>$value)
			if($value['distance_km'] > $radius)
				unset($stations[$station]);
		
		/* Reset array keys*/
		$stations = array_values($stations);
		
		/* Split response in one array*/
		$response['airports'] = $airports;
		$response['stations'] = $stations;
		
		/* Return just one type if user requests it*/
		switch(strtolower($type)){
			case 'both':
				return($response);
				break;
			case 'stations':
				return($response['stations']);
				break;
			case 'airports':
				return($response['airports']);
				break;
			default :
				return($response);
		}
	}
}

$api			 = new Weatherunderground();

$coordinates	 = $api->GetGPSLocation();
$stations		 = $api->NearbyStations(1);
$temperatuurC 	 = $api->GetTemperature('c');
$temperatuurF	 = $api->GetTemperature('f');
$GtemperatuurC 	 = $api->GetPerceivedTemperature('c');
$GtemperatuurF	 = $api->GetPerceivedTemperature('f');

?>
<html>
	<head>
		<title>MEOL1 - Opdracht 1</title>
	</head>
	<body>
		<?php
			print( 'Gevoelstemperatuur' . $GtemperatuurC . '<br/>' );
			print( 'Temperatuur' . $GtemperatuurC . '<br/>' );
		?>
	</body>
</html>