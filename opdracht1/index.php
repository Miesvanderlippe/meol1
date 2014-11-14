<?php

class Weatherunderground{
	
	/* API stuff*/
	protected $apikey = "b20a23e4857a0c68";
	protected $apiurl = "http://api.wunderground.com/api/";
	
	/* Variables */
	public $ConditionsRawJSON;
	public $GeoLookupsRawJSON;
	public $status;
	public $country;
	public $city;
	public $language = 'NL';

	/*
		Description : 
			Constructor.
			Requests and saves weather data
		
		Parameters :
			Country (str)	 - The country the city you are looking for is in
			City(str)		 - The city you're looking for
		
		Return value :
			void
	*/	
	public function __construct($country ='France', $city='Paris'){
		
		/* Save city and country*/
		$this->country 	 = $country;
		$this->city 	 = $city;

		/* Conditions */
		$requestURL		 = $this->apiurl . $this->apikey . '/conditions/lang:' . $this->language .'/q/' . urlencode($country) . '/' . urlencode($city) . '.json';
		$data			 = $this->CurlGet($requestURL);

		$this->ConditionsRawJSON = $data;
		
		/* Geolookup*/
		$requestURL		 = $this->apiurl . $this->apikey . '/geolookup/lang:' . $this->language .'/q/' . urlencode($country) . '/' . urlencode($city) . '.json';
		$data			 = $this->CurlGet($requestURL);

		$this->GeoLookupsRawJSON = $data;

		/* Request status */
		$this->status 	 = true;

		$geolookupdata	 = json_decode($this->GeoLookupsRawJSON, true);
		$geolookupstatus = (isset($geolookupdata['response']['error']) ? false : true);

		$condsdata		 = json_decode($this->GeoLookupsRawJSON, true);
		$condstatus 	 = (isset($condsdata['response']['error']) ? false : true);

		if(!$condstatus || !$geolookupstatus)
			$this->status 	 = false;
	}

	/*
		Description : 
			Makes a GET request on the given URL and returns the returned data.
			Auto opens and closes the connection and sets some options
		
		Parameters :
			url (str) - the url you want data from
		
		Return value :
			data (str) All data found on page
	*/
	private function CurlGet($url){
		
		$curl	 = curl_init();
		
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		
		$data	 = curl_exec($curl);
		
		curl_close($curl);
		
		return($data);
	}

	/*
		Description :
			Gets the URL for an icon describing the weather from saved data

		Parameters : 
			None

		Return value :
			URL (str) - full HTTP link to icon
	*/
	public function GetIconURL(){

		if(!$this->status){
			trigger_error("API response is empty, can't get icon", E_USER_WARNING);
			return Null;
		}

		$data = $this->ConditionsRawJSON;
		$data = json_decode($data, true);

		$iconURL = $data['current_observation']['icon_url'];

		return $iconURL;
	}

	/*
		Description : 
			Gets satelite image url of saved area.
			You can get both a .gif (animated clouds) or a .png (static image).
			!!! WATCH OUT : REVEALS API KEYS IF USED INCORRECTLY!!!
			Use in combination with image relay server, proof of concept can be found here : 
			https://github.com/Miesvanderlippe/meol1/blob/master/opdracht1/imagerelay.php

		Parameters :
			width (int) - image width (default = 300)
			height (int) - image height (default = 300)
			animated (bool) - wether image is animated or not (default = false)
		
		Return value :
			URL (str) - URL to image.
	*/
	public function GetSateliteImageURL($width = 300, $height = 300, $animated = false){

		if(!$this->status){
			trigger_error("API response is empty, can't get satelite image", E_USER_WARNING);
			return Null;
		}

		$relayurl = 'imagerelay.php?url=';

		if($animated)
			$imageURL		 = $relayurl . urlencode('/animatedsatellite/q/' . urlencode($this->country) . '/' . urlencode($this->city) . '.gif?basemap=1&width=' . $width . '&height=' . $height);
		else
			$imageURL		 = $relayurl . urlencode('/satellite/q/' . urlencode($this->country) . '/' . urlencode($this->city) . '.png?basemap=1&width=' . $width . '&height=' . $height);

		return $imageURL;
	}

