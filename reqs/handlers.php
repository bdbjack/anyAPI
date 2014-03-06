<?php
abstract class anyapiHandlers extends anyapiCore {

	/**
	 * file_get_contents handler
	 */
	protected function fileGetContentsHandler() {
		$this->addDebugMessage('file_get_contents handler activated. Running validation.');
		$this->addDebugMessage('Checking that URL is set.');
		if(!isset($this->options['url']) || $this->options['url'] === NULL || strlen($this->options['url']) === 0) {
			return $this->exception('URL not found.');
		}
		switch ($this->queryType) {
			case 'GET':
				$this->addDebugMessage('GET Query detected. Preparing Query.');
				$filename = $this->options['url'];
				if($this->queryDataType !== NULL) {
					$this->addDebugMessage('Query Data Exists - Running Appropriate Parser');
					$parsedData = $this->runParser($this->queryDataType);
					$this->addDebugMessage('Adding Parsed Data to Query');
					$filename .= '?';
					foreach ($parsedData as $key => $value) {
						$filename .= "&$key=$value";
					}
					$this->addDebugMessage('Parsed Data Added');
				}
				$use_include_path = FALSE;
				if(isset($this->options['context']) && is_array($this->options['context'])) {
					if(isset($this->options['context']['params']) && is_array($this->options['context']['params'])) {
						$contextParams = $this->options['context']['params'];
					} else {
						$contextParams = array();
					}
					$context = stream_context_create($this->options['context']['opts'],$contextParams);
				} else {
					$context = NULL;
				}
				$this->addDebugMessage('Running Query');
				$results = file_get_contents($filename , $use_include_path , $context );
				$this->addDebugMessage('Detecting Return Data Type');
				$this->resultType = self::dataType($results);
				$this->addDebugMessage('Return Data Type detected as ' . $this->resultType);
				$this->addDebugMessage('Storing Results');
				$this->resultsRaw = $results;
				break;

			case 'POST':
				$this->addDebugMessage('POST Query detected. Running Specific Validation Rules.');
				$filename = $this->options['url'];
				if($this->queryDataType !== NULL) {
					$this->addDebugMessage('Query Data Exists - Running Appropriate Parser');
					$parsedData = $this->runParser($this->queryDataType);
					$this->addDebugMessage('Adding Parsed Data to Query');
					$parsedData = $this->runParser($this->queryDataType);
				}
				$postdata = http_build_query($parsedData);
				$use_include_path = FALSE;
				$opts = array('http' =>
				    array(
				        'method'  => 'POST',
				        'header'  => 'Content-type: application/x-www-form-urlencoded',
				        'content' => $postdata
				    )
				);
				$context  = stream_context_create($opts);
				$this->addDebugMessage('Running Query');
				$results = file_get_contents($filename , $use_include_path , $context );
				$this->addDebugMessage('Detecting Return Data Type');
				$this->resultType = self::dataType($results);
				$this->addDebugMessage('Return Data Type detected as ' . $this->resultType);
				$this->addDebugMessage('Storing Results');
				$this->resultsRaw = $results;
				break;
			
			default:
				return $this->exception('This function does not know how to run this query type.');
				break;
		}
	}

