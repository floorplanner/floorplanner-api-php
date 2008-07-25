<?php

class Floorplanner_Object {
	
	protected $_attributes = array();
	protected $_new_object = true;
	protected $_changed_attributes = array();
	protected $_related_objects = array();
	
	protected $_api_object;
	
	function __construct($api_object) {
		$this->_api_object = $api_object;
	}
	
	public function __get($attribute) {
		return $this->getAttribute($attribute);
	}
	
	public function __set($attribute, $value) {
		$this->setAttribute($attribute, $value);
	}
	
	public function getAttribute($attribute) {
		if (array_key_exists($attribute, $this->_changed_attributes))
			return $this->_changed_attributes[$attribute];
		else
			return $this->_attributes[$attribute];
	}
	
	public function setAttribute($attribute, $value) {
		$this->_changed_attributes[$attribute] = $value;
	}
	
	protected function getChangedAttributesArray($prefix) {
		$result = array();
		foreach ($this->_changed_attributes as $key => $value) {
			$result["{$prefix}[{$key}]"] = $value;
		}
		return $result;
	}
	
	protected function _parseXMLAttributes($xml_element, $relations = array()) {
		$this->_new_object = false;
		
		foreach($xml_element->children() as $child) {
			$child_name = $child->getName();		
			if (isset($relations[$child_name])) {
				$parse_array = false;
				foreach ($child->attributes() as $key => $value) {
					$value = (string) $value;
					if ($key == 'type' && $value == 'array') {
						$parse_array = true;
						break;
					}
				}
				
				if ($parse_array) {
					$list = array();
					foreach ($child->children() as $related) {
						$list[] = call_user_func(array($relations[$child_name], 'fromXML'), $this->_api_object, $related, $relations);
					}
					$this->_related_objects[$child_name] = $list;
				} else {
					$relation = call_user_func(array($relations[$child_name], 'fromXML'), $this->_api_object, $child, $relations);
					$this->_related_objects[$child_name] = $relation;
				}
				
			} else {
				$this->_attributes[$child_name] =  (string) $xml_element->$child_name;
			}
		}
	}
		

	public function path($format = null) {
		throw new Exception("Implement this function in the subclass!");
	}
	
	public function url($format = null) {
		return 'http://' . $this->_api_object->host . $this->path($format);
	}
		
	protected function apiCall($path, $method = 'GET', $params = array(), $headers = array()) {
		return $this->_api_object->apiCall($path, $method, $params, $headers);
	}
}

?>