<?php

class Floorplanner_Account extends Floorplanner_Object {
	
	protected $_token;

	public static function fromXML($api_object, $xml, $relations = array()) {
		$account = new Floorplanner_Account($api_object);
		$account->_parseXMLAttributes($xml, $relations);
		return $account;
	}
	
	public function getProjects($page = 1, $per_page = 100) {
		$data = array('page' => $page, 'per_page' => $per_page);
		
		$response = $this->apiCall('/accounts/' . $this->getAttribute('id') . '/projects.xml', 'GET', $data);
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
	
	public function getToken() {
		if (is_null($this->_token)) {
			$response = $this->apiCall('/accounts/' . $this->getAttribute('id') . '/token', 'GET');
			if (Floorplanner::success($response)) {
				$this->_token = $response['data'];
			} else {
				throw new Floorplanner_Exception($response);	
			}
		}
		
		return $this->_token;
	}
}


?>