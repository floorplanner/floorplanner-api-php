<?php

class Floorplanner_Floor extends Floorplanner_Object {

	public static function fromXML($api_object, $xml, $relations = array()) {
		$floor = new Floorplanner_Floor($api_object);
		$floor->_parseXMLAttributes($xml, $relations);
		return $floor;
	}
	
	public function designs() {
		return $this->_related_objects['designs'];
	}
	
}


?>