<?php
/**
 * AnyAPI Class
 * @Author: Jack Michaels
 * A framework for interaction with external data-sources.
 */

 class anyapi {

 	public $debug = false;
 	private $debugLog = array();
 	private static $defaults = array(
 		'GET_PREFERENCE' => array(
 				'http_get',
 				'curl_init',
 				'file_get_contents',
 				'fopen',
 			),
 		'POST_PREFERENCE' => array(
 				'http_post_data',
 				'curl_init',
 				'file_get_contents',
 			),
 	);
 	public $options = array(
 		'GET_PREFERENCE' => array(
 				'http_get',
 				'curl_init',
 				'file_get_contents',
 				'fopen',
 			),
 		'POST_PREFERENCE' => array(
 				'http_post_data',
 				'curl_init',
 				'file_get_contents',
 			),
 	);
 	protected $type = null;
 	protected $credentials = array();
 	protected $headers = null;
 	protected $rawreturn = null;


 	function __construct( $type , $credentials = array(), $headers = null ) {
 		
 	}

 	static function canRunQueryType($type) {
 		if(isset(self::$defaults[ $type . '_PREFERENCE']) && count(self::$defaults[ $type . '_PREFERENCE']) > 0) {
 			$can_use = false;
 				foreach (self::$defaults[ $type . '_PREFERENCE'] as $function) {
 					if(!$can_use) {
 						if(function_exists($function)) {
 							$can_use = true;
 						}
 					}
 				}
 			if($can_use) {
 				return true;
 			} else {
 				$returnString = "Your server configuration does not have a function compatible with this Query Type" . "\r\n" .
 				"Please consider enabling one of the following functions:"  . "\r\n";
 				foreach (self::$defaults[ $type . '_PREFERENCE'] as $function) {
 					$returnString .= "- " . $function  . "\r\n";
 				}
 				$returnString .= "Once you have enabled the function, please check again.";
 				return $returnString;
 			}
 		} else {
 			return 'AnyAPI does not know how to run query type "' . $type .'".';
 		}
 	}

 	public function debugLog() {
 		return $this->$debugLog;
 	}

 } // End of anyapi Class
?>