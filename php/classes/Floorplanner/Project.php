<?php

class Floorplanner_Project extends Floorplanner_Object {
	
	protected $_token;
	
	public function getToken() {
		if (is_null($this->_token)) {
			$response = $this->apiCall('/accounts/' . $this->getAttribute('user-id') . '/token', 'GET');
			if (Floorplanner::success($response)) {
				$this->_token = $response['data'];
			} else {
				throw new Floorplanner_Exception($response);	
			}
		}
		
		return $this->_token;
	}
		
	public function path($format = null) {
		$result = "/projects/" . $this->id;
		if (!is_null($format)) $result .= '.' . $format;
		return $result;
	}
	
	public static function fromXML($api_object, $xml, $relations = array()) {
		$project = new Floorplanner_Project($api_object);
		$project->_parseXMLAttributes($xml, $relations);
		return $project;
	}
	
	public function floors() {
		return $this->_related_objects['floors'];
	}
	
	public function iframeHTML($token) {
		// TODO
	}
	
	public function hash() {
		return $this->_attributes['project-url'];
	}
	
	public function embedScript($div, $token = null, $mode = null) {
		if (is_null($mode)) $mode = 'Floorplanner.STATE_EDIT';
		$project_hash = $this->hash();
		if (is_null($token)) {
			$javascript  = "var fp = new Floorplanner('${project_hash}', {config: ${mode}, auth_token: '${token}'});\n";
		} else {
			$javascript  = "var fp = new Floorplanner('${project_hash}', {config: ${mode}});\n";
		}
		
		$javascript .= "fp.embed('${div}');\n";
		return $javascript;
	}
	
}

?>