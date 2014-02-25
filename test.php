<?php
#error_reporting(E_ALL); ini_set('display_errors', '1');
require_once('./ElephantIO/Client.php');
use ElephantIO\Client as Elephant;
require_once('./AnyAPI.php');

$query = array(
	'api_username' => '',
	'api_password' => '',
	'MODULE' => 'Customer',
	'COMMAND' => 'view',
	//'FILTER[id]' => '10032389'
);

$AnyAPI = new anyapi( 'POST' , array('url' => 'http://bdbservice.com/centipede/apiWrap.php?integration=bdb_eu' ) , $query , 'array' , TRUE );

$AnyAPI->execute();
header('Content-type: text/xml');
#print('<pre>');
print_r($AnyAPI->results('xml'));
#print('</pre>');

#print('<pre>');
#print_r($AnyAPI);
#print('</pre>');

#print('<pre>');
#print_r($AnyAPI->returnDebug());
#print('</pre>');
?>