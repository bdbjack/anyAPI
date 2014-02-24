<?php
abstract class anyapiParsers extends anyapiHandlers {

	protected function runParser( $dataType , $data = NULL) {
		$this->addDebugMessage('Finding Parser for ' . $dataType);
		if($dataType == NULL || strlen($dataType) == 0) {
			return $this->exception('No Datatype Found');
		}
		$parseFunction = $dataType . 'Parser';
		if(is_null($data)) {
			$data = $this->queryData;
		}
		return $this->$parseFunction($data, $dataType);
	}

	protected function arrayParser($data,$dataType) {
		$this->addDebugMessage('Running Array Parser');
		$returnArray = array();
		foreach ($data as $key => $value) {
			$returnArray[$key] = $value;
		}
		return $returnArray;
	}

	protected function objectParser($data,$dataType) {
		$this->addDebugMessage('Found Object Parser. Running as Array Parser');
		$this->arrayParser( $data );
	}

	protected function jsonParser($data,$dataType) {
		$this->addDebugMessage('Running JSON Parser.');
		if(self::checkIfJSON($data)) {
			return json_decode($data,true);
		} else {
			return json_encode($data);
		}
	}

	protected function xmlParser($data,$dataType) {
		$this->addDebugMessage('Running XML Parser.');
		if(self::checkIfXML($data)) {
			$xml = new SimpleXMLElement($data);
			return $this->xml2array($xml);
		} else {
			$returnData = new SimpleXMLElement("<?xml version=\"1.0\"?><return></return>");
			$returnedXML = $this->arraytoxml($returnData,$data);
			return $returnedXML->asXML();
		}
	}

	protected function csvParser($data,$dataType) {
		$this->addDebugMessage('Running CSV Parser.');
		if(!is_array($data)) {
			return str_getcsv($data);
		} else {
			$multidimensional = false;
			foreach ($data as $key => $value) {
				if(is_array($value)) {
					$multidimensional = true;
				}
			}
			if($multidimensional) {
				return $this->exception('Cannot parse multi-dimensional array as CSV.');
			} else {
				$output = fopen("php://output",'w');
				fputcsv($output,$data);
				fclose($output);
				return $output;
			}
		}
	}

	protected function stdClassParser($data, $dataType) {
		$this->addDebugMessage('Running stdClass Parser.');
		if(is_object($data)) {
			$this->objectParser($data, $dataType);
		} else {
			return (object) $data;
		}
	}

	protected function base64Parser($data, $dataType) {
		$this->addDebugMessage('Running base64 Parser.');
		if(self::checkIfBase64($data)) {
			return base64_decode($data);
		} else {
			if(is_array($data)) {
				return $this->exception('Cannot convert arrays as Base64.');
			} else {
				return base64_encode($data);
			}
		}
	}

	protected function urlParser($data, $dataType) {
		$this->addDebugMessage('Running URL Parser.');
		if(self::checkIfURL($data)) {
			return parse_url($data);
		} else {
			if(is_array($data)) {
				return $this->arraytourl($data);
			} else {
				return urlencode($data);
			}
		}
	}

	protected function rawParser($data) {
		$this->addDebugMessage('Running raw data Parser.');
		return $data;
	}
} // end of anyapiParsers class
?>