	/*
		Description : 
			Gets wind speed from saved data. 
		
		Parameters :
			unit (str (case insensitive)) - The unit you want to get windspeed in. 
				kmh 	(kilometers per hour)
				mph 	(miles per hour)
		
		Return value :
			Windspeed (str) - Found windspeed
	*/
	public function GetWindspeed($unit = 'kmh'){

		if(!$this->status){
			trigger_error("API response is empty, can't get windspeed", E_USER_WARNING);
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

	/*
		Description : 
			Gets wind direction from saved data
		
		Parameters :
			type (str (case insensitive)) - The format you want the direction in.
				degrees 	(degrees out of 360)
				direction 	(Direction as you'd see on a compass)
		
		Return value :
			direction (str) - Found winddirection
	*/
	public function GetWindDirection($type = 'degrees'){

		if(!$this->status){
			trigger_error("API response is empty, can't get winddirection", E_USER_WARNING);
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

	/*
		Description : 
			Gets temperature from saved data
		
		Parameters :
			unit (str (case insensitive)) - The unit the temperature will be returned in.
				c 	(degrees Celcius)
				f 	(degrees Fahrenheit)
		
		Return value :
			direction (str) - Found temperature
	*/
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

	/*
		Description : 
			Gets perceived temperature from saved data
		
		Parameters :
			unit (str (case insensitive)) - The unit the temperature will be returned in.
				c 	(degrees Celcius)
				f 	(degrees Fahrenheit)
		
		Return value :
			direction (str) - Found temperature
	*/
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
	
	/*
		Description : 
			Gets longtitude and latitude from saved data
		
		Parameters :
			None
		
		Return value :
			location (str) - latitude and longtitude devided by a comma and a space
	*/
	public function GetGPSLocation(){

		if(!$this->status){
			trigger_error("API response is empty, can't get location", E_USER_WARNING);
			return Null;
		}
		
		$data = $this->GeoLookupsRawJSON;
		$data = json_decode($data);
		
		$lon = $data->{'location'}->{'lon'};
		$lat = $data->{'location'}->{'lat'};
		
		return ($lat . ', ' . $lon);
	}
	
	/*
		Description : 
			Gets weather data sources in the given radius (km). You can choose wether you want airports, stations or both
		
		Parameters :
			radius 	(int) - Maximum distance between given city and weatherstations. Does not affect returned airports.
			type 	(str (case insensitive)) - The type of weathersource to return
				airports	 (just airports)
				stations 	 (just stations)
				both (both stations and airports)
		
		Return value :
			response (array) - When a type is specified just the found sources, otherwise an array with the types of source and within there the found sources
	*/
	public function NearbyStations($radius = 100, $type = 'both'){

		if(!$this->status){
			trigger_error("API response is empty, can't get nearby stations", E_USER_WARNING);
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

?>
<html>
	<head>
		<title>MEOL1 - Opdracht 1</title>
	</head>
	<body>

		<?php
			if($api->status){
				print("Land : " . $api->country . "<br/>");
				print("Stad : " . $api->city . "<br/>");

				print("Coordinaten : " . $api->GetGPSLocation() . "<br/>");

				print("Temperatuur in graden Celcius : " . $api->GetTemperature('c') . "<br/>");
				print("Temperatuur in graden Fahrenheit : " . $api->GetTemperature('f') . "<br/>");

				print("Gevoelsemperatuur in graden Celcius : " . $api->GetPerceivedTemperature('c') . "<br/>");
				print("Gevoelsemperatuur in graden Fahrenheit : " . $api->GetPerceivedTemperature('f') . "<br/>");


				print('Windrichting in graden : ' . $api->GetWindDirection('degrees') . '<br/>' );
				print('Windrichting als op het compas : ' . $api->GetWindDirection('direction') . '<br/>');

				print('Windsnelheid in km/u : ' . $api->GetWindspeed('kmh') . '<br/>' );
				print('Windsnelheid in mp/h: ' . $api->GetWindspeed('mph') . '<br/>' );

				print("<img alt='Current Weather' src='" . $api->GetIconURL() . "'/>");
				print("<img alt='Current Weather' src='" . $api->GetSateliteImageURL() . "'/>");

				print("<b>Weerstations in een radius van 5 KM van de gegeven stad</b><br/>");

				$weathersources = $api->NearbyStations(5);

				print("<br/><b>Vliegvelden</b><br/>");
				foreach($weathersources['airports'] as $airport){

					print('land : ' . $airport['country'] . '<br/>');
					print('stad : ' . $airport['city'] . '<br/>');
					print('longtitude : ' . $airport['lon'] . '<br/>');
					print('latitude : ' . $airport['lat'] . '<br/>');

					print('<br/>');
				}


				print("<br/><b>Weerstations</b><br/>");
				foreach($weathersources['stations'] as $station){
					
					print('land : ' . $station['country'] . '<br/>');
					print('stad : ' . $station['city'] . '<br/>');
					print('wijk : ' . $station['neighborhood'] . '<br/>');
					print('longtitude : ' . $station['lon'] . '<br/>');
					print('latitude : ' . $station['lat'] . '<br/>');
					
					print('<br/>');
				}

			}else{

				print("Can't find requested city or country");
			}
		
		?>
	</body>
</html>