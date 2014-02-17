<?php
abstract class anyapiParsers extends anyapiHandlers {

	protected function runParser( $dataType , $data = NULL) {
		$this->addDebugMessage('Finding Parser for ' . $dataType);
		$parseFunction = $dataType . 'Parser';
		if(is_null($data)) {
			$data = $this->queryData;
		}
		return $this->$parseFunction($data);
	}

	protected function arrayParser($data) {
		$this->addDebugMessage('Running Array Parser');
		$returnArray = array();
		foreach ($data as $key => $value) {
			$returnArray[$key] = $value;
		}
		return $returnArray;
	}

	protected function objectParser($data) {
		$this->addDebugMessage('Found Object Parser. Running as Array Parser');
		$this->arrayParser( $data );
	}

	protected function jsonParser($data) {
		$this->addDebugMessage('Running JSON Parser.');
		if(self::checkIfJSON($data)) {
			return json_decode($data,true);
		} else {
			return json_encode($data);
		}
	}

	protected function xmlParser($data) {
		$this->addDebugMessage('Running XML Parser.');
		if(self::checkIfXML($data)) {
			return json_decode(json_encode($data),true);
		} else {
			$returnData = new SimpleXMLElement("<?xml version=\"1.0\"?><return></return>");
			$returnedXML = $this->arraytoxml($returnData,$data);
			return $returnedXML->asXML();
		}
	}
} // end of anyapiParsers class
?>