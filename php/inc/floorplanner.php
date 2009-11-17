<?php
/**
 *
 */

define ("API_URL", "http://www.floorplanner.com/");
define ("API_KEY", "43d9290d0301000e9d689192c14f3df4719cc501");
define ("API_USER", "x");

/**
 *
 */
class Floorplanner {
	
	var $responseHeaders;
	var $responseXml;
	var $projects;
	
	var $_host;
	var $_port;
	var $_timeout;
	var $_api_key;
	var $_api_user;
	var $_parser_depth;
	var $_current_object;
	var $_current_name;
	
	/**
	 *
	 */
	function Floorplanner($api_url, $api_key, $api_user="x", $port=80, $timeout=30) {
		$uri = parse_url($api_url);
		$this->_host = $uri["host"];
		$this->_port = $port;
		$this->_api_key = $api_key;
		$this->_api_user = $api_user;
		$this->_timeout = $timeout;
		$this->projects = array();
	}
	
	/**
	 *
	 */
	function apiCall($path, $method="GET", $payload=NULL) {
		$fp = fsockopen($this->_host, $this->_port, $errno, $errstr, $this->_timeout);
		
		$this->responseHeaders = array();
		$this->responseXml = NULL;
		$result = NULL;
		
		if (!$fp) {
		    echo "$errstr ($errno)<br />\n";
			return $result;
		} else {
		    $auth = base64_encode($this->_api_key . ":" . $this->_api_user);
		
			$out = "$method $path HTTP/1.1\r\n";
		    $out .= "Host: " . $this->_host . "\r\n";
			if ($payload) {
				$out .= "Content-Length: " . strlen($payload) . "\r\n";
				$out .= "Content-Type: application/xml\r\n";
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
			$rawXml = trim($parts[1]);
			
			if ($method == "GET") {
				$p = new SimpleParser();
				$result = $p->parse($rawXml);
				//die("<pre>".var_export($result, 1)."</pre>");
			}
			
			// parse response headers
			$headers = explode("\r\n", $rawHeaders);
			foreach($headers as $value) {
				$parts = explode(":", $value);
				$key = trim($parts[0]);
				$val = trim($parts[1]);
				$this->responseHeaders[$key] = $val;
			}

			return $result;
		}
	}
	
	/**
	 *
	 */
	function buildForm($array) {
		$form = "<table>";
		foreach ($array as $key=>$val) {
			if (is_array($val) || $key == "id" || $key == "user-id") continue;
			$form .= "<tr>";
			$form .= "<td>{$key}</td>";
			$form .= "<td><input type=\"text\" name=\"{$key}\" value=\"{$val}\"></input></td>";
			$form .= "</tr>";
		}
		$form .= "</table>";
		return $form;
	}
	
	/**
	 *
	 */
	function toXml($array, $nodeName) {
		$xml = "<" . $nodeName . ">";
		foreach ($array as $key=>$val) {
			if (is_array($val)) {
				$xml .= $this->toXml($val, $key);
			} else {
				$xml .= "<" . $key . ">";
				$xml .= trim($val);
				$xml .= "</" . $key . ">";
			}
		}
		$xml .= "</" . $nodeName . ">";
		return $xml;
	}
	
	/**
	 *
	 */
	function createProject($project) {
		$path = "/projects.xml";
		$payload = $this->toXml($project, "project");
		if ($this->apiCall($path, "POST", $payload)) {
		}
	}
	
	/**
	 *
	 */
	function createUser($user) {
		$path = "/users.xml";
		$payload = $this->toXml($user, "user");
		if ($this->apiCall($path, "POST", $payload)) {
		}
	}
	
	/**
	 *
	 */
	function deleteProject($id) {
		$path = "/projects/{$id}.xml";
		$payload = $this->toXml(array("id"=>$id), "project");
		if ($this->apiCall($path, "DELETE", $payload)) {
		}
	}
	
	/**
	 *
	 */
	function deleteUser($id) {
		$path = "/users/{$id}.xml";
		$payload = $this->toXml(array("id"=>$id), "user");
		if ($this->apiCall($path, "DELETE", $payload)) {
		}
	}
	
	/**
	 *
	 */
	function getDesign($id) {
		$path = "/designs/{$id}.xml";
		$result = $this->apiCall($path);
		if ($result && array_key_exists("designs", $result)) {
			return $result["designs"][0];
		} else {
			return NULL;
		}
	}
	
	/**
	 *
	 */
	function getProject($id) {
		$path = "/projects/{$id}.xml";
		$result = $this->apiCall($path);
		if ($result && array_key_exists("projects", $result)) {
			return $result["projects"][0];
		} else {
			return NULL;
		}
	}
	
	/**
	 *
	 */
	function getProjects($page = 1, $per_page = 100) {
		$path = "/projects.xml?page=$page&per_page=$per_page";
		$result = $this->apiCall($path);
		if ($result && array_key_exists("projects", $result)) {
			return $result["projects"];
		} else {
			return NULL;
		}
	}
	
	/**
	 *
	 */
	function getToken($id) {
		$path = "/users/{$id}/token.xml";
		$result = $this->apiCall($path);
		if ($result && array_key_exists("users", $result) && count($result["users"]) > 0) {
			$user = $result["users"][0];
			return $user["current-token"];
		} else {
			return NULL;
		}
	}
	
	/**
	 *
	 */
	function getUser($id) {
		$path = "/users/{$id}.xml";
		$result = $this->apiCall($path);
		if ($result && array_key_exists("users", $result)) {
			return $result["users"][0];
		} else {
			return NULL;
		}
	}
	
	/**
	 *
	 */
	function getUsers($page = 1, $per_page = 100) {
		$path = "/users.xml?page=$page&per_page=$per_page";
		$result = $this->apiCall($path);
		if ($result && array_key_exists("users", $result)) {
			return $result["users"];
		} else {
			return NULL;
		}
	}
	
	/**
	 *
	 */
	function updateProject($project) {
		$path = "/projects/{$project['id']}.xml";
		$payload = $this->toXml($project, "project");
		if ($this->apiCall($path, "PUT", $payload)) {
		}
	}
	
	/**
	 *
	 */
	function updateUser($user) {
		$path = "/users/{$user['id']}.xml";
		$payload = $this->toXml($user, "user");
		if ($this->apiCall($path, "PUT", $payload)) {
		}
	}
}

/**
 * Simple XML parser.
 */
class SimpleParser {
	var $projects;
	var $users;
	var $floors;
	var $designs;
	var $result;
	
