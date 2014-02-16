<?php
/**
 * AnyAPI Class
 * @Author: Jack Michaels
 * A framework for interaction with external data-sources.
 */

 require_once('./reqs/core.php');
 require_once('./reqs/handlers.php');
 require_once('./reqs/parsers.php');

 class anyapi extends anyapiParsers {
 	/**
 	 * Query Settings
 	 */
 	private $queryOptions = array(
 		'queryTimeout' => 15, 		// Amount of time in seconds to allow the query to run before "timing-out"
 		'connectTimeout' => 3,		// Amount of time in seconds to allow to connect to external source before "timing-out"
 		'dnsTimeout' => 1,			// Amount of time in seconds to allow DNS Resolution before "timing-out"
 	);
 	private $options = array();
 	private $queryType = NULL;
 	private $queryData = NULL;
 	private $queryDataType = NULL;
 	private $resultsRaw = NULL;
 	private $resultType = NULL;

 	/**
 	 * Query Methods
 	 */
 	// Object Initialization
 	function __construct( $type , $options = NULL , $data = NULL , $dataType = NULL , $debug = FALSE) {
 			$this->setDebug($debug);
 			$this->addDebugMessage('Started Creation of AnyAPI Object');
 			$this->addDebugMessage('Checking that AnyAPI can run Query Type ' . $type);
 			if(self::canRunQueryType($type) !== FALSE) {
 				$this->addDebugMessage('Setting Query Type to ' . $type);
 				$this->queryType = $type;
 				$this->addDebugMessage('Checking that Query Options are stored in an array');
 				if(is_array($options) || is_object($options)) {
 					$this->addDebugMessage('Adding Query Options');
 					$this->options = $options;
 					if($data !== NULL) {
 						if($dataType === NULL) {
 							$this->addDebugMessage('Checking the Query Data Type');
 							$dataType = self::dataType($data);
 							$this->addDebugMessage('Data Type validating as' . $dataType);
 						}
 						$this->addDebugMessage('Checking that AnyAPI can parse Data Type ' . $dataType);
 						if(self::canParseFormat($dataType)) {
 							$this->addDebugMessage('Data Type set to ' . $dataType);
 							$this->queryDataType = $dataType;
 						} else {
 							$this->addDebugMessage('Unable to parse Data Type ' . $dataType);
 							throw new Exception('AnyAPI Error: Unable to parse Data Type ' . $dataType, 1);
 						}
 					}
 				} else {
 					$this->addDebugMessage('Query Options are an array or an object.');
 					throw new Exception('Query Options are an array or an object.', 1);
 				}
 			} else {
 				$this->addDebugMessage('Cannot Run Query Type ' . $type . '. Returning Error.');
 				throw new Exception('AnyAPI Error: Cannot Run Query Type ' . $type . '.', 1);
 			}
 	}

 	// Update Options
 	function setOptions( $options ) {
 		$this->addDebugMessage('Request to update options initiated. Checking that options are being passed in the correct format.');
 		if(is_array($options) || is_object($options)) {
 			$this->addDebugMessage('Options are being passed in the correct format. Updating Options.');
 			foreach ($options as $key => $value) {
 				$this->options[$key] = $value;
 			}
 			$this->addDebugMessage('Options updated');
 			return $this->options;
 		}
 		else {
 			$this->addDebugMessage('Query Options are an array or an object.');
 			throw new Exception('Query Options are an array or an object.', 1);
 		}
 	}

 	// Run the Query
 	function execute( $params = NULL , $overwrite = FALSE ) {
 		$this->addDebugMessage('Execution requested.');
 		if($overwrite === FALSE) {
 			$this->addDebugMessage('Checking that AnyAPI is not overwriting data.');
 			if($this->resultsRaw !== NULL) {
 				$this->addDebugMessage('Cannot Continue. No Permission to overwrite existing data.');
 				throw new Exception("Cannot Continue. No Permission to overwrite existing data.", 1);
 			}
 		}
 		if($params !== NULL) {
 			$this->addDebugMessage('Checking that params are being passed as an array.');
 			if(!is_array($params)) {
 				$this->addDebugMessage('Cannot Continue. Params are not in a valid format.');
 				throw new Exception("Cannot Continue. Params are not in a valid format.", 1);
 			}
 			$this->addDebugMessage('Adding Params');
 			foreach ($params as $key => $value) {
 				$this->queryOptions[$key] = $value;
 			}
 		}
 		$handler = self::canRunQueryType($this->queryType);
 		$this->addDebugMessage('Running Handler: ' . $handler);
 		$this->$handler();

 	// Alias to Execute
 	function exec( $params = NULL , $overwrite = FALSE ) {
 		$this->execute( $params , $overwrite );
 	}

 	}
 } // End of anyapi Class
?>