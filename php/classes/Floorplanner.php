<?php

require_once 'Floorplanner/Exception.php';
require_once 'Floorplanner/Object.php';
require_once 'Floorplanner/User.php';
require_once 'Floorplanner/Project.php';
require_once 'Floorplanner/Floor.php';
require_once 'Floorplanner/Design.php';

class Floorplanner {
	
	public $host;
	public $api_key;
	public $token;
	
	const DEFAULT_HOST = 'beta.floorplanner.com';
	
	/**
	 * Creates a new Floorplanner API consumer object. This function is used
	 * internally, use Floorplanner::connect() instead.
	 */
	protected function __construct($host = null) {
		$this->host = is_null($host) ? Floorplanner::DEFAULT_HOST : $host;
	}
	
	/**
	 * Connects to the Floorplanner-server for API calls.
	 *
	 * Note that this function doesn't actually connect to the server,
	 * but only keeps track of the API credentials, which are used
	 * by subsequent calls to the API using the resulting Floorplanner-
	 * instance.
	 * 
	 * @param string $username The username (email address) to use for 
	 *			the connection.
	 * @param string $api_key The API key/password to use for the connection
	 * @param string $host The host to connect to. This parameter can be
	 *			left blank to connect to the default server.
	 * @return object An Floorplanner-instance that can be used for API calls.
	 */
	public static function connect($api_key, $host = null) {
		$fp = new Floorplanner($host);
		$fp->api_key  = $api_key;
		return $fp;
	}
	
	public static function connectWithToken($api_key, $user_token, $host = null) {
		$fp = new Floorplanner($host);
		$fp->api_key  = $api_key;
		$fp->token = $user_token;
		return $fp;
	}
	
	public function factory($type) {
		switch (strtolower($type)) {
			case 'user': return new Floorplanner_User($this); break;
			case 'project': return new Floorplanner_Project($this); break;
			case 'floor':   return new Floorplanner_Floor($this); break;	
			case 'design':  return new Floorplanner_Design($this); break;	
			
			default: throw new Exception("Object of type {$type} not supported");
		}
	}
	
	
	public function javascriptIncludes() {
		$result  = '<script type="text/javascript" src="http://' . $this->host . '/javascripts/floorplanner/swfobject_20.js"></script>' . "\n";
		$result .= '<script type="text/javascript" src="http://' . $this->host . '/javascripts/floorplanner/floorplanner.js"></script>' . "\n";
		return $result;
	}
	
	//////////////////////////////////////////////////////////////
	// PROJECT & USER FUNCTIONS
	// Use the following functions to interact with Floorplanner
	// projects. Projects give access to the related floors and
	// designs.
	//////////////////////////////////////////////////////////////
	
	public function getUsers($page = 1, $per_page = 100) {
		$data = array('page' => $page, 'per_page' => $per_page);
		$response = $this->apiCall('/users.xml', 'GET', $data);
		if (Floorplanner::success($response)) {
			$xml = Floorplanner::parseXMLResponse($response);
			$result = array();
			foreach ($xml->user as $user) {
				$result[] = Floorplanner_User::fromXML($this, $user);
			}
			return $result;
		}
		else {
			throw new Floorplanner_Exception($response);
		}		
	}

	public function getUser($id) {

		$response = $this->apiCall("/users/{$id}.xml", 'GET');
		if (Floorplanner::success($response)) {		
			$xml = Floorplanner::parseXMLResponse($response);
			return Floorplanner_User::fromXML($this, $xml);
		} else {
			throw new Floorplanner_Exception($response);
		}		
	}	
	
	/**
	 * Returns a list of projects accessible to the user.
	 * @return array of Floorplanner_Project instances.
	 */
	public function getProjects($page = 1, $per_page = 100) {
		$data = array('page' => $page, 'per_page' => $per_page);
		
		$response = $this->apiCall('/projects.xml', 'GET', $data);
		if (Floorplanner::success($response)) {
			$xml = Floorplanner::parseXMLResponse($response);
			$result = array();
			foreach ($xml->project as $project) {
				$result[] = Floorplanner_Project::fromXML($this, $project);
			}
			return $result;
		}
		else {
			throw new Floorplanner_Exception($response);
		}
	}
	
	/**
	 * Returns a Floorplanner project given its hash, including its floors
	 * and designs.
	 * @param string $hash The hash that identifies the project
	 * @return Floorplanner_Project The requested project-instance; null
	 *		if the project is not found or is inaccessible.
	 */
	public function getProject($id) {
		$response = $this->apiCall("/projects/{$id}.xml", 'GET');
		if (Floorplanner::success($response)) {		
			$xml = Floorplanner::parseXMLResponse($response);
			return Floorplanner_Project::fromXML($this, $xml, 
						array(  'floors'  => 'Floorplanner_Floor',
								'designs' => 'Floorplanner_Design'));
		} else {
			throw new Floorplanner_Exception($response);
		}
	}
	
	public function getToken() {
		if (is_null($this->token)) {
			$response = $this->apiCall('/users/me/token', 'GET');
			if (Floorplanner::success($response)) {
				$this->token = $response['data'];
			} else {
				throw new Floorplanner_Exception($response);	
			}
		}
		
		return $this->token;
	}
	
	
	
	
	//////////////////////////////////////////////////////////////
	// LOW LEVEL API CALLING
	// These functions are used to call the Floorplanner REST API. 
	// You should not use these functions directly, but use the
	// Floorplanner-objects instead.
	//////////////////////////////////////////////////////////////
	
	/**
	 * Parses the response headers.
	 * @param array $response A response returned by Floorplanner::apiCall()
	 * @return array An associated array with all headers and their values  
	 */
	public static function parseResponseHeaders($response) {
		$raw_headers = explode("\n", $response['headers']);
		unset($raw_headers[0]); // remove HTTP status code
		$parsed_headers = array();
		foreach($raw_headers as $raw_header) {
			$parsed_data = explode(': ', $raw_header, 2);
			if (is_array($parsed_data) && count($parsed_data) == 2) {
				$parsed_headers[$parsed_data[0]] = trim($parsed_data[1], "\r\n");
			}
		}
		
		return $parsed_headers;
	}
	
	/**
	 * Parses the XML that is returned by an API call.
	 * The SimpleXML-extention is used for parsing.
	 * @param array $response A response returned by Floorplanner::apiCall()	
	 * @return object A SimpleXMLElement instance.
	 */
	public static function parseXMLResponse($response) {
		return simplexml_load_string($response['data']);
	}
	
	/**
	 * Checks whether the response was successful (HTTP status code in 200-range)
	 * @param array $response A response returned by Floorplanner::apiCall()
	 * @return bool Whether the API call was successful	
	 */
	public static function success($response) {
		return (is_array($response) && $response['status'] >= 200 && $response['status'] < 300);
	}
	
	/**
	 * Performs a call to the Floorplanner API.
	 * To perform the calls to the API, the CURL-extention for PHP is required.
	 */
	public function apiCall($path, $method = 'GET', $params = array(), $headers = array()) {
		
		$ch = curl_init();
		
		// Return the headers
		curl_setopt($ch, CURLOPT_HEADER, true); 
		// Return the actual reponse as string
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		if (!empty($this->api_key)) {
			// Send HTTP Basic authentication credentials
			curl_setopt($ch, CURLOPT_USERPWD, $this->api_key . ':x');
		}

		if (!empty($this->token)) {
			$headers['AuthToken'] = $this->token;
		}
		
		// Handle the different HTTP methods
		switch(strtoupper($method)) {
			case 'PUT': case 'DELETE':
				if (!is_array($params)) $params = array();
				$params['_method'] = strtoupper($method);
			case 'POST': 		
				curl_setopt($ch, CURLOPT_POST, true);
				if (is_array($params) && count($params) > 0) {
					curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
				}
				break;
			default: // GET request
				if (is_array($params) && count($params) > 0) {
					$path .= '?' . http_build_query($params);
				}
				break;
		}
		
		// Set any additional headers
		if (!is_array($headers)) $headers = array();
		$headers['Expect'] = '';
		
		$raw_headers = array();
		foreach ($headers as $key => $value) {
			$raw_headers[] = "{$key}: {$value}";
		}

		curl_setopt($ch, CURLOPT_HTTPHEADER, $raw_headers);
		curl_setopt($ch, CURLOPT_URL, $this->host . $path);
		
		// Execute the API call
		$raw_data = curl_exec($ch); 
		curl_close($ch);
		
		if ($raw_data === false) {
			return false;
		} else {
			// SPlit the headers from the actual response
			$response = explode("\r\n\r\n", $raw_data, 2);
			
			// Find the HTTP status code
			$matches = array();
			if (preg_match('/^HTTP.* ([0-9]+) /', $response[0], $matches)) {
				$status = intval($matches[1]);
			}
			
			// Build the result array
			return array(
					'status'  => $status,
					'headers' => $response[0],
					'data'    => $response[1]
				);
		}
	}
	
}


?>