	/**
	 * cURL Handler
	 */
	protected function curlHandler() {
		$this->addDebugMessage('file_get_contents handler activated. Running validation.');
		$this->addDebugMessage('Checking that URL is set.');
		if(!isset($this->options['url']) || $this->options['url'] === NULL || strlen($this->options['url']) === 0) {
			return $this->exception('URL not found.');
		}
		if($this->queryDataType !== NULL) {
			$query = '';
			$this->addDebugMessage('Query Data Exists - Running Appropriate Parser');
			$parsedData = $this->runParser($this->queryDataType);
			$this->addDebugMessage('Adding Parsed Data to Query');
			foreach ($parsedData as $key => $value) {
				$query .= "&$key=$value";
			}
			$this->addDebugMessage('Parsed Data Added');
		}
		$this->addDebugMessage('Initializing cURL');
		try {
			 $ch = curl_init();
			 if($this->queryType == 'POST') {
             	curl_setopt($ch, CURLOPT_URL, $this->options['url']);
             } else {
             	curl_setopt($ch, CURLOPT_URL, $this->options['url'] . '?' . $query);
             }
             curl_setopt($ch, CURLOPT_FAILONERROR, 1);
             curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
             curl_setopt($ch, CURLOPT_TIMEOUT, $this->queryOptions['queryTimeout']);
             if($this->queryType == 'POST') {
             	curl_setopt($ch, CURLOPT_POST, 1);
             	curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
             }
             $this->addDebugMessage('Retrieving Data');
             $result = curl_exec($ch);
             $error = curl_error($ch);
             if($error) {
             	return $this->exception($error);
             }
             $this->addDebugMessage('Detecting Return Data Type');
			 $this->resultType = self::dataType($result);
			 $this->addDebugMessage('Return Data Type detected as ' . $this->resultType);
			 $this->addDebugMessage('Storing Results');
			 $this->resultsRaw = $result;
		}
		catch (Exception $e) {
			return $this->exception($e->getMessage());
		}
	}

	/**
	 * PDO Handler
	 */
	protected function PDOHandler() {
		$this->addDebugMessage('PDO handler activated. Running validation.');
		$req_keys = array(
			'host',
			'user',
			'password',
			'database',
			'query',
		);
		foreach ($req_keys as $key) {
			if(!isset($this->options[$key]) || is_null($this->options[$key]) || strlen($this->options[$key]) == 0) {
				return $this->exception('Required Option ' . $key . ' is missing or not formatted correctly');
			}
		}
		if(!isset($this->options['port']) || is_null($this->options['port']) || strlen($this->options['port']) == 0) {
			$this->options['port'] = 3306;
		}
		$this->addDebugMessage('Creating Database Object');
		try {
			$db = new PDO("mysql:host=" . $this->options['host'] . ";dbname=" . $this->options['database'] . ";port="  .$this->options['port'] ,$this->options['user'],$this->options['password']);
			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
			$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
		}
		catch (Exception $e) {
			return $this->exception($e->getMessage());
		}
		$this->addDebugMessage('Preparing PDO Statement');
		$statement = $db->prepare($this->options['query']);
		$this->addDebugMessage('Preparing Query');
		$query = $this->runParser( $this->queryDataType , $this->queryData );
		$this->addDebugMessage('Executing Query');
		try {
			$statement->execute($query);
		}
		catch (Exception $e) {
			return $this->exception($e->getMessage());
		}
		$this->addDebugMessage('Returning Data');
		$results = $statement->fetchAll();
		$this->addDebugMessage('Setting Data Type');
		$this->resultType = self::dataType($results);
		$this->addDebugMessage('Posting Data to Object Variable');
		$this->resultsRaw = $results;
	}

	/**
	 * jsonRPC Handler
	 */
	protected function jsonRPCHandler() {
		$this->addDebugMessage('jsonRPC handler activated. Running validation.');
		$req_keys = array(
			'url',
			'function',
		);
		foreach ($req_keys as $key) {
			if(!isset($this->options[$key]) || is_null($this->options[$key]) || strlen($this->options[$key]) == 0) {
				return $this->exception('Required Option ' . $key . ' is missing or not formatted correctly');
			}
		}
		$this->addDebugMessage('Creating jsonRPC Object');
		$client = new jsonRPCClient($this->options['url']);
		$this->addDebugMessage('Setting up the function');
		$function = $this->options['function'];
		$this->addDebugMessage('Function set as ' . $function . '(). Running Function.');
		$this->addDebugMessage('Returning Data');
		$results = $client->$function($this->queryData[0],$this->queryData[1]);
		$this->addDebugMessage('Setting Data Type');
		$this->resultType = self::dataType($results);
		$this->addDebugMessage('Posting Data to Object Variable');
		$this->resultsRaw = $results;
	}

} // end of anyapiHandlers class
?>