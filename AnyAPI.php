<?php
/**
 * AnyAPI Class
 * @Author: Jack Michaels
 * A framework for interaction with external data-sources.
 */

 class anyapi {

 	/**
 	 * Private Variables
 	 */
 	private $debug = false;
 	private $options = array();
 	private $type = null;
 	private $credentials = null;
 	private $headers = null;
 	private $query = null;
 	private $rawReturn = null;
 	private static $preference = array(
 		'TYPE_PREFERENCES' => array(
 			'GET_PREFERENCE' => array(
 					'http_get',
 					'HttpRequest',
 					'curl_init',
 					'file_get_contents',
 					'fopen',
 				),
 			'POST_PREFERENCE' => array(
 					'http_post_data',
 					'HttpRequest',
 					'curl_init',
 					'file_get_contents',
 				),
 			'PUT_PREFERENCE' => array(
 					'HttpRequest',
 					'curl_init',
 					'stream_context_create',
 				),
 			'DELETE_PREFERENCE' => array(
 					'HttpRequest',
 					'curl_init',
 					'stream_context_create',
 				),
 			'OPTIONS_PREFERENCE' => array(
 					'HttpRequest',
 					'stream_context_create',
 				),
 			'MySQL_PREFERENCE' => array(
 					'PDO',
 					'mysqli_connect',
 					'mysql_connect',
 				),
 			'MySQLi_PREFERENCE' => array(
 					'PDO',
 					'mysqli_connect',
 					'mysql_connect',
 				),
 			'COOKIE_PREFERENCE' => array(
 					'HttpRequest',
 					'curl_init',
 					'stream_context_create',
 				),
 			'JSON_PREFERENCE' => array(
 					'HttpRequest',
 					'curl_init',
 					'file_get_contents',
 					'fopen',
 				),
 			'WEBSOCKET_PREFERENCE' => array(
 					'ElephantIO\Client',
 				),
 			'PDO_PREFERENCE' => array(
 					'PDO'
 				),
 		),
		'RETURN_PREFERENCES' => array(
			'ARRAY_A_PREFERENCE' => array(
					'phpinfo',
				),
			'JSON_A_PREFERENCE' => array(
					'json_encode',
				),
			'XML_PREFERENCE' => array(
					'SimpleXMLElement',
				),
			'CSV_PREFERENCE' => array(
					'fputcsv',
					'phpinfo',
				),
			'HTML_E_PREFERENCE' => array(
					'htmlentities',
				),
			'URL_E_PREFERENCE' => array(
					'urlencode',
				),
			'RAW_PREFERENCE' => array(
					'phpinfo',
				),
		),
 	);

	/**
	 * Public Variables
	 */
	public $debugLog = array();

 	/**
 	 * Static Methods
 	 */
 	public static function canRunQueryType( $type ) {
 		if(isset(self::$preference['TYPE_PREFERENCES'][ $type . '_PREFERENCE']) && count(self::$preference['TYPE_PREFERENCES'][ $type . '_PREFERENCE']) > 0) {
 			$can_use = false;
 				foreach (self::$preference['TYPE_PREFERENCES'][ $type . '_PREFERENCE'] as $function) {
 					if(!$can_use) {
 						$can_use = self::canRunHandler($function);
 					}
 				}
 			if($can_use) {
 				return true;
 			} else {
 				$returnString = "Your server configuration does not have a function/class compatible with query type " . $type . "\r\n" .
 				"Please consider enabling one of the following functions / classes:"  . "\r\n";
 				foreach (self::$preference['TYPE_PREFERENCES'][ $type . '_PREFERENCE'] as $function) {
 					$returnString .= "- " . $function  . "\r\n";
 				}
 				$returnString .= "Once you have enabled the function/class, please check again.";
 				return $returnString;
 			}
 		} else {
 			return 'AnyAPI does not know how to run query type "' . $type .'".';
 		}
 	}

 	private static function canRunHandler($handler) {
 		if(function_exists($handler) || class_exists($handler)) {
 			return true;
 		} else {
 			return false;
 		}
 	}

 	public static function canReturnType( $type ) {
 		if(isset(self::$preference['RETURN_PREFERENCES'][ $type . '_PREFERENCE']) && count(self::$preference['RETURN_PREFERENCES'][ $type . '_PREFERENCE']) > 0) {
 			$can_use = false;
 				foreach (self::$preference['RETURN_PREFERENCES'][ $type . '_PREFERENCE'] as $function) {
 					if(!$can_use) {
 						$can_use = self::canRunHandler($function);
 					}
 				}
 			if($can_use) {
 				return true;
 			} else {
 				$returnString = "Your server configuration does not have a function/class compatible with return type " . $type . "\r\n" .
 				"Please consider enabling one of the following functions / classes:"  . "\r\n";
 				foreach (self::$preference['RETURN_PREFERENCES'][ $type . '_PREFERENCE'] as $function) {
 					$returnString .= "- " . $function  . "\r\n";
 				}
 				$returnString .= "Once you have enabled the function/class, please check again.";
 				return $returnString;
 			}
 		} else {
 			return 'AnyAPI does not know how to run return type "' . $type .'".';
 		}
 	}

 	/**
 	 * Object Methods
 	 */
 	
 	// Set up the Object //
 	function __construct( $type , $credentials = array(), $headers = array() ) {
 		$canRun = self::canRunQueryType($type);
 		if( $canRun === true) {
 			$this->type = $type;
 		} else {
 			throw New Exception($canRun);
 		}
 		if( isset($credentials) && $credentials !== 'null') {
 			$this->credentials = $credentials;
 		}
 		if( isset($headers) && $headers !== 'null') {
 			$this->headers = $headers;
 		}
 	}

 	// Enable Debug //
 	public function debug() {
 		$this->debug = true;
 		array_push($this->debugLog, 'Debug Enabled');
 		return $this->debug;
 	}

 	// Return Credentials if Debug is enabled //
 	public function returnCredentials() {
 		if($this->debug) {
 			return $this->credentials;
 		} else {
 			throw New Exeception('This method can only be run when debugging is enabled.');
 		}
 	}

 	// Prepares the Query //
 	public function prepare($query) {
 		if($this->debug) {
 			array_push($this->debugLog, 'Prepared Query');
 		}
 		// First check what type of data we're dealing with
 		$data = null;
 		if(is_object(json_decode($data))) {
 			$data = json_decode($query,true);
 		}
 		else if(@unserialize($query) !== false) {
 			$data = unserialize($query);
 		} 
 		else if(is_array($query)) {
 			$data = $query;
 		} 
 		else {
 			throw New Exception('AnyAPI was not able to recognize the query type.');
 		}
 		$this->query = $data;
 	}

 	// Execute the query and pull the results back to the rawReturn variable //
 	public function exec() {
 		if($this->debug) {
 			array_push($this->debugLog, 'Execution Called - Looking for Handler');
 		}
 		$handlerFound = false;
 		foreach (self::$preference['TYPE_PREFERENCES'][$this->type . '_PREFERENCE'] as $handler) {
 			if(self::canRunHandler($handler)) {
 				$handlerName = str_replace('\\','',$handler) . '_handler';
 				$this->$handlerName();
 				return;
 			}
 		}
 		if(!$handlerFound) {
 			throw New Exception('AnyAPI Error: The Expected handlers for Query Type ' . $this->type . ' could not be found.');
 		}
 	}

 	// Returns Data in the format requested //
 	public function results($type) {
 		if(self::canReturnType($type)) {
 			if(count(self::$preference['RETURN_PREFERENCES'][ $type . '_PREFERENCE']) == 1) {
 				$handlerName = 'return_' . strtolower($type);
 				if($this->debug) {
					array_push($this->debugLog, 'Return Called - Running Hander');
				}
				return $this->$handlerName();
 			}
 			else {
 				foreach (self::$preference['RETURN_PREFERENCES'][ $type . '_PREFERENCE'] as $handle) {
 					if($handle != 'phpinfo') {
 						$handlerName = 'return_' . strtolower($handle);
 						if($this->debug) {
	 						array_push($this->debugLog, 'Return Called - Running Hander');
	 					}
	 					return $this->$handlerName();
 					}
 					else {
 						$handlerName = 'return_' . strtolower($type);
		 				if($this->debug) {
	 						array_push($this->debugLog, 'Return Called - Running Hander');
	 					}
	 					return $this->$handlerName();
 					}
 				}
 			}
 		}
 		else {
 			throw new Exception('AnyAPI Error: Cannot return this type of data.');
 		}
 	}

 	/**
 	 * Execution Handlers
 	 */
 	private function file_get_contents_handler() {
 		if($this->debug) {
 			array_push($this->debugLog, 'Found file_get_contents_handler(). Searching credentials for URL.');
 		}
 		$url = null;
 		foreach ($this->credentials as $key => $value) {
			if(is_null($url)) {
				if(filter_var($value,FILTER_VALIDATE_URL) !== FALSE) {
					$url = $value;
					if($this->debug) {
						array_push($this->debugLog, 'Found URL ' . $url . '. Awaiting action.');
					}
				}
			}
		}
		if(is_null($url)) {
			throw new Exception('AnyAPI Error: No Valid URL Provided');
		}
		if($this->query !== null) {
			if($this->debug) {
				array_push($this->debugLog, 'Encodeing Query Variables.');
			}
			$urlString = http_build_query($this->query);
			if($this->debug) {
				array_push($this->debugLog, 'URL Variables Encoded: ' . $urlString);
			}
			$full = $url . '?' . $urlString;
			if($this->debug) {
				array_push($this->debugLog, 'Beginning Query to ' . $full);
			}
			$this->rawReturn = file_get_contents($full);
			if($this->debug) {
				array_push($this->debugLog, 'Query Complete');
			}
			return true;
		}
 	}

 	/**
 	 * Return Data Handlers
 	 */
 	private function return_array_a() {
 		return 'Function Called';
 	}

 	private function return_jason_a() {
 		return 'Function Called';
 	}

 	private function return_xml() {
 		return 'Function Called';
 	}

 	private function return_csv() {
 		return 'Function Called';
 	}

 	private function return_fputcsv() {
 		return 'Function Called';
 	}

 	private function return_html_e() {
 		return htmlentities($this->rawReturn, ENT_HTML5, "UTF-8");
 	}

 	private function return_url_e() {
 		return 'Function Called';
 	}

 	private function return_raw() {
 		return $this->rawReturn;
 	}

 } // End of anyapi Class
?>