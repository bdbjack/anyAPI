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

} // end of anyapiHandlers class
?>