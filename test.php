<?php
	/**
	 * Example file for AnyAPI
	 * Shows an example of requesting data from a website. In this case we will use sample data.
	 */
	error_reporting(E_ALL); ini_set('display_errors', '1');
	require_once('./ElephantIO/Client.php');
	use ElephantIO\Client as Elephant;
	require_once('./AnyAPI.php');

	$getResponseOptions = array(
		'url' => 'http://api2.getresponse.com',
		'function' => 'get_campaigns',
	);

	$getResponseQuery = array(
		'e26755820e7f8067f7fc6049af4da797',
		array(
			'name' => array ( 'EQUALS' => 'binarystealth_bdb' ),
		),
	);

	$getResponseCampaignSearch = new anyapi('jsonRPC', $getResponseOptions, $getResponseQuery, NULL, true);
	$getResponseCampaignSearch->execute();
	print('<pre>');
	print_r($getResponseCampaignSearch);
	print('</pre>');
?>