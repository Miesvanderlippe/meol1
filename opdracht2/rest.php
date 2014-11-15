<?php

class REST{
	
	public $method;

	public function __construct() {

		$this->method = $this->GetMethod();
	}


	public function GetMethod() {

		return (strtoupper($_SERVER['REQUEST_METHOD']));
	}
}