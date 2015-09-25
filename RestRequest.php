<?php

class RestRequest {

	private $urlElements;
	private $verb;
	private $parameters;

	public function __construct() {
		$this->verb = $_SERVER['REQUEST_METHOD'];
		$this->urlElements = explode('/', substr($_SERVER['PHP_SELF'], strlen($_SERVER['SCRIPT_NAME'])+1));
		if (empty(end($this->urlElements))) {
			array_pop($this->urlElements);
		}
		$this->parseIncomingParams();
		// initialise json as default format
		$this->format = 'json';
		if(isset($this->parameters['format'])) {
			$this->format = $this->parameters['format'];
		}
		return true;
	}

	public function parseIncomingParams() {
		$parameters = array();

		// first of all, pull the GET vars
		if (isset($_SERVER['QUERY_STRING'])) {
			parse_str($_SERVER['QUERY_STRING'], $parameters);
		}

		// now how about PUT/POST bodies? These override what we got from GET
		$body = file_get_contents('php://input');
		$content_type = false;
		if(isset($_SERVER['CONTENT_TYPE'])) {
			$content_type = $_SERVER['CONTENT_TYPE'];
		}
		switch($content_type) {
			case 'application/json':
				$body_params = json_decode($body);
				if($body_params) {
					foreach($body_params as $param_name => $param_value) {
						$parameters[$param_name] = $param_value;
					}
				}
				$this->format = 'json';
				break;
			case 'application/x-www-form-urlencoded':
				parse_str($body, $postvars);
				foreach($postvars as $field => $value) {
					$parameters[$field] = $value;

				}
				$this->format = 'html';
				break;
			default:
				// we could parse other supported formats here
				break;
		}
		$this->parameters = $parameters;
	}

	/**
	* Gets the value of urlElements.
	*
	* @return mixed
	*/
	public function getUrlElements() {
		return $this->urlElements;
	}

	/**
	* Sets the value of urlElements.
	*
	* @param mixed $urlElements the url elements
	*
	* @return self
	*/
	public function setUrlElements($urlElements){
		$this->urlElements = $urlElements;
		return $this;
	}

	/**
	* Gets the value of verb.
	*
	* @return mixed
	*/
	public function getVerb() {
		return $this->verb;
	}

	/**
	* Sets the value of verb.
	*
	* @param mixed $verb the verb
	*
	* @return self
	*/
	public function setVerb($verb){
		$this->verb = $verb;
		return $this;
	}

	/**
	* Gets the value of parameters.
	*
	* @return mixed
	*/
	public function getParameters() {
		return $this->parameters;
	}

	/**
	* Sets the value of parameters.
	*
	* @param mixed $parameters the parameters
	*
	* @return self
	*/
	public function setParameters($parameters){
		$this->parameters = $parameters;
		return $this;
	}
}