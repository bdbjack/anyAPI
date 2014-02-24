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
 	protected $queryOptions = array(
 		'queryTimeout' => 15, 		// Amount of time in seconds to allow the query to run before "timing-out"
 		'connectTimeout' => 3,		// Amount of time in seconds to allow to connect to external source before "timing-out"
 		'dnsTimeout' => 1,			// Amount of time in seconds to allow DNS Resolution before "timing-out"
 	);
 	protected $options = array();
 	protected $queryType = NULL;
 	protected $queryData = NULL;
 	protected $queryDataType = NULL;
 	protected $resultsRaw = NULL;
 	protected $resultType = NULL;

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
 							return $this->exception('Unable to parse Data Type ' . $dataType);
 						}
 						$this->addDebugMessage('Adding Query Data');
 						$this->queryData = $data;
 					}
 				} else {
 					return $this->exception('Query Options are an array or an object.');
 				}
 			} else {
 				return $this->exception('Cannot Run Query Type ' . $type . '. Returning Error.');
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
 			return $this->exception('Query Options are an array or an object.');
 		}
 	}

 	// Run the Query
 	function execute( $params = NULL , $overwrite = FALSE ) {
 		$this->addDebugMessage('Execution requested.');
 		if($overwrite === FALSE) {
 			$this->addDebugMessage('Checking that AnyAPI is not overwriting data.');
 			if($this->resultsRaw !== NULL) {
 				return $this->exception('Cannot Continue. No Permission to overwrite existing data.');
 			}
 		}
 		if($params !== NULL) {
 			$this->addDebugMessage('Checking that params are being passed as an array.');
 			if(!is_array($params)) {
 				return $this->exception('Cannot Continue. Params are not in a valid format.');
 			}
 			$this->addDebugMessage('Adding Params');
 			foreach ($params as $key => $value) {
 				$this->queryOptions[$key] = $value;
 			}
 		}
 		$handler = self::canRunQueryType($this->queryType);
 		$this->addDebugMessage('Running Handler: ' . $handler);
 		$this->$handler();
 	}

 	// Return the Results of the Query
 	function results( $returnType = 'array', $parseAs = NULL) {
 		$this->addDebugMessage('Retrieve Request Initiated.');
 		if(is_null($this->resultsRaw)) {
 			return $this->exception('No Data to Return');
 		}
 		if(!is_null($parseAs)) {
 			if(!self::canParseFormat($parseAs)) {
 				return $this->exception('Cannot Parse Returned Data as ' . $parseAs);
 			}
 		} else {
 			$parseAs = $this->resultType;
 		}
 		if(!self::canParseFormat($returnType)) {
 			return $this->exception('Cannot Return Data as ' . $returnType);
 		}
 		elseif( $returnType == 'raw') {
 			return $this->resultsRaw;
 		}
 		$parsedData = $this->runParser($parseAs,$this->resultsRaw);
 		return $this->runParser($returnType,$parsedData);
 	}
 } // End of anyapi Class
?>