	var $currentProject;
	var $currentFloor;
	var $currentDesign;
	var $currentUser;
	var $currentObject;
	var $currentName;
	var $parserDepth;
	
	function SimpleParser() {
	}
	
	function parse($xml) {
		$this->result = array();
		$xml_parser = xml_parser_create();
		xml_set_object ( $xml_parser, $this );
		xml_set_element_handler($xml_parser, "startElement", "endElement");
		xml_set_character_data_handler($xml_parser, "contents");
		if (!xml_parse($xml_parser, $xml, true)) {
			die(sprintf("XML error: %s at line %d", 
				xml_error_string(xml_get_error_code($xml_parser)),
				xml_get_current_line_number($xml_parser)) . "\n" . $xml);
		}
		xml_parser_free($xml_parser);
		return $this->result;
	}
	
	function startElement($parser, $name, $attrs) {
	    $this->parserDepth[$parser]++;
		switch($name) {
			case "USERS":
				$this->result["users"] = array();
				break;
			case "USER":
				$this->currentUser = array();
				$this->currentObject = "USER";
				break;
			case "PROJECTS":
				$this->result["projects"] = array();
				break;
			case "PROJECT":
				$this->currentProject = array();
				$this->currentObject = "PROJECT";
				break;
			case "FLOORS":
				if ($this->currentProject) {
					$this->currentProject["floors"] = array();
				} else {
					$this->result["floors"] = array();
				}
				break;
			case "FLOOR":
				$this->currentFloor = array();
				$this->currentObject = "FLOOR";
				break;
			case "DESIGNS":
				if ($this->currentFloor) {
					$this->currentFloor["designs"] = array();
				}
				break;
			case "DESIGN":
				$this->currentDesign = array();
				$this->currentObject = "DESIGN";
				break;
			default:
				$this->currentName = strtolower($name);
				break;
		}
	}

	function endElement($parser, $name) {
	    $this->parserDepth[$parser]--;
		switch($name) {
			case "USERS":
				break;
			case "USER":
				if (!array_key_exists("users", $this->result)) {
					$this->result["users"] = array();
				}
				$this->result["users"][] = $this->currentUser;
				$this->currentUser = NULL;
				break;
			case "PROJECTS":
				break;
			case "PROJECT":
				if (!array_key_exists("projects", $this->result)) {
					$this->result["projects"] = array();
				}
				$this->result["projects"][] = $this->currentProject;
				$this->currentProject = NULL;
				break;
			case "FLOOR":
				if ($this->currentProject) {
					$this->currentProject["floors"][] = $this->currentFloor;
					$this->currentObject = "PROJECT";
				} else {
					if (!array_key_exists("floors", $this->result)) {
						$this->result["floors"] = array();
					}
					$this->result["floors"][] = $this->currentFloor;
				}
				$this->currentFloor = NULL;
				break;
			case "DESIGN":
				if ($this->currentFloor) {
					$this->currentFloor["designs"][] = $this->currentDesign;
					$this->currentObject = "FLOOR";
				} else {
					if (!array_key_exists("designs", $this->result)) {
						$this->result["designs"] = array();
					}
					$this->result["designs"][] = $this->currentDesign;
				}
				$this->currentDesign = NULL;
				break;
			default:
				break;
		}
	}
	
	function contents($parser, $data) {
		$data = preg_replace("/^\s+/", "", $data); 
		$data = preg_replace("/\s+$/", "", $data);
		if (strlen($data) ) {	
			switch ($this->currentObject) {
				case "PROJECT":
					$this->currentProject[ $this->currentName ] = $data;
					break;
				case "FLOOR":
					$this->currentFloor[ $this->currentName ] = $data;
					break;
				case "DESIGN":
					$this->currentDesign[ $this->currentName ] = $data;
					break;
				case "USER":
					$this->currentUser[ $this->currentName ] = $data;
					break;
				default:
					break;
			}
		}
	}
}
?>