<?php
abstract class anyapiParsers extends anyapiHandlers {

	protected function runParser( $dataType , $data = NULL) {
		$this->addDebugMessage('Finding Parser for ' . $dataType);
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
			return json_decode(json_encode($data),true);
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
} // end of anyapiParsers class
?>