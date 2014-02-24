<?php
/**
 * AnyAPI Class
 * @Author: Jack Michaels
 * A framework for interaction with external data-sources.
 */

 abstract class anyapiCore {

 	/**
 	 * Requirements Checking & Debug
 	 */
 	const anyapiVersion = '0.0.2-dev';	// Define AnyAPI Version
 	const minPHPVersion = '5.3';		// Minimum Version of PHP Required
 	protected $debug = false;			// Debug Status	
 	protected $debugLog = array();		// Debug Log

 	// Methods for Requirement Checking //
 	public static function installInfo() {
 		$installInfo = array(
 			'anyapiVersion' => self::anyapiVersion,
 			'phpVersion' => phpversion(),
 			'dataTypes' => array(
 				'string' => self::canParseFormat('string'),
 				'array' => self::canParseFormat('array'),
 				'PDO' => self::canParseFormat('PDO'),
 				'stdClass' => self::canParseFormat('stdClass'),
 				'json' => self::canParseFormat('json'),
 				'base64' => self::canParseFormat('base64'),
 				'xml' => self::canParseFormat('xml'),
 				'url' => self::canParseFormat('url'),
 				'csv' => self::canParseFormat('csv'),
 				'MySQLQuery' => self::canParseFormat('MySQLQuery'),
				'PDOMySQLQuery' => self::canParseFormat('PDOMySQLQuery'),
 			),
 			'queryTypes' => array(
 				'GET' => self::canRunQueryType('GET'),
 				'POST' => self::canRunQueryType('POST'),
 				'MySQL' => self::canRunQueryType('MySQL'),
 				'SocketIO' => self::canRunQueryType('SocketIO'),
 			),
 		);
 		return $installInfo;
 	}

 	public static function checkRequirements() {
 		if( version_compare( phpversion() , self::minPHPVersion , '<' ) ) {
 			return false;
 		} else {
 			return true;
 		}
 	}

 	public static function canRunQueryType($type) {
 		// For each query type, either return the handler used to run the query, or FALSE if the query type cannot be run
 		$return = FALSE;
 		switch ($type) {
 			case 'GET':
 				if(function_exists('http_get')) {
 					$return = 'httpGetHandler';
 				}
 				elseif(class_exists('HttpRequest')) {
 					$return = 'HttpRequestHandler';
 				}
 				elseif(function_exists('curl_init')) {
 					$return = 'curlHandler';
 				}
 				elseif(function_exists('file_get_contents')) {
 					$return = 'fileGetContentsHandler';
 				}
 				elseif(function_exists('fopen')) {
 					$return = 'fopenHandler';
 				}
 				break;
 			
 			case 'POST':
 				if(function_exists('http_build_str')) {
 					if(function_exists('http_post_data')) {
 						$return = 'httpPostDataHandler';
 					}
 				}
 				elseif(function_exists('http_post_fields')) {
 					$return = 'httpPostFieldsHandler';
 				}
 				elseif(class_exists('HttpRequest')) {
 					$return = 'HttpRequestHandler';
 				}
 				elseif(function_exists('curl_init')) {
 					$return = 'curlHandler';
 				}
 				elseif(function_exists('file_get_contents')) {
 					if(function_exists('stream_context_create')) {
 						$return = 'fileGetContentsHandler';
 					}
 				}
 				break;

 			case 'MySQL':
 				if(class_exists('PDO')) {
 					$return = 'PDOHandler';
 				}
 				elseif(class_exists('mysqli')) {
 					$return = 'MySQLiHandler';
 				}
 				elseif(function_exists('mysql_connect')) {
 					$return = 'MySQLHandler';
 				}
 				break;

 			case 'SocketIO':
 				if(class_exists('ElephantIO\Client')) {
 					$return = 'ElephantHandler';
 				}
 				break;

 			default:
 				break;
 		}
 		return $return;
 	}

 	public static function canParseFormat($type) {
 		switch ($type) {
			case 'raw':
 				return true;
 				break;

 			case 'string':
 				return true;
 				break;

			case 'array':
 				return true;
 				break;

 			case 'PDO':
 				if(class_exists('PDO')) {
 					return true;
 				}
 				break;

 			case 'stdClass':
 				if(class_exists('stdClass')) {
 					return true;
 				}
 				break;

 			case 'json':
 				if(function_exists('json_encode') && function_exists('json_decode')) {
 					return true;
 				}
 				break;

 			case 'base64':
 				if(function_exists('base64_encode') && function_exists('base64_decode')) {
 					return true;
 				}
 				break;

 			case 'xml':
 				if(class_exists('SimpleXMLElement') && function_exists('json_encode') && function_exists('json_decode')) {
 					return true;
 				}
 				break;

 			case 'url':
 				if(function_exists('parse_url')) {
 					return true;
 				}
 				break;

 			case 'csv':
 				if(function_exists('str_getcsv')) {
 					return true;
 				}
 				break;

 			case 'MySQLQuery':
 				if(self::canRunQueryType('MySQL') !== FALSE) {
 					return true;
 				}
 				break;

 			case 'PDOMySQLQuery':
 				if(self::canRunQueryType('MySQL') !== FALSE) {
 					return true;
 				}
 				break;

 			default:
 				break;
 		}
 		return false;
 	}

 	// Debug Functions //
 	public function setDebug($status) {
 		if(!is_bool($status)) {
 			throw new Exception('AnyAPI Error: Status must be boolean');
 		} else {
 			$this->debug = $status;
 		}
 	}

 	public function returnDebug() {
 		return $this->debugLog;
 	}

 	public function addDebugMessage($message) {
 		if($this->debug) {
 			array_push($this->debugLog, array(time(),$message));
 		}
 	}

 	public function exception($message) {
 		$this->addDebugMessage($message);
		return "AnyAPI Error: " . $message;
 	}

 	/**
 	 * Data Parsing
 	 */
 	public static function dataType($data) {
 		$phpType = gettype( $data );
 		$anyAPIType = NULL;
 		switch ($phpType) {
 			case 'boolean':
 				$anyAPIType = 'string';
 				break;

 			case 'interger':
 				$anyAPIType = 'string';
 				break;

 			case 'double':
 				$anyAPIType = 'string';
 				break;

 			case 'float':
 				$anyAPIType = 'string';
 				break;

 			case 'string':
 				$anyAPIType = 'string';
 				break;

 			case 'array':
 				$anyAPIType = 'array';
 				break;

 			case 'object':
 				$objType = get_class($data);
 				return $objType;
 				break;

 			case 'resource':
 				$anyAPIType = NULL;
 				break;

 			case 'NULL':
 				$anyAPIType = NULL;
 				break;
 			
 			default:
 				throw new Exception("AnyAPI Error: Unknown Data Type", 1);
 				break;
 		}
 		if($anyAPIType === NULL) {
 			throw new Exception("AnyAPI Error: Unknown Data Type", 1);
 		} else {
 			if($anyAPIType === 'string') {
 				if(self::checkIfJSON($data)) {
 					return 'json';
 				}
 				elseif(self::checkIfBase64($data)) {
 					return 'base64';
 				}
 				elseif(self::checkIfXML($data)) {
 					return 'xml';
 				}
 				elseif(self::checkIfURL($data)) {
 					return 'url';
 				}
 				else {
 					return 'string';
 				}
 			} else {
 				return $anyAPIType;
 			}
 		}
 	}

 	public static function checkIfJSON($string) {
 		if(!function_exists('json_decode')) {
 			throw new Exception("AnyAPI Error: AnyAPI Requires package php5-json in-order to work with JSON strings.", 1);
 		} else {
 			return ((is_string($string) && 
         (is_object(json_decode($string)) || 
         is_array(json_decode($string))))) ? true : false;
 		}
 	}

 	public static function checkIfBase64($string) {
 		if(!function_exists('base64_decode')) {
 			throw new Exception("AnyAPI Error: AnyAPI Requires base64_encode() and base64_decode() functions.", 1);
 		} else {
 			if(base64_decode($string,true)) {
 				return true;
 			} else {
 				return false;
 			}
 		}
 	}

 	public static function checkIfXML($string) {
 		if(!class_exists('SimpleXMLElement')) {
 			throw new Exception("AnyAPI Error: AnyAPI Requires SimpleXMLElement in order to work with XML.", 1);
 		}
 		try {
 			$parsed = @new SimpleXMLElement($string);
 		}
 		catch(Exception $e) {
 			return false;
 		}
 		return true;
 	}

 	public static function checkIfURL($string) {
 		if(!function_exists('parse_url')) {
 			throw new Exception("AnyAPI Error: AnyAPI Requires parse_url() function.", 1);
 		} else {
 			if(is_array($string)) {
 				return false;
 			} else {
 				$parse = parse_url($string);
	 			if(is_array($parse)) {
	 				return true;
	 			}
	 			else {
	 				return false;
	 			}
 			}
 		}
 	}

 	/**
 	 * Helper Functions
 	 */
 	protected function arraytoxml($xmlObj,$data) {
 		foreach ($data as $key => $value) {
 			if(is_array($value)) {
 				if(is_numeric($key)) {
 					$key = 'item_' . $key;
 				}
 				$subnode = $xmlObj->addChild("$key");
 				$this->arraytoxml($subnode,$value);
 			} else {
 				$xmlObj->addChild("$key","$value");
 			}
 		}
 		return $xmlObj;
 	}

 	protected function arraytourl($data, $isSub = false) {
 		$return = '';
		foreach ($data as $key => $value) {
			if($isSub) {
				$return .= "[" . urlencode($key) . "]";
			} else {
				$return .= "&" . $key;
			}
			if(is_array($value)) {
				$return .= $this->arraytourl($value,true);
			} else {
				$return .= "=" . urlencode($value);
			}
		}
		return $return;
 	}

 } // End of anyapiCore Class
?>