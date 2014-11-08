<?php

class Weatherunderground{
	
	/* API stuff*/
	protected $apikey = "b20a23e4857a0c68";
	protected $apiurl = "http://api.wunderground.com/api/";
	
	/* Variables */
	public $ConditionsRawJSON;
	public $GeoLookupsRawJSON;
	public $status;
	
	public function __construct($country ='France', $city='Paris'){
		
		/* Conditions */
		$requestURL = $this->apiurl . $this->apikey . '/conditions/q/' . urlencode($country) . '/' . urlencode($city) . '.json';
		$data = $this->CurlGet($requestURL);

		$this->ConditionsRawJSON = $data;
		
		/* Geolookup*/
		$requestURL = $this->apiurl . $this->apikey . '/geolookup/q/' . urlencode($country) . '/' . urlencode($city) . '.json';
		$data = $this->CurlGet($requestURL);
		
		$this->GeoLookupsRawJSON = $data;

		/* Request status */
		$this->status = true;

		$geolookupdata = json_decode($this->GeoLookupsRawJSON, true);
		$geolookupstatus = (isset($geolookupdata['response']['error']) ? false : true);

		$conditionsdata = json_decode($this->GeoLookupsRawJSON, true);
		$conditionsstatus = (isset($geolookupdata['response']['error']) ? false : true);

		if(!$conditionsstatus || !$geolookupstatus)
			$this->status = false;
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

	public function GetWindspeed($unit = 'kmh'){

		if(!$this->status){
			trigger_error("API response is empty, can't get temperature", E_USER_WARNING);
			return Null;
		}

		$data = $this->ConditionsRawJSON;
		$data = json_decode($data, true);

		switch(strtolower($unit)){

			case 'kph' :
				$windspeed = $data['current_observation']['wind_kph'];
				break;
			case 'mph' :
				$windspeed = $data['current_observation']['wind_mph'];
				break;
			default :
				$windspeed = $data['current_observation']['wind_kph'];
		}

		return $windspeed;
	}

	public function GetWindDirection($type = 'degrees'){

		if(!$this->status){
			trigger_error("API response is empty, can't get temperature", E_USER_WARNING);
			return Null;
		}

		$data = $this->ConditionsRawJSON;
		$data = json_decode($data, true);

		switch(strtolower($type)){

			case 'degrees' :
				$direction = $data['current_observation']['wind_degrees'];
				break;
			case 'direction' :
				$direction = $data['current_observation']['wind_dir'];
				break;
			default :
				$direction = $data['current_observation']['wind_degrees'];
		}

		return $direction;
	}

	public function GetTemperature($unit = 'c'){

		if(!$this->status){
			trigger_error("API response is empty, can't get temperature", E_USER_WARNING);
			return Null;
		}

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

		if(!$this->status){
			trigger_error("API response is empty, can't get temperature", E_USER_WARNING);
			return Null;
		}

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

		if(!$this->status){
			trigger_error("API response is empty, can't get temperature", E_USER_WARNING);
			return Null;
		}
		
		$data = $this->GeoLookupsRawJSON;
		$data = json_decode($data);
		
		$lon = $data->{'location'}->{'lon'};
		$lat = $data->{'location'}->{'lat'};
		
		return ($lat . ', ' . $lon);
	}
	
	public function NearbyStations($radius = 100, $type = 'both'){

		if(!$this->status){
			trigger_error("API response is empty, can't get temperature", E_USER_WARNING);
			return Null;
		}
		
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

$api			 = new Weatherunderground('Netherlands', 'Amsterdam');

if($api->status){

	$coordinates	 = $api->GetGPSLocation();
	$stations		 = $api->NearbyStations(1);
	$temperatuurC 	 = $api->GetTemperature('c');
	$temperatuurF	 = $api->GetTemperature('f');
	$GtemperatuurC 	 = $api->GetPerceivedTemperature('c');
	$GtemperatuurF	 = $api->GetPerceivedTemperature('f');
	$windspeedK		 = $api->GetWindspeed('kmh');
	$windspeedM		 = $api->GetWindspeed('mph');
	$windrichtingD	 = $api->GetWindDirection('degrees');
	$windrichtingC	 = $api->GetWindDirection('direction');
}

?>
<html>
	<head>
		<title>MEOL1 - Opdracht 1</title>
	</head>
	<body>
		<?php
		
			if($api->status){

				//print_r(json_decode($api->ConditionsRawJSON));

				print( 'Windrichting in graden : ' . $windrichtingD . '<br/>' );
				print( 'Windrichting als op het compas : ' . $windrichtingC . '<br/>' );
			}else{

				print("Can't find requested city or country");
			}
		
		?>
	</body>
</html>