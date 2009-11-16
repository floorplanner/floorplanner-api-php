<?php
define ("API_URL", "http://www.floorplanner.com/");
define ("API_KEY", "43d9290d0301000e9d689192c14f3df4719cc501");
define ("API_USER", "x");

class Floorplanner {
	
	public $responseHeaders;
	public $responseXml;
	
	private $_host;
	private $_port;
	private $_timeout;
	private $_api_key;
	private $_api_user;
	
	public function __construct($api_url, $api_key, $api_user="x", $port=80, $timeout=30) {
		$uri = parse_url($api_url);
		$this->_host = $uri["host"];
		$this->_port = $port;
		$this->_api_key = $api_key;
		$this->_api_user = $api_user;
		$this->_timeout = $timeout;
	}
	
	/**
	 *
	 */
	private function apiCall($path, $method="GET", $payload=NULL) {
		$fp = fsockopen($this->_host, $this->_port, $errno, $errstr, $this->_timeout);
		
		$this->responseHeaders = array();
		$this->responseXml = NULL;
		
		if (!$fp) {
		    echo "$errstr ($errno)<br />\n";
			return FALSE;
		} else {
		    $auth = base64_encode($this->_api_key . ":" . $this->_api_user);
		
			$out = "$method $path HTTP/1.1\r\n";
		    $out .= "Host: " . $this->_host . "\r\n";
			
			if ($payload) {
				$out .= "Content-Length: " . strlen($payload) . "\r\n";
			}
			
			$out .= "Authorization: Basic $auth\r\n";
		    $out .= "Connection: Close\r\n\r\n";
			
			if ($payload != NULL) {
				$out .= $payload;
			}
			
			// write to stream
			fwrite($fp, $out);
			
			// fetch result
			$response = "";
			while (!feof($fp)) {
				$response .= fgets($fp, 128);
			}
			fclose($fp);

			// parse result
			$parts = explode("\r\n\r\n", $response);
			$rawHeaders = trim($parts[0]);
			$this->responseXml = simplexml_load_string(trim($parts[1]));
			
			// parse response headers
			$headers = explode("\r\n", $rawHeaders);
			foreach($headers as $value) {
				$parts = explode(":", $value);
				$key = trim($parts[0]);
				$val = trim($parts[1]);
				$this->responseHeaders[$key] = $val;
			}

			return TRUE;
		}
	}
	
	public function deleteProject($id) {
		$path = "/projects/{$id}.xml";
		$project = new FloorplannerProject(NULL);
		$project->id = $id;
		$payload = $project->toXml();
		
		if ($this->apiCall($path, "DELETE", $payload)) {
		}
	}
	
	public function getProject($id) {
		$path = "/projects/{$id}.xml";
		if ($this->apiCall($path)) {
			$xml = $this->responseXml;
			return new FloorplannerProject($xml);
		} else {
			return NULL;
		}
	}
	
	public function getProjects($page = 1, $per_page = 100) {
		$path = "/projects.xml?page=$page&per_page=$per_page";
		$projects = array();
		if ($this->apiCall($path)) {
			$xml = $this->responseXml;
			foreach($xml->children() as $child) {
				if ($child->getName() == "project") {
					$projects[] = new FloorplannerProject($child);
				}
			}
		}
		return $projects;
	}
	
	public function getToken($id) {
		$path = "/users/{$id}/token.xml";
		if ($this->apiCall($path)) {
			$xml = $this->responseXml;
			$user = new FloorplannerUser($xml);
			return $user->current_token;
		} else {
			return NULL;
		}
	}
	
	public function getUser($id) {
		$path = "/users/{$id}.xml";
		if ($this->apiCall($path)) {
			$xml = $this->responseXml;
			return new FloorplannerUser($xml);
		} else {
			return NULL;
		}
	}
	
	public function getUsers($page = 1, $per_page = 100) {
		$path = "/users.xml?page=$page&per_page=$per_page";
		$users = array();
		if ($this->apiCall($path)) {
			$xml = $this->responseXml;		
			foreach($xml->children() as $child) {
				if ($child->getName() == "user") {
					$users[] = new FloorplannerUser($child);
				}
			}
		}
		return $users;
	}
}

class FloorplannerObject {

	public $data;
	
	public function __construct($xml) {
		$this->data = array();
		if ($xml != NULL) {
			foreach ($xml->children() as $child) {
				$this->data[$child->getName()] = (string) $child;
			}
		}
	}
	
	public function __get($name) {
		$name = str_replace("_", "-", $name);
		return $this->data[$name];
	}
	
	public function __set($name, $value) {
		$name = str_replace("_", "-", $name);
		$this->data[$name] = $value;
	}
	
	public function buildForm($fields=NULL) {
		$html = "<form>";
		$html .= "<table>";
		
		if ($fields) {
			foreach($fields as $field) {
				$value = $this->data[$field];
				$text = "<input type=\"text\" name=\"{$field}\" value=\"{$value}\"></input>";
				$html .= "<tr><td>{$field}</td><td>{$text}</td></tr>";
			}
		}
		
		$html .= "</table>";
		$html .= "</form>";
		return $html;
	}
	
	public function toXml($name="user") {
		$xml = "<$name>";
		foreach($this->data as $key=>$val) {
			$xml .= "<$key>";
			$xml .= $val;
			$xml .= "</$key>";
		}
		$xml .= "</$name>";
		return $xml;
	}
}

class FloorplannerDesign extends FloorplannerObject  {
	public function __construct($xml) {
		parent::__construct($xml);
	}
}

class FloorplannerFloor extends FloorplannerObject  {
	public function __construct($xml) {
		parent::__construct($xml);
	}
}

class FloorplannerProject extends FloorplannerObject  {
	public function __construct($xml) {
		parent::__construct($xml);
	}
	
	public function buildForm($fields=NULL) {
		$fields = array("name", "description", "element-library-id", "texture-library-id", "external-identifier",
			"grid-cell-size", "grid-sub-cell-size");
		return parent::buildForm($fields);
	}
	
	public function toXml($name="project") {
		return parent::toXml($name);
	}
}

class FloorplannerUser extends FloorplannerObject {
	public function __construct($xml) {
		parent::__construct($xml);
	}
	
	public function buildForm($fields=NULL) {
		$fields = array("username", "email", "profile", "url", "account-type", "external-identifier");
		return parent::buildForm($fields);
	}
}
?>