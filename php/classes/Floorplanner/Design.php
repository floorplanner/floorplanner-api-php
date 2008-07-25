<?php

class Floorplanner_Design extends Floorplanner_Object {
	

	public static function fromXML($api_object, $xml, $relations = array()) {
		$design = new Floorplanner_Floor($api_object);
		$design->_parseXMLAttributes($xml, $relations);
		return $design;
	}
	
}


?>