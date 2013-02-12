<?php

class Floorplanner_Project extends Floorplanner_Object {
	
	protected $_token;
	
	public function getToken() {
		if (is_null($this->_token)) {
			$response = $this->apiCall('/users/' . $this->getAttribute('user-id') . '/token', 'GET');
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
	
	public function embedScript($div, $token = null, $state = null) {
		if (is_null($state)) $state = 'Floorplanner.STATE_EDIT';
		if (is_null($token)) {
			$javascript  = "var fp = new Floorplanner({project_id:$this->id, state: ${state}, auth_token: '${token}'});\n";
		} else {
			$javascript  = "var fp = new Floorplanner({project_id:$this->id, state: ${state}});\n";
		}
		
		$javascript .= "fp.embed('${div}');\n";
		return $javascript;
	}

    /**
     * resolution[width]    - The width of the image(s) in pixels (or millimeters for PDF)
     * resolution[height]   - The height of the image(s) in pixels (or millimeters for PDF)

     Optional:
     * send_to              - result will be sent to given email address
     * callback             - After all design images have been exported, the callback URL will receive a POST request containing XML result message
     * type                 - MIME type of requested export format
     * paper_scale          - Number between 0.002 and 0.02 (PDF only, see design export)
     * scaling              - if set to 'constant', all designs will be scaled by the same ratio
     * scalebar             - set to 1 or “true” to include a scale bar in the output image
     * black_white          - Boolean value (true/false) of whether the output should be in grayscale
     */
    public function render($width, $height, $send_to="nobody@example.com") {
        $response = $this->apiCall($this->path() . '/render', 'POST',
            array('resolution' => array('width'=>$width, 'height'=>$height),
                  'send_to' => $send_to)
        );
        if (Floorplanner::success($response)) {
            var_dump($response);
        } else {
            throw new Floorplanner_Exception($response);
        }
    }
	
}

?>