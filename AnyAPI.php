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
 	private $type = null;
 	private $credentials = null;
 	private $headers = null;
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
 				),
 			'DELETE_PREFERENCE' => array(
 					'HttpRequest',
 					'curl_init',
 				),
 			'OPTIONS_PREFERENCE' => array(
 					'HttpRequest',
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
 				),
 			'JSON_PREFERENCE' => array(
 					'HttpRequest',
 					'curl_init',
 					'file_get_contents',
 					'fopen',
 				),
 			'WEBSOCKET_PREFERENCE' => array(),
 			'PDO_PREFERENCE' => array(
 					'PDO'
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
 						if(function_exists($function) || class_exists($function)) {
 							$can_use = true;
 						}
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

 	/**
 	 * Object Methods
 	 */
 	
 	function __construct( $type , $credentials = array(), $headers = array() ) {
 		$this->type = $type;
 		if( isset($credentials) && $credentials !== 'null') {
 			$this->credentials = $credentials;
 		}
 		if( isset($headers) && $headers !== 'null') {
 			$this->headers = $headers;
 		}
 	}

 	public function debug() {
 		$this->debug = true;
 		return $this->debug;
 	}


 	public function returnCredentials() {
 		if($this->debug) {
 			return $this->credentials;
 		}
 	}


 } // End of anyapi Class